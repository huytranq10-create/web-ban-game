# Web Bán Game

Đồ án môn **Lập trình ứng dụng web** – Trường Đại học Văn Lang.

Website bán game được xây dựng bằng **HTML, CSS, JavaScript, PHP và MySQL**. Người dùng có thể xem danh sách game, tìm kiếm và lọc theo thể loại, thêm game vào giỏ hàng, đặt hàng và xem lịch sử đơn hàng. Quản trị viên có thể quản lý game, đơn hàng và người dùng.

---

## 1. Thành viên và phân công

| Thành viên | Vai trò | Phụ trách chính |
|---|---|---|
| **Hiếu** | PHP (chính) + HTML + CSS | Các trang PHP ở thư mục gốc và `admin/`, `includes/` (header, navbar, footer, `functions.php`), session và phân quyền, thiết kế giao diện, responsive, `assets/css/`, `assets/img/` |
| **Huy** | MySQL + PHP phục vụ MySQL | Thiết kế database (`database/`), `config/db.sample.php`, `includes/db.php` (kết nối PDO và các hàm truy vấn MySQL) |
| **Dũng** | JavaScript + PHP phục vụ JS | Toàn bộ mã JS (`assets/js/`) và các endpoint PHP trả JSON trong `api/` mà JS gọi tới |

**Quy tắc:** ai phụ trách thư mục nào thì người đó chỉnh sửa. Muốn thay đổi thư mục của người khác thì nhắn trong nhóm trước.

---

## 2. Công nghệ sử dụng

- **Giao diện:** HTML5, CSS3
- **Tương tác phía trình duyệt:** JavaScript (dùng `fetch` để gọi API)
- **Máy chủ:** PHP (kết nối database bằng PDO)
- **Cơ sở dữ liệu:** MySQL
- **Môi trường chạy:** XAMPP
- **Quản lý mã nguồn:** Git và GitHub

> **Lưu ý:** GitHub (và GitHub Pages) chỉ lưu mã nguồn và chạy được web tĩnh, không chạy được PHP/MySQL. Muốn chạy dự án thì dùng XAMPP trên máy hoặc đưa lên hosting có hỗ trợ PHP/MySQL.

---

## 3. Cấu trúc thư mục

```
web-ban-game/
│
├── index.php                  # Trang chủ
├── games.php                  # Danh sách game, lọc, tìm kiếm
├── game-detail.php            # Chi tiết một game
├── cart.php                   # Giỏ hàng
├── checkout.php               # Thanh toán / đặt hàng
├── login.php                  # Đăng nhập
├── register.php               # Đăng ký
├── orders.php                 # Lịch sử đơn hàng của người dùng
│
├── includes/                  # Phần dùng chung của các trang PHP
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   ├── functions.php
│   └── db.php                 # Kết nối MySQL (PDO) và các hàm truy vấn
│
├── admin/                     # Trang quản trị
│   ├── index.php
│   ├── games.php
│   ├── orders.php
│   └── users.php
│
├── api/                       # Lớp trung gian JS <-> MySQL, trả JSON
│   ├── games.php
│   ├── cart.php
│   ├── orders.php
│   └── auth.php
│
├── database/
│   ├── schema.sql             # Tạo database và các bảng
│   └── seed.sql               # Dữ liệu mẫu
│
├── config/
│   ├── db.sample.php          # File mẫu (được commit)
│   └── db.php                 # Thông tin kết nối thật (KHÔNG commit)
│
├── assets/
│   ├── js/
│   │   ├── api.js             # Hàm fetch dùng chung
│   │   ├── games.js
│   │   ├── cart.js
│   │   ├── auth.js
│   │   └── main.js
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   └── img/
│       ├── games/
│       └── banners/
│
├── .gitignore
└── README.md
```

| Thư mục / file | Người phụ trách |
|---|---|
| Các trang `*.php` ở thư mục gốc, `admin/`, `includes/header.php`, `navbar.php`, `footer.php`, `functions.php` | Hiếu |
| `assets/css/`, `assets/img/` | Hiếu |
| `database/`, `config/db.sample.php`, `includes/db.php` | Huy |
| `api/`, `assets/js/` | Dũng |

---

## 4. Cách hoạt động

JavaScript chạy trên trình duyệt nên **không kết nối trực tiếp** với MySQL. Luồng dữ liệu như sau:

```
Trình duyệt (JS, Dũng) --fetch--> api/*.php (Dũng) --gọi hàm--> includes/db.php (Huy) --PDO--> MySQL
```

- Các trang PHP ở thư mục gốc và `admin/` (Hiếu) dựng khung trang và dùng HTML/CSS do Hiếu thiết kế. Khi cần dữ liệu, các trang này gọi các hàm trong `includes/db.php` của Huy.
- JS của Dũng gọi các endpoint trong `api/` để lấy hoặc gửi dữ liệu, rồi cập nhật giao diện mà không cần tải lại trang. Mỗi endpoint trong `api/` nhận yêu cầu, gọi hàm tương ứng trong `includes/db.php` và trả JSON.
- **Huy** đảm bảo mọi truy vấn nằm trong `includes/db.php` và dùng **prepared statement** (dấu `?`) để tránh SQL injection. Mật khẩu lưu bằng `password_hash()`.
- Danh sách các hàm trong `includes/db.php` (tên hàm, tham số, giá trị trả về) cần được ghi trong README hoặc ở đầu file để Hiếu và Dũng biết cách gọi.

---

## 5. Cơ sở dữ liệu

Các bảng dự kiến (có thể điều chỉnh, mọi thay đổi phải cập nhật trong `database/schema.sql`):

| Bảng | Mô tả |
|---|---|
| `nguoidung` | Tài khoản người dùng và quản trị viên |
| `theloai` | Thể loại game |
| `game` | Thông tin game (tên, giá, mô tả, hình ảnh, thể loại) |
| `giohang` | Giỏ hàng của từng người dùng |
| `donhang` | Đơn hàng |
| `chitietdonhang` | Các game trong từng đơn hàng |
| `danhgia` | Đánh giá game của người dùng (tính năng mở rộng) |

---

## 6. Danh sách API (hợp đồng giữa JS và PHP)

Tất cả API trả về JSON. Bảng dưới là bản dự kiến, nếu thay đổi thì Dũng cập nhật tại đây và báo cho cả nhóm. Dữ liệu trong các endpoint lấy từ các hàm của `includes/db.php` (Huy).

| Endpoint | Phương thức | Tham số | Kết quả |
|---|---|---|---|
| `api/games.php` | GET | `ma_theloai` (tùy chọn), `tukhoa` (tùy chọn) | Mảng các game: `ma_game`, `ten_game`, `gia`, `hinh_anh`, `ten_theloai` |
| `api/games.php` | GET | `ma_game` | Thông tin chi tiết một game |
| `api/cart.php` | GET | | Danh sách game trong giỏ hàng của người dùng đang đăng nhập |
| `api/cart.php` | POST | `ma_game`, `so_luong` | Thêm game vào giỏ hàng |
| `api/cart.php` | PUT | `ma_game`, `so_luong` | Cập nhật số lượng |
| `api/cart.php` | DELETE | `ma_game` | Xóa game khỏi giỏ hàng |
| `api/orders.php` | POST | Thông tin đặt hàng | Tạo đơn hàng từ giỏ hàng |
| `api/orders.php` | GET | | Lịch sử đơn hàng của người dùng |
| `api/auth.php` | POST | `hanhdong` (`dangnhap` / `dangky` / `dangxuat`), thông tin tài khoản | Kết quả đăng nhập, đăng ký, đăng xuất |

---

## 7. Hướng dẫn cài đặt

1. Cài **XAMPP** và bật **Apache** và **MySQL**.
2. Clone dự án vào thư mục `htdocs` của XAMPP:
   ```bash
   cd C:/xampp/htdocs
   git clone <đường-dẫn-repo> web-ban-game
   ```
3. Tạo file cấu hình kết nối: copy `config/db.sample.php` thành `config/db.php`, rồi sửa thông tin (tên database, user, mật khẩu) cho đúng với máy của bạn.
4. Mở **phpMyAdmin** (`http://localhost/phpmyadmin`), import lần lượt:
   - `database/schema.sql`
   - `database/seed.sql`
5. Mở trình duyệt và truy cập: `http://localhost/web-ban-game`

---

## 8. Quy tắc làm việc với Git

- Nhánh `main` chỉ chứa bản chạy được. **Không push trực tiếp lên `main`.**
- Mỗi người làm trên nhánh riêng, đặt tên theo dạng `feature/<nội-dung>`, ví dụ:
  - `feature/giao-dien-trang-chu` (Hiếu)
  - `feature/database-schema` (Huy)
  - `feature/api-gio-hang` (Dũng)
- Làm xong thì tạo **Pull Request** vào `main`, nhóm trưởng xem và merge.
- Viết nội dung commit rõ ràng, ví dụ: `Thêm API lấy danh sách game`.
- Trước khi bắt đầu làm việc mỗi ngày, chạy `git pull` để cập nhật mã mới nhất.
- **Không commit** `config/db.php`. File `.gitignore` tối thiểu:
  ```
  config/db.php
  .vscode/
  .idea/
  ```
- Mọi thay đổi database phải được sửa trong `database/schema.sql` và `database/seed.sql` rồi commit, **không** chỉ sửa trực tiếp trong phpMyAdmin.

---

## 9. Quy ước đặt tên

- Tên thư mục và file: chữ thường, nối bằng dấu gạch ngang, không dấu, không khoảng trắng (ví dụ `game-detail.php`).
- Tên bảng và cột trong database: chữ thường, không dấu, nối bằng dấu gạch dưới (ví dụ `ma_game`, `ten_game`).
- Từ khóa SQL viết chữ thường.

---

## 10. Tiến độ

- [ ] Chốt danh sách bảng và cột, viết `schema.sql`, `seed.sql`
- [ ] Giao diện các trang chính (trang chủ, danh sách game, chi tiết game)
- [ ] `config/db.php` và `api/games.php`
- [ ] Hiển thị danh sách game bằng JS
- [ ] Đăng ký và đăng nhập
- [ ] Giỏ hàng
- [ ] Đặt hàng và lịch sử đơn hàng
- [ ] Trang quản trị
- [ ] Tìm kiếm, lọc theo thể loại
- [ ] Kiểm thử và hoàn thiện
