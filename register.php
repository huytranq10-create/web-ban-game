<?php
$page_title = "Đăng ký";
include 'includes/header.php';
include 'includes/navbar.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $ten_dangnhap = trim($_POST['ten_dangnhap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    $xac_nhan = $_POST['xac_nhan_mat_khau'] ?? '';

    if ($ho_ten === '' || $ten_dangnhap === '' || $email === '' || $mat_khau === '') {
        $error = 'Vui lòng điền đầy đủ tất cả các trường.';
    } elseif ($mat_khau !== $xac_nhan) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } elseif (strlen($mat_khau) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } else {
        $new_id = dang_ky_nguoidung($ten_dangnhap, $email, $mat_khau, $ho_ten);
        if ($new_id) {
            $success = 'Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.';
        } else {
            $error = 'Tên đăng nhập hoặc email đã được sử dụng.';
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">TẠO TÀI KHOẢN</h1>
        <p class="auth-subtitle">Tham gia cộng đồng game bản quyền giá tốt</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?> <a href="login.php" style="text-decoration: underline; font-weight: bold;">Đăng nhập</a>
            </div>
        <?php endif; ?>

        <form method="POST" id="register-form">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="ho_ten" class="form-control" required value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="ten_dangnhap" class="form-control" required value="<?= htmlspecialchars($_POST['ten_dangnhap'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="mat_khau" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Nhập lại mật khẩu</label>
                <input type="password" name="xac_nhan_mat_khau" class="form-control" required>
            </div>

            <button type="submit" class="btn-buy btn-full">ĐĂNG KÝ TÀI KHOẢN</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-muted);">
            Đã có tài khoản? <a href="login.php" style="color: var(--accent-gold); font-weight: 700;">Đăng nhập</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>