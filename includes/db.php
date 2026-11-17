<?php
// =====================================================================
// includes/db.php : kết nối MySQL (PDO) và các hàm truy vấn
// Người phụ trách: Huy
//
// CÁCH DÙNG (dành cho Hiếu và Dũng):
//   require_once __DIR__ . '/db.php';        // trong thư mục includes/
//   require_once '../includes/db.php';        // trong thư mục api/ hoặc admin/
//   $danh_sach = lay_danh_sach_game();
//
// QUY ƯỚC:
//   - Mọi truy vấn dùng prepared statement (tham số ?), không nối chuỗi SQL.
//   - Hàm "lấy" trả về mảng (rỗng nếu không có) hoặc false nếu không tìm thấy 1 dòng.
//   - Hàm "thêm / sửa / xóa" trả về true/false hoặc id mới tạo.
//   - Lỗi hệ thống (mất kết nối, sai SQL) sẽ ném PDOException.
// =====================================================================
//
// DANH SÁCH HÀM
//   ket_noi_db()
//   --- Game ---
//   lay_danh_sach_theloai()
//   lay_danh_sach_game($ma_theloai = null, $tu_khoa = null)
//   lay_game_theo_id($ma_game)
//   them_game($ten, $gia, $mo_ta, $hinh_anh, $nha_phat_hanh, $ngay_phat_hanh, $ma_theloai)
//   sua_game($ma_game, $ten, $gia, $mo_ta, $hinh_anh, $nha_phat_hanh, $ngay_phat_hanh, $ma_theloai, $dang_ban)
//   xoa_game($ma_game)                 // ẩn game (dang_ban = 0), không xóa hẳn
//   --- Người dùng ---
//   dang_ky_nguoidung($ten_dangnhap, $email, $mat_khau, $ho_ten)
//   dang_nhap($ten_hoac_email, $mat_khau)
//   lay_nguoidung_theo_id($ma_nguoidung)
//   lay_danh_sach_nguoidung()
//   --- Giỏ hàng ---
//   lay_gio_hang($ma_nguoidung)
//   them_vao_gio_hang($ma_nguoidung, $ma_game, $so_luong = 1)
//   cap_nhat_so_luong_gio_hang($ma_nguoidung, $ma_game, $so_luong)
//   xoa_khoi_gio_hang($ma_nguoidung, $ma_game)
//   dem_san_pham_gio_hang($ma_nguoidung)
//   --- Đơn hàng ---
//   tao_don_hang($ma_nguoidung, $ghi_chu = '')
//   lay_don_hang_cua_nguoidung($ma_nguoidung)
//   lay_chi_tiet_don_hang($ma_donhang, $ma_nguoidung = null)
//   lay_tat_ca_don_hang()
//   cap_nhat_trang_thai_don_hang($ma_donhang, $trang_thai)
//   --- Đánh giá ---
//   lay_danh_gia_game($ma_game)
//   luu_danh_gia($ma_nguoidung, $ma_game, $so_sao, $noi_dung)
// =====================================================================

require_once __DIR__ . '/../config/db.php';

// ---------------------------------------------------------------------
// Kết nối MySQL bằng PDO. Dùng lại cùng một kết nối cho cả lần chạy.
// ---------------------------------------------------------------------
function ket_noi_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT
             . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// =====================================================================
// GAME
// =====================================================================

/** Trả về mảng các thể loại: [ma_theloai, ten_theloai] */
function lay_danh_sach_theloai(): array
{
    $stmt = ket_noi_db()->query('select ma_theloai, ten_theloai from theloai order by ten_theloai');
    return $stmt->fetchAll();
}

/**
 * Lấy danh sách game đang bán, có thể lọc theo thể loại và/hoặc tìm theo tên.
 * Trả về mảng: ma_game, ten_game, gia, mo_ta, hinh_anh, nha_phat_hanh,
 *              ngay_phat_hanh, ma_theloai, ten_theloai, diem_trung_binh, so_danh_gia
 */
function lay_danh_sach_game($ma_theloai = null, $tu_khoa = null): array
{
    $sql = 'select g.ma_game, g.ten_game, g.gia, g.mo_ta, g.hinh_anh, g.nha_phat_hanh,
                   g.ngay_phat_hanh, g.ma_theloai, t.ten_theloai,
                   (select round(avg(d.so_sao), 1) from danhgia d where d.ma_game = g.ma_game) as diem_trung_binh,
                   (select count(*) from danhgia d where d.ma_game = g.ma_game) as so_danh_gia
            from game g, theloai t
            where g.ma_theloai = t.ma_theloai
              and g.dang_ban = 1';
    $tham_so = [];

    if ($ma_theloai !== null && $ma_theloai !== '') {
        $sql .= ' and g.ma_theloai = ?';
        $tham_so[] = (int)$ma_theloai;
    }
    if ($tu_khoa !== null && trim($tu_khoa) !== '') {
        $sql .= ' and g.ten_game like ?';
        $tham_so[] = '%' . trim($tu_khoa) . '%';
    }
    $sql .= ' order by g.ngay_them desc, g.ma_game desc';

    $stmt = ket_noi_db()->prepare($sql);
    $stmt->execute($tham_so);
    return $stmt->fetchAll();
}

/** Lấy 1 game theo mã. Trả về mảng hoặc false nếu không có / đã ẩn. */
function lay_game_theo_id($ma_game)
{
    $stmt = ket_noi_db()->prepare(
        'select g.ma_game, g.ten_game, g.gia, g.mo_ta, g.hinh_anh, g.nha_phat_hanh,
                g.ngay_phat_hanh, g.ma_theloai, t.ten_theloai,
                (select round(avg(d.so_sao), 1) from danhgia d where d.ma_game = g.ma_game) as diem_trung_binh,
                (select count(*) from danhgia d where d.ma_game = g.ma_game) as so_danh_gia
         from game g, theloai t
         where g.ma_theloai = t.ma_theloai
           and g.ma_game = ?
           and g.dang_ban = 1'
    );
    $stmt->execute([(int)$ma_game]);
    return $stmt->fetch();
}

/** Thêm game mới (dùng cho trang quản trị). Trả về ma_game vừa tạo. */
function them_game($ten, $gia, $mo_ta, $hinh_anh, $nha_phat_hanh, $ngay_phat_hanh, $ma_theloai): int
{
    $pdo = ket_noi_db();
    $stmt = $pdo->prepare(
        'insert into game (ten_game, gia, mo_ta, hinh_anh, nha_phat_hanh, ngay_phat_hanh, ma_theloai)
         values (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $ten, $gia, $mo_ta, $hinh_anh, $nha_phat_hanh,
        ($ngay_phat_hanh === '' ? null : $ngay_phat_hanh), (int)$ma_theloai,
    ]);
    return (int)$pdo->lastInsertId();
}

/** Sửa thông tin game (trang quản trị). Trả về true nếu câu lệnh chạy thành công. */
function sua_game($ma_game, $ten, $gia, $mo_ta, $hinh_anh, $nha_phat_hanh, $ngay_phat_hanh, $ma_theloai, $dang_ban = 1): bool
{
    $stmt = ket_noi_db()->prepare(
        'update game
         set ten_game = ?, gia = ?, mo_ta = ?, hinh_anh = ?, nha_phat_hanh = ?,
             ngay_phat_hanh = ?, ma_theloai = ?, dang_ban = ?
         where ma_game = ?'
    );
    return $stmt->execute([
        $ten, $gia, $mo_ta, $hinh_anh, $nha_phat_hanh,
        ($ngay_phat_hanh === '' ? null : $ngay_phat_hanh), (int)$ma_theloai,
        (int)$dang_ban, (int)$ma_game,
    ]);
}

/**
 * "Xóa" game: chỉ ẩn khỏi cửa hàng (dang_ban = 0).
 * Không xóa hẳn vì game có thể đã nằm trong đơn hàng cũ.
 */
function xoa_game($ma_game): bool
{
    $stmt = ket_noi_db()->prepare('update game set dang_ban = 0 where ma_game = ?');
    return $stmt->execute([(int)$ma_game]);
}

// =====================================================================
// NGƯỜI DÙNG
// =====================================================================

/**
 * Đăng ký tài khoản khách. Mật khẩu được băm ở đây, truyền mật khẩu gốc vào.
 * Trả về ma_nguoidung mới, hoặc false nếu tên đăng nhập / email đã tồn tại.
 */
function dang_ky_nguoidung($ten_dangnhap, $email, $mat_khau, $ho_ten)
{
    $pdo = ket_noi_db();

    $kiem_tra = $pdo->prepare('select count(*) from nguoidung where ten_dangnhap = ? or email = ?');
    $kiem_tra->execute([$ten_dangnhap, $email]);
    if ((int)$kiem_tra->fetchColumn() > 0) {
        return false;
    }

    $stmt = $pdo->prepare(
        'insert into nguoidung (ten_dangnhap, email, mat_khau, ho_ten) values (?, ?, ?, ?)'
    );
    $stmt->execute([$ten_dangnhap, $email, password_hash($mat_khau, PASSWORD_DEFAULT), $ho_ten]);
    return (int)$pdo->lastInsertId();
}

/**
 * Đăng nhập bằng tên đăng nhập hoặc email.
 * Trả về mảng thông tin người dùng (KHÔNG có mật khẩu) hoặc false nếu sai.
 * Việc lưu vào $_SESSION do trang PHP / api xử lý.
 */
function dang_nhap($ten_hoac_email, $mat_khau)
{
    $stmt = ket_noi_db()->prepare(
        'select ma_nguoidung, ten_dangnhap, email, mat_khau, ho_ten, vai_tro
         from nguoidung
         where ten_dangnhap = ? or email = ?
         limit 1'
    );
    $stmt->execute([$ten_hoac_email, $ten_hoac_email]);
    $nguoi_dung = $stmt->fetch();

    if (!$nguoi_dung || !password_verify($mat_khau, $nguoi_dung['mat_khau'])) {
        return false;
    }
    unset($nguoi_dung['mat_khau']);
    return $nguoi_dung;
}

/** Lấy thông tin 1 người dùng (không có mật khẩu). Trả về mảng hoặc false. */
function lay_nguoidung_theo_id($ma_nguoidung)
{
    $stmt = ket_noi_db()->prepare(
        'select ma_nguoidung, ten_dangnhap, email, ho_ten, vai_tro, ngay_tao
         from nguoidung where ma_nguoidung = ?'
    );
    $stmt->execute([(int)$ma_nguoidung]);
    return $stmt->fetch();
}

/** Danh sách toàn bộ người dùng (trang quản trị). */
function lay_danh_sach_nguoidung(): array
{
    $stmt = ket_noi_db()->query(
        'select ma_nguoidung, ten_dangnhap, email, ho_ten, vai_tro, ngay_tao
         from nguoidung order by ma_nguoidung'
    );
    return $stmt->fetchAll();
}

// =====================================================================
// GIỎ HÀNG
// =====================================================================

/**
 * Lấy giỏ hàng của người dùng.
 * Mỗi dòng: ma_game, ten_game, gia, hinh_anh, so_luong, thanh_tien
 */
function lay_gio_hang($ma_nguoidung): array
{
    $stmt = ket_noi_db()->prepare(
        'select g.ma_game, g.ten_game, g.gia, g.hinh_anh, h.so_luong,
                (g.gia * h.so_luong) as thanh_tien
         from giohang h, game g
         where h.ma_game = g.ma_game
           and h.ma_nguoidung = ?
           and g.dang_ban = 1
         order by h.ngay_them'
    );
    $stmt->execute([(int)$ma_nguoidung]);
    return $stmt->fetchAll();
}

/** Thêm game vào giỏ. Nếu đã có thì cộng dồn số lượng. */
function them_vao_gio_hang($ma_nguoidung, $ma_game, $so_luong = 1): bool
{
    $so_luong = max(1, (int)$so_luong);
    $stmt = ket_noi_db()->prepare(
        'insert into giohang (ma_nguoidung, ma_game, so_luong) values (?, ?, ?)
         on duplicate key update so_luong = so_luong + values(so_luong)'
    );
    return $stmt->execute([(int)$ma_nguoidung, (int)$ma_game, $so_luong]);
}

/** Đặt lại số lượng của 1 game trong giỏ. Nếu so_luong <= 0 thì xóa khỏi giỏ. */
function cap_nhat_so_luong_gio_hang($ma_nguoidung, $ma_game, $so_luong): bool
{
    if ((int)$so_luong <= 0) {
        return xoa_khoi_gio_hang($ma_nguoidung, $ma_game);
    }
    $stmt = ket_noi_db()->prepare(
        'update giohang set so_luong = ? where ma_nguoidung = ? and ma_game = ?'
    );
    return $stmt->execute([(int)$so_luong, (int)$ma_nguoidung, (int)$ma_game]);
}

/** Xóa 1 game khỏi giỏ. */
function xoa_khoi_gio_hang($ma_nguoidung, $ma_game): bool
{
    $stmt = ket_noi_db()->prepare('delete from giohang where ma_nguoidung = ? and ma_game = ?');
    return $stmt->execute([(int)$ma_nguoidung, (int)$ma_game]);
}

/** Tổng số sản phẩm trong giỏ (dùng hiển thị số trên biểu tượng giỏ hàng). */
function dem_san_pham_gio_hang($ma_nguoidung): int
{
    $stmt = ket_noi_db()->prepare(
        'select coalesce(sum(so_luong), 0) from giohang where ma_nguoidung = ?'
    );
    $stmt->execute([(int)$ma_nguoidung]);
    return (int)$stmt->fetchColumn();
}

// =====================================================================
// ĐƠN HÀNG
// =====================================================================

/**
 * Tạo đơn hàng từ giỏ hàng hiện tại rồi xóa giỏ.
 * Toàn bộ chạy trong một transaction: lỗi ở đâu thì hủy hết, không để dữ liệu dở dang.
 * Trả về ma_donhang mới, hoặc false nếu giỏ hàng trống.
 */
function tao_don_hang($ma_nguoidung, $ghi_chu = '')
{
    $pdo = ket_noi_db();
    $pdo->beginTransaction();
    try {
        // Lấy giỏ hàng cùng giá hiện tại của từng game
        $stmt = $pdo->prepare(
            'select h.ma_game, h.so_luong, g.gia
             from giohang h, game g
             where h.ma_game = g.ma_game
               and h.ma_nguoidung = ?
               and g.dang_ban = 1'
        );
        $stmt->execute([(int)$ma_nguoidung]);
        $dong_gio_hang = $stmt->fetchAll();

        if (count($dong_gio_hang) === 0) {
            $pdo->rollBack();
            return false;
        }

        $tong_tien = 0;
        foreach ($dong_gio_hang as $dong) {
            $tong_tien += $dong['gia'] * $dong['so_luong'];
        }

        $them_don = $pdo->prepare(
            'insert into donhang (ma_nguoidung, tong_tien, ghi_chu) values (?, ?, ?)'
        );
        $them_don->execute([(int)$ma_nguoidung, $tong_tien, $ghi_chu]);
        $ma_donhang = (int)$pdo->lastInsertId();

        $them_chi_tiet = $pdo->prepare(
            'insert into chitietdonhang (ma_donhang, ma_game, so_luong, don_gia) values (?, ?, ?, ?)'
        );
        foreach ($dong_gio_hang as $dong) {
            $them_chi_tiet->execute([$ma_donhang, $dong['ma_game'], $dong['so_luong'], $dong['gia']]);
        }

        $xoa_gio = $pdo->prepare('delete from giohang where ma_nguoidung = ?');
        $xoa_gio->execute([(int)$ma_nguoidung]);

        $pdo->commit();
        return $ma_donhang;
    } catch (Throwable $loi) {
        $pdo->rollBack();
        throw $loi;
    }
}

/** Lịch sử đơn hàng của 1 người dùng, mới nhất trước. */
function lay_don_hang_cua_nguoidung($ma_nguoidung): array
{
    $stmt = ket_noi_db()->prepare(
        'select ma_donhang, ngay_dat, tong_tien, trang_thai, ghi_chu
         from donhang
         where ma_nguoidung = ?
         order by ngay_dat desc, ma_donhang desc'
    );
    $stmt->execute([(int)$ma_nguoidung]);
    return $stmt->fetchAll();
}

/**
 * Chi tiết 1 đơn hàng: các dòng game trong đơn.
 * Truyền $ma_nguoidung để chắc chắn đơn đó thuộc về người đang đăng nhập
 * (người dùng không xem được đơn của người khác). Để null khi admin xem.
 * Mỗi dòng: ma_game, ten_game, hinh_anh, so_luong, don_gia, thanh_tien
 */
function lay_chi_tiet_don_hang($ma_donhang, $ma_nguoidung = null): array
{
    $sql = 'select c.ma_game, g.ten_game, g.hinh_anh, c.so_luong, c.don_gia,
                   (c.so_luong * c.don_gia) as thanh_tien
            from chitietdonhang c, game g, donhang d
            where c.ma_game = g.ma_game
              and c.ma_donhang = d.ma_donhang
              and c.ma_donhang = ?';
    $tham_so = [(int)$ma_donhang];

    if ($ma_nguoidung !== null) {
        $sql .= ' and d.ma_nguoidung = ?';
        $tham_so[] = (int)$ma_nguoidung;
    }

    $stmt = ket_noi_db()->prepare($sql);
    $stmt->execute($tham_so);
    return $stmt->fetchAll();
}

/** Tất cả đơn hàng kèm tên người đặt (trang quản trị). */
function lay_tat_ca_don_hang(): array
{
    $stmt = ket_noi_db()->query(
        'select d.ma_donhang, d.ngay_dat, d.tong_tien, d.trang_thai, d.ghi_chu,
                n.ma_nguoidung, n.ten_dangnhap, n.ho_ten
         from donhang d, nguoidung n
         where d.ma_nguoidung = n.ma_nguoidung
         order by d.ngay_dat desc, d.ma_donhang desc'
    );
    return $stmt->fetchAll();
}

/** Đổi trạng thái đơn: 'cho_xu_ly', 'da_thanh_toan' hoặc 'da_huy'. */
function cap_nhat_trang_thai_don_hang($ma_donhang, $trang_thai): bool
{
    $hop_le = ['cho_xu_ly', 'da_thanh_toan', 'da_huy'];
    if (!in_array($trang_thai, $hop_le, true)) {
        return false;
    }
    $stmt = ket_noi_db()->prepare('update donhang set trang_thai = ? where ma_donhang = ?');
    return $stmt->execute([$trang_thai, (int)$ma_donhang]);
}

// =====================================================================
// ĐÁNH GIÁ
// =====================================================================

/** Danh sách đánh giá của 1 game: ho_ten, so_sao, noi_dung, ngay_danh_gia. */
function lay_danh_gia_game($ma_game): array
{
    $stmt = ket_noi_db()->prepare(
        'select n.ho_ten, d.so_sao, d.noi_dung, d.ngay_danh_gia
         from danhgia d, nguoidung n
         where d.ma_nguoidung = n.ma_nguoidung
           and d.ma_game = ?
         order by d.ngay_danh_gia desc'
    );
    $stmt->execute([(int)$ma_game]);
    return $stmt->fetchAll();
}

/** Thêm đánh giá, hoặc cập nhật nếu người này đã đánh giá game đó rồi. so_sao từ 1 đến 5. */
function luu_danh_gia($ma_nguoidung, $ma_game, $so_sao, $noi_dung): bool
{
    $so_sao = (int)$so_sao;
    if ($so_sao < 1 || $so_sao > 5) {
        return false;
    }
    $stmt = ket_noi_db()->prepare(
        'insert into danhgia (ma_nguoidung, ma_game, so_sao, noi_dung) values (?, ?, ?, ?)
         on duplicate key update so_sao = values(so_sao), noi_dung = values(noi_dung),
                                 ngay_danh_gia = current_timestamp'
    );
    return $stmt->execute([(int)$ma_nguoidung, (int)$ma_game, $so_sao, $noi_dung]);
}
