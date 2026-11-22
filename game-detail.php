<?php
$page_title = "Chi tiết Game";
include 'includes/header.php';
include 'includes/navbar.php';

$ma_game = $_GET['ma_game'] ?? 0;
$game = false;
$ds_danhgia = [];

try {
    $game = lay_game_theo_id($ma_game);
    if ($game) {
        $ds_danhgia = lay_danh_gia_game($ma_game);
    }
} catch (Throwable $e) {
    $game = false;
}
?>

<div class="container">
    <?php if ($game): ?>
        <div class="detail-wrapper">
            <!-- Cột trái: Ảnh poster -->
            <div class="detail-poster">
                <img src="assets/img/games/<?= htmlspecialchars($game['hinh_anh']) ?>" alt="<?= htmlspecialchars($game['ten_game']) ?>">
            </div>

            <!-- Cột phải: Thông tin & Nút mua -->
            <div class="detail-info">
                <span class="tag-item"><?= htmlspecialchars($game['ten_theloai']) ?></span>
                <h1><?= htmlspecialchars($game['ten_game']) ?></h1>

                <div class="detail-meta">
                    <span><i class="fa-solid fa-building"></i> Nhà phát hành: <strong><?= htmlspecialchars($game['nha_phat_hanh'] ?? 'Đang cập nhật') ?></strong></span>
                    <span><i class="fa-regular fa-calendar"></i> Phát hành: <strong><?= htmlspecialchars($game['ngay_phat_hanh'] ?? 'Đang cập nhật') ?></strong></span>
                    <span><i class="fa-solid fa-star" style="color: var(--accent-gold);"></i> Đánh giá: <strong><?= $game['diem_trung_binh'] ?? 'Chưa có' ?></strong> (<?= $game['so_danh_gia'] ?> lượt)</span>
                </div>

                <div class="detail-price-box">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">GIÁ BÁN</div>
                        <div class="detail-price"><?= formatPrice($game['gia']) ?></div>
                    </div>
                    <!-- Nút thêm vào giỏ -->
                    <a href="cart.php?add=<?= $game['ma_game'] ?>" class="btn-buy" id="btn-add-to-cart" data-ma-game="<?= $game['ma_game'] ?>">
                        <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                    </a>
                </div>

                <h3 class="section-title" style="font-size: 16px;">MÔ TẢ TRÒ CHƠI</h3>
                <div class="detail-desc">
                    <?= nl2br(htmlspecialchars($game['mo_ta'] ?? 'Chưa có mô tả cho trò chơi này.')) ?>
                </div>
            </div>
        </div>

        <!-- Phần đánh giá từ người dùng -->
        <div class="review-section">
            <h3 class="section-title" style="margin-bottom: 20px;">ĐÁNH GIÁ TỪ NGƯỜI CHƠI (<?= count($ds_danhgia) ?>)</h3>
            <?php if (!empty($ds_danhgia)): ?>
                <?php foreach ($ds_danhgia as $dg): ?>
                    <div class="review-item">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                            <strong><?= htmlspecialchars($dg['ho_ten']) ?></strong>
                            <span style="color: var(--accent-gold); font-size: 13px;">
                                <?= str_repeat('★', (int)$dg['so_sao']) . str_repeat('☆', 5 - (int)$dg['so_sao']) ?>
                            </span>
                        </div>
                        <p style="color: #d4d4d8; font-size: 14px;"><?= htmlspecialchars($dg['noi_dung']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 14px;">Chưa có đánh giá nào cho tựa game này.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 80px 0;">
            <h2>Không tìm thấy thông tin game!</h2>
            <a href="games.php" class="btn-hero" style="margin-top: 20px;">QUAY LẠI DANH SÁCH</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>