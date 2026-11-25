<?php
$page_title = "Lịch sử đơn hàng";
include 'includes/header.php';
requireLogin();
include 'includes/navbar.php';

$ma_nguoidung = $_SESSION['user']['ma_nguoidung'];
$ds_donhang = lay_don_hang_cua_nguoidung($ma_nguoidung);

$nhan_trang_thai = [
    'cho_xu_ly' => 'Chờ xử lý',
    'da_thanh_toan' => 'Đã thanh toán',
    'da_huy' => 'Đã hủy'
];
?>

<div class="container" style="padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 class="section-title" style="font-size: 26px;">LỊCH SỬ ĐƠN HÀNG</h1>
        <a href="logout.php" class="btn-danger-sm" style="padding: 8px 16px;">Đăng xuất</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Đặt hàng thành công! Cảm ơn bạn đã mua game tại GameStore.</div>
    <?php endif; ?>

    <?php if (!empty($ds_donhang)): ?>
        <?php foreach ($ds_donhang as $dh): 
            $chi_tiet = lay_chi_tiet_don_hang($dh['ma_donhang'], $ma_nguoidung);
            $tt = $dh['trang_thai'];
        ?>
            <div class="auth-card" style="max-width: 100%; margin-bottom: 20px; padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <strong style="color: var(--accent-gold);">Đơn hàng #<?= $dh['ma_donhang'] ?></strong>
                        <span style="color: var(--text-muted); font-size: 13px; margin-left: 12px;">Ngày đặt: <?= $dh['ngay_dat'] ?></span>
                    </div>
                    <span class="status-badge status-<?= $tt ?>"><?= $nhan_trang_thai[$tt] ?? $tt ?></span>
                </div>

                <?php foreach ($chi_tiet as $ct): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 14px;">
                        <div class="cart-game-info">
                            <?php $g_ct = lay_game_theo_id($ct['ma_game']); ?>
                                <img src="assets/img/games/<?= htmlspecialchars($g_ct['hinh_anh'] ?? 'default.jpg') ?>" alt="">
                            <span><?= htmlspecialchars($ct['ten_game']) ?> <strong style="color: var(--text-muted);">x<?= $ct['so_luong'] ?></strong></span>
                        </div>
                        <span><?= formatPrice($ct['thanh_tien']) ?></span>
                    </div>
                <?php endforeach; ?>

                <div style="text-align: right; padding-top: 12px; border-top: 1px solid var(--border-color); margin-top: 10px;">
                    <span style="color: var(--text-muted); font-size: 13px;">Tổng tiền: </span>
                    <strong style="color: var(--accent-gold); font-size: 18px;"><?= formatPrice($dh['tong_tien']) ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: var(--text-muted); text-align: center; padding: 50px 0;">Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>