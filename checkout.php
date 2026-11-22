<?php
$page_title = "Thanh toán";
include 'includes/header.php';
requireLogin();

$ma_nguoidung = $_SESSION['user']['ma_nguoidung'];
$gio_hang = lay_gio_hang($ma_nguoidung);

if (empty($gio_hang)) {
    header('Location: cart.php');
    exit;
}

$tong_tien = 0;
foreach ($gio_hang as $item) {
    $tong_tien += $item['thanh_tien'];
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ghi_chu = trim($_POST['ghi_chu'] ?? '');
    $ma_donhang = tao_don_hang($ma_nguoidung, $ghi_chu);
    if ($ma_donhang) {
        header('Location: orders.php?success=1');
        exit;
    } else {
        $error = 'Có lỗi xảy ra khi tạo đơn hàng, vui lòng thử lại.';
    }
}

include 'includes/navbar.php';
?>

<div class="container" style="padding: 40px 20px; max-width: 800px;">
    <h1 class="section-title" style="font-size: 26px; margin-bottom: 24px;">XÁC NHẬN ĐẶT HÀNG</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="auth-card" style="max-width: 100%; margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px; color: var(--accent-gold);">1. Tóm tắt đơn hàng</h3>
        <?php foreach ($gio_hang as $item): ?>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); font-size: 14px;">
                <span><?= htmlspecialchars($item['ten_game']) ?> <strong style="color: var(--text-muted);">x<?= $item['so_luong'] ?></strong></span>
                <strong><?= formatPrice($item['thanh_tien']) ?></strong>
            </div>
        <?php endforeach; ?>
        <div style="display: flex; justify-content: space-between; padding-top: 16px; font-size: 18px;">
            <strong>Tổng cộng:</strong>
            <strong style="color: var(--accent-gold);"><?= formatPrice($tong_tien) ?></strong>
        </div>
    </div>

    <div class="auth-card" style="max-width: 100%;">
        <h3 style="margin-bottom: 16px; color: var(--accent-gold);">2. Thông tin nhận key / tài khoản</h3>
        <form method="POST" id="checkout-form">
            <div class="form-group">
                <label>Người nhận</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['ho_ten']) ?> (<?= htmlspecialchars($_SESSION['user']['email']) ?>)" disabled>
            </div>
            <div class="form-group">
                <label>Ghi chú đơn hàng (tùy chọn)</label>
                <textarea name="ghi_chu" rows="3" class="form-control" placeholder="VD: Gửi key qua email giúp mình nhé..."></textarea>
            </div>
            <button type="submit" class="btn-buy btn-full">HOÀN TẤT ĐẶT HÀNG (<?= formatPrice($tong_tien) ?>)</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>