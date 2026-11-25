<?php
$page_title = "Trang chủ";
include 'includes/header.php';
include 'includes/navbar.php';

// Gọi hàm lấy game từ includes/db.php của Huy
$ds_game = [];
try {
    $ds_game = lay_danh_sach_game();
} catch (Throwable $e) {
    // Nếu chưa tạo config/db.php hoặc chưa import database thì dùng tạm mảng mẫu này để xem giao diện
    $ds_game = [
        ['ma_game' => 1, 'ten_game' => 'EA SPORTS FC™ 27', 'gia' => 89000, 'hinh_anh' => 'assets/img/games/fc27.jpg', 'ten_theloai' => 'Thể thao', 'nha_phat_hanh' => 'EA Sports', 'diem_trung_binh' => 4.8],
        ['ma_game' => 2, 'ten_game' => 'Forza Horizon 6', 'gia' => 49000, 'hinh_anh' => 'assets/img/games/forza6.jpg', 'ten_theloai' => 'Đua tốc độ', 'nha_phat_hanh' => 'Xbox Game Studios', 'diem_trung_binh' => 4.9],
        ['ma_game' => 3, 'ten_game' => 'Resident Evil Requiem', 'gia' => 49000, 'hinh_anh' => 'assets/img/games/re9.jpg', 'ten_theloai' => 'Kinh dị', 'nha_phat_hanh' => 'Capcom', 'diem_trung_binh' => 4.7],
        ['ma_game' => 4, 'ten_game' => 'Phantom Blade Zero', 'gia' => 49000, 'hinh_anh' => 'assets/img/games/phantom.jpg', 'ten_theloai' => 'Nhập vai (RPG)', 'nha_phat_hanh' => 'S-GAME', 'diem_trung_binh' => 4.9],
        ['ma_game' => 5, 'ten_game' => 'Marvel\'s Spider-Man 2', 'gia' => 49000, 'hinh_anh' => 'assets/img/games/spiderman2.jpg', 'ten_theloai' => 'Hành động', 'nha_phat_hanh' => 'Sony', 'diem_trung_binh' => 4.8],
        ['ma_game' => 6, 'ten_game' => 'Onimusha: Way of the Sword', 'gia' => 49000, 'hinh_anh' => 'assets/img/games/onimusha.jpg', 'ten_theloai' => 'Phiêu lưu', 'nha_phat_hanh' => 'Capcom', 'diem_trung_binh' => 4.6],
    ];
}

// Lấy tối đa 6 game đầu tiên cho mục nổi bật
$noi_bat = array_slice($ds_game, 0, 6);
?>

<!-- HERO BANNER -->
<section class="hero-banner">
    <div class="container">
        <p class="hero-subtitle">CHIẾN GAME BẢN QUYỀN - DEAL CỰC CHÁY</p>
        <h1 class="hero-title">GAMESTORE</h1>
        <a href="games.php" class="btn-hero">KHÁM PHÁ NGAY</a>
    </div>
</section>

<!-- SECTION: SẢN PHẨM NỔI BẬT -->
<section class="product-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">SẢN PHẨM BÁN CHẠY</h2>
            <a href="games.php" class="view-all">Xem tất cả <i class="fa-solid fa-chevron-right"></i></a>
        </div>

        <div class="game-grid" id="featured-games">
            <?php foreach ($noi_bat as $game): ?>
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
    </div>
</section>

<?php include 'includes/footer.php'; ?>