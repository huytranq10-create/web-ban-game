# GameStore – Web bán game

Đồ án môn **Lập trình ứng dụng web** – Trường Đại học Văn Lang.

Website bán game bản quyền viết bằng **PHP + MySQL**. Khách hàng xem và tìm game, thêm vào giỏ, đặt hàng và xem lịch sử đơn. Quản trị viên quản lý game, đơn hàng và người dùng.

---

## 1. Tổng quan cách hoạt động

Đây là website **render phía máy chủ (server-side rendering)**. Mỗi lần người dùng bấm link hoặc gửi form, trình duyệt gửi một request tới một file `.php`. PHP truy vấn MySQL, dựng ra HTML hoàn chỉnh rồi trả về cho trình duyệt.

```
Trình duyệt ──(link / form GET, POST)──► trang .php ──gọi hàm──► includes/db.php ──PDO──► MySQL
      ▲                                       │
      └──────────── HTML đã dựng sẵn ◄────────┘
```

Một trang PHP thường làm theo thứ tự sau:

1. `include 'includes/header.php'` – nạp hàm dùng chung, kết nối DB, bật session, in phần `<head>`.
2. Kiểm tra quyền (`requireLogin()` hoặc `requireAdmin()`) nếu trang yêu cầu đăng nhập.
3. Nếu là request POST thì xử lý form, gọi hàm trong `db.php`, rồi `header('Location: ...')` để chuyển trang (mô hình *Post/Redirect/Get*, tránh gửi lại form khi tải lại trang).
4. `include 'includes/navbar.php'` – in thanh điều hướng (thể loại lấy từ DB, số lượng trong giỏ).
5. In phần nội dung HTML bằng các vòng lặp `foreach` trên dữ liệu vừa lấy từ DB.
6. `include 'includes/footer.php'` – in chân trang và đóng `</body>`.

---

## 2. Công nghệ

| Thành phần | Công nghệ |
|---|---|
| Giao diện | HTML5, CSS3 (`assets/css/style.css` cho khách, `assets/css/admin.css` cho quản trị), icon Font Awesome 6 qua CDN |
| Máy chủ | PHP, dùng session để lưu trạng thái đăng nhập |
| Cơ sở dữ liệu | MySQL, truy cập bằng PDO |
| Môi trường chạy | XAMPP (Apache + MySQL) |

---

## 3. Cấu trúc thư mục

```
web-ban-game/
├── index.php            Trang chủ: banner và các game nổi bật
├── games.php            Danh sách game, lọc theo thể loại, tìm kiếm theo tên
├── game-detail.php      Chi tiết một game và các đánh giá
├── cart.php             Giỏ hàng
├── checkout.php         Xác nhận đặt hàng
├── orders.php           Lịch sử đơn hàng của người dùng
├── login.php            Đăng nhập
├── register.php         Đăng ký
├── logout.php           Đăng xuất
│
├── includes/            Phần dùng chung
│   ├── header.php       Mở đầu HTML, nạp functions và db, bật session
│   ├── navbar.php       Thanh điều hướng, dropdown thể loại, đếm giỏ hàng
│   ├── footer.php       Chân trang
│   ├── functions.php    Session, kiểm tra quyền, định dạng giá
│   └── db.php           Kết nối PDO và toàn bộ hàm truy vấn MySQL
│
├── admin/               Trang quản trị (chỉ vai trò admin)
│   ├── index.php        Tổng quan: số game, đơn, người dùng, doanh thu
│   ├── games.php        Thêm, sửa, ẩn game, upload ảnh
│   ├── orders.php       Xem đơn và đổi trạng thái
│   └── users.php        Danh sách người dùng
│
├── database/
│   ├── schema.sql       Tạo database và các bảng
│   └── seed.sql         Dữ liệu mẫu và tài khoản mẫu
├── config/
│   ├── db.sample.php    Mẫu cấu hình kết nối
└── assets/
    ├── css/             style.css, admin.css
    └── img/games/       Ảnh bìa game
```

---

## 4. Cơ sở dữ liệu

Database `web_ban_game` (utf8mb4) gồm 7 bảng:

| Bảng | Nội dung | Ghi chú |
|---|---|---|
| `nguoidung` | Tài khoản | `vai_tro` là `khach` hoặc `admin`. `mat_khau` lưu bằng `password_hash()` |
| `theloai` | Thể loại game | Dùng cho dropdown và bộ lọc |
| `game` | Sản phẩm | `dang_ban = 1` là đang bán, `0` là đã ẩn. `hinh_anh` là tên file trong `assets/img/games/` |
| `giohang` | Giỏ hàng | Khóa chính `(ma_nguoidung, ma_game)`: mỗi game chỉ một dòng, có `so_luong` |
| `donhang` | Đơn hàng | `trang_thai`: `cho_xu_ly`, `da_thanh_toan`, `da_huy` |
| `chitietdonhang` | Game trong từng đơn | `don_gia` lưu **giá lúc mua**, nên đổi giá game sau này không ảnh hưởng đơn cũ |
| `danhgia` | Đánh giá 1–5 sao | Mỗi người chỉ đánh giá một game một lần |

Quan hệ chính: `game → theloai`, `giohang → nguoidung + game`, `donhang → nguoidung`, `chitietdonhang → donhang + game`, `danhgia → nguoidung + game`. Khi xóa người dùng hoặc đơn hàng thì giỏ hàng, chi tiết đơn và đánh giá liên quan bị xóa theo (`on delete cascade`).

---

## 5. Lớp truy cập dữ liệu (`includes/db.php`)

Mọi truy vấn MySQL nằm trong file này. Các trang PHP chỉ gọi hàm, không tự viết SQL.

- `ket_noi_db()` tạo một kết nối PDO duy nhất cho cả lần chạy (biến `static`). PDO bật chế độ ném exception và tắt giả lập prepared statement.
- Mọi truy vấn dùng **prepared statement** (tham số `?`), tránh SQL injection.

| Nhóm | Hàm |
|---|---|
| Game | `lay_danh_sach_theloai`, `lay_danh_sach_game($ma_theloai, $tu_khoa)`, `lay_game_theo_id`, `them_game`, `sua_game`, `xoa_game` |
| Người dùng | `dang_ky_nguoidung`, `dang_nhap`, `lay_nguoidung_theo_id`, `lay_danh_sach_nguoidung` |
| Giỏ hàng | `lay_gio_hang`, `them_vao_gio_hang`, `cap_nhat_so_luong_gio_hang`, `xoa_khoi_gio_hang`, `dem_san_pham_gio_hang` |
| Đơn hàng | `tao_don_hang`, `lay_don_hang_cua_nguoidung`, `lay_chi_tiet_don_hang`, `lay_tat_ca_don_hang`, `cap_nhat_trang_thai_don_hang` |
| Đánh giá | `lay_danh_gia_game`, `luu_danh_gia` |



---

## 6. Các luồng chức năng

### 6.1 Đăng ký, đăng nhập, đăng xuất

- **Đăng ký** (`register.php`): kiểm tra đủ trường, mật khẩu xác nhận khớp, mật khẩu ít nhất 6 ký tự. Sau đó `dang_ky_nguoidung()` kiểm tra trùng tên đăng nhập hoặc email rồi lưu mật khẩu đã băm. Tài khoản mới luôn có vai trò `khach`.
- **Đăng nhập** (`login.php`): nhập tên đăng nhập **hoặc** email. `dang_nhap()` so sánh bằng `password_verify()`. Đúng thì lưu thông tin người dùng (không gồm mật khẩu) vào `$_SESSION['user']`. Admin được chuyển tới `admin/index.php`, khách tới `index.php`.
- **Đăng xuất** (`logout.php`): xóa session và về trang chủ.

### 6.2 Phân quyền

`includes/functions.php` có các hàm kiểm tra:

- `isLoggedIn()`: có `$_SESSION['user']` hay không.
- `isAdmin()`: `vai_tro` trong session là `admin`.
- `requireLogin()`: chưa đăng nhập thì chuyển về `login.php`.
- `requireAdmin()`: không phải admin thì chuyển về `login.php`. Mọi trang trong `admin/` đều gọi hàm này ở đầu file.

### 6.3 Xem và tìm game

- `index.php`: lấy danh sách game đang bán và hiển thị tối đa 6 game đầu ở mục nổi bật. Nếu chưa có `config/db.php` hoặc chưa import database, trang dùng dữ liệu mẫu có sẵn trong code để vẫn xem được giao diện.
- `games.php`: đọc `?ma_theloai=` (lọc thể loại) và `?tukhoa=` (tìm theo tên, `like %từ khóa%`). Hai điều kiện dùng chung được. Hàng nút lọc thể loại được dựng từ bảng `theloai`.
- `game-detail.php?ma_game=`: hiển thị thông tin chi tiết, điểm trung bình, danh sách đánh giá và nút "Thêm vào giỏ hàng". Game không tồn tại hoặc đã ẩn thì hiện thông báo không tìm thấy.
- Ô tìm kiếm trên thanh điều hướng là một form `GET` gửi tới `games.php`.

### 6.4 Giỏ hàng

Giỏ hàng lưu **trong MySQL** theo từng người dùng (bảng `giohang`), không lưu trong session, nên bắt buộc đăng nhập.

- Nút "Thêm vào giỏ" là link `cart.php?add=<ma_game>`: thêm một sản phẩm rồi chuyển về `cart.php`.
- Trong `cart.php`, form POST với `action=update` đổi số lượng và `action=delete` xóa dòng.
- Tổng tiền được cộng từ `thanh_tien = gia * so_luong` do câu SQL tính sẵn.
- Số trên biểu tượng giỏ hàng ở thanh điều hướng lấy từ `dem_san_pham_gio_hang()`.

### 6.5 Đặt hàng

1. `checkout.php` (cần đăng nhập) hiển thị giỏ hàng và ô ghi chú. Giỏ trống thì chuyển về `cart.php`.
2. Khi gửi form, `tao_don_hang()` chạy trong **một transaction**:
   - lấy giỏ hàng cùng giá hiện tại của từng game,
   - tính tổng tiền và tạo dòng trong `donhang` (trạng thái mặc định `cho_xu_ly`),
   - ghi từng dòng vào `chitietdonhang` với `don_gia` là giá lúc đó,
   - xóa giỏ hàng,
   - `commit`. Nếu lỗi ở bước nào thì `rollBack` toàn bộ.
3. Thành công thì chuyển tới `orders.php?success=1`.

Trang này chưa có cổng thanh toán thật. Đơn được tạo ở trạng thái "Chờ xử lý" và admin đổi trạng thái thủ công.

### 6.6 Lịch sử đơn hàng

`orders.php` (cần đăng nhập) liệt kê đơn của chính người đang đăng nhập, mới nhất trước, với trạng thái hiển thị bằng nhãn tiếng Việt (Chờ xử lý, Đã thanh toán, Đã hủy). `lay_chi_tiet_don_hang()` nhận thêm `ma_nguoidung` để người dùng không xem được đơn của người khác.

### 6.7 Trang quản trị (`admin/`)

| Trang | Chức năng |
|---|---|
| `index.php` | Thống kê: số game đang bán, số đơn, số người dùng, doanh thu (tổng tiền các đơn `da_thanh_toan`) |
| `games.php` | Form thêm game, sửa game (`?edit=<id>`), bảng danh sách, nút "Xóa" (thực chất là ẩn). Có upload ảnh, file được lưu vào `assets/img/games/` với tên `<timestamp>_<tên gốc>` |
| `orders.php` | Danh sách mọi đơn kèm người đặt, form đổi trạng thái (chỉ nhận 3 giá trị hợp lệ) |
| `users.php` | Danh sách người dùng |

---

## 7. Bảo mật đã áp dụng

- Prepared statement cho mọi truy vấn.
- Mật khẩu băm bằng `password_hash()` và kiểm tra bằng `password_verify()`.
- Dữ liệu in ra HTML đều qua `htmlspecialchars()` để chống XSS.
- Kiểm tra quyền ở đầu mỗi trang cần bảo vệ; đơn hàng được lọc theo chủ sở hữu.


