<?php
$page_title = "Đăng nhập";
include 'includes/header.php';


if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_hoac_email = trim($_POST['ten_hoac_email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';

    if ($ten_hoac_email === '' || $mat_khau === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin đăng nhập.';
    } else {
        $user = dang_nhap($ten_hoac_email, $mat_khau);
        if ($user) {
            $_SESSION['user'] = $user;
            if (($user['vai_tro'] ?? '') === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Tên đăng nhập/email hoặc mật khẩu không chính xác.';
        }
    }
}
include 'includes/navbar.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">ĐĂNG NHẬP</h1>
        <p class="auth-subtitle">Chào mừng quay trở lại với GameStore</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="login-form">
            <div class="form-group">
                <label>Tên đăng nhập hoặc Email</label>
                <input type="text" name="ten_hoac_email" class="form-control" required
                       value="<?= htmlspecialchars($_POST['ten_hoac_email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="mat_khau" class="form-control" required>
            </div>

            <button type="submit" class="btn-buy btn-full">ĐĂNG NHẬP NGAY</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-muted);">
            Chưa có tài khoản? <a href="register.php" style="color: var(--accent-gold); font-weight: 700;">Đăng ký ngay</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>