<?php
$page_title = "Danh sách Game";
include 'includes/header.php';
include 'includes/navbar.php';

$ma_theloai = $_GET['ma_theloai'] ?? null;
$tu_khoa = $_GET['tukhoa'] ?? null;

$ds_theloai = [];
$ds_game = [];
try {
    $ds_theloai = lay_danh_sach_theloai();
    $ds_game = lay_danh_sach_game($ma_theloai, $tu_khoa);
} catch (Throwable $e) {
    $ds_game = [];
}
?>

<div class="container">
    <div class="page-header">
        <h1 class="section-title" style="font-size: 26px;">
            <?php if (!empty($tu_khoa)): ?>
                KẾT QUẢ TÌM KIẾM: "<?= htmlspecialchars($tu_khoa) ?>"
            <?php else: ?>
                KHO GAME BẢN QUYỀN
            <?php endif; ?>
        </h1>

        <!-- Thanh nút bấm lọc nhanh theo thể loại -->
        <div class="filter-bar" id="genre-filters">
            <a href="games.php" class="filter-pill <?= empty($ma_theloai) ? 'active' : '' ?>">Tất cả</a>
            <?php foreach ($ds_theloai as $tl): ?>
                <a href="games.php?ma_theloai=<?= $tl['ma_theloai'] ?>"
                   class="filter-pill <?= ($ma_theloai == $tl['ma_theloai']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($tl['ten_theloai']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Vùng chứa danh sách game (Dũng có thể dùng id="game-list" để render JS nếu cần) -->
    <section style="padding-bottom: 40px;">
        <?php if (!empty($ds_game)): ?>
            <div class="game-grid" id="game-list">
                <?php foreach ($ds_game as $game): ?>
                    <a href="game-detail.php?ma_game=<?= $game['ma_game'] ?>" class="game-card">
                        <div class="card-poster">
                            <img src="assets/img/games/<?= htmlspecialchars($game['hinh_anh']) ?>" alt="<?= htmlspecialchars($game['ten_game']) ?>">
                            <span class="hot-badge"><i class="fa-solid fa-star"></i></span>
                        </div>

                        <span class="card-type"><?= htmlspecialchars($game['nha_phat_hanh'] ?? 'GAME BẢN QUYỀN') ?></span>
                        <h3 class="card-title"><?= htmlspecialchars($game['ten_game']) ?></h3>

                        <div class="card-tags">
                            <span class="tag-item"><?= htmlspecialchars($game['ten_theloai']) ?></span>
                            <?php if (!empty($game['diem_trung_binh'])): ?>
                                <span class="tag-item"><i class="fa-solid fa-star"></i> <?= $game['diem_trung_binh'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="card-price-row">
                            <span class="price-current"><?= formatPrice($game['gia']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); text-align: center; padding: 60px 0;">
                Không tìm thấy tựa game nào phù hợp.
            </p>
        <?php endif; ?>
    </section>
</div>

<?php include 'includes/footer.php'; ?>