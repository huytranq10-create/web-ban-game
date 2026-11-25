<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Lấy danh sách thể loại từ DB của Huy để render dropdown động
$ds_theloai_nav = [];
try {
    $ds_theloai_nav = lay_danh_sach_theloai();
} catch (Throwable $e) {
    $ds_theloai_nav = [];
}

// Đếm số lượng giỏ hàng nếu đã đăng nhập
$cart_count = 0;
if (isLoggedIn()) {
    try {
        $cart_count = dem_san_pham_gio_hang($_SESSION['user']['ma_nguoidung']);
    } catch (Throwable $e) {
        $cart_count = 0;
    }
}
?>
<header class="navbar">
    <div class="container nav-container">
        <!-- Logo -->
        <a href="index.php" class="logo">
            <span class="logo-highlight">GAME</span>STORE
        </a>

        <!-- Menu điều hướng -->
        <nav class="nav-links">
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">HOME</a>

            <!-- Dropdown GAMES -->
            <div class="nav-dropdown">
                <a href="games.php" class="dropdown-toggle <?= $current_page == 'games.php' && !isset($_GET['ma_theloai']) ? 'active' : '' ?>">
                    GAMES
                </a>
                <div class="dropdown-menu">
                    <a href="games.php" class="dropdown-item">
                        <i class="fa-solid fa-gamepad"></i>
                        <span>TẤT CẢ GAME</span>
                    </a>
                    <a href="games.php?loai=offline" class="dropdown-item">
                        <i class="fa-solid fa-eye-slash"></i>
                        <span>TÀI KHOẢN OFFLINE</span>
                    </a>
                    <a href="games.php?loai=full" class="dropdown-item">
                        <i class="fa-solid fa-user-check"></i>
                        <span>TÀI KHOẢN FULL THÔNG TIN</span>
                    </a>
                    <a href="games.php?loai=steam-key" class="dropdown-item">
                        <i class="fa-solid fa-key"></i>
                        <span>STEAM KEY CHÍNH HÃNG</span>
                    </a>
                    <a href="games.php?loai=xbox" class="dropdown-item">
                        <i class="fa-brands fa-xbox"></i>
                        <span>TÀI KHOẢN XBOX</span>
                    </a>
                </div>
            </div>

            <!-- Dropdown THỂ LOẠI (Lấy tự động từ DB) -->
            <div class="nav-dropdown">
                <a href="games.php" class="dropdown-toggle <?= isset($_GET['ma_theloai']) ? 'active' : '' ?>">
                    THỂ LOẠI
                </a>
                <div class="dropdown-menu dropdown-grid">
                    <?php if (!empty($ds_theloai_nav)): ?>
                        <?php foreach ($ds_theloai_nav as $tl): ?>
                            <a href="games.php?ma_theloai=<?= $tl['ma_theloai'] ?>" class="dropdown-item">
                                <i class="fa-solid fa-layer-group"></i>
                                <span><?= htmlspecialchars(mb_strtoupper($tl['ten_theloai'], 'UTF-8')) ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="games.php" class="dropdown-item"><span>Chưa có thể loại</span></a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isLoggedIn()): ?>
                <a href="orders.php" class="<?= $current_page == 'orders.php' ? 'active' : '' ?>">ĐƠN HÀNG</a>
            <?php endif; ?>
        </nav>

        <!-- Tìm kiếm, Giỏ hàng, Tài khoản -->
        <div class="nav-actions">
            <form action="games.php" method="GET" class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="tukhoa" placeholder="Tìm kiếm game..." value="<?= htmlspecialchars($_GET['tukhoa'] ?? '') ?>">
            </form>

            <a href="cart.php" class="icon-btn" title="Giỏ hàng">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="badge" id="cart-count"><?= $cart_count ?></span>
            </a>

<?php if (isLoggedIn()): ?>
                <a href="orders.php" class="icon-btn" title="Xin chào, <?= htmlspecialchars($_SESSION['user']['ho_ten']) ?>">
                    <i class="fa-regular fa-user"></i>
                </a>
                <?php if (isAdmin()): ?>
                    <a href="admin/index.php" class="tag-item">ADMIN</a>
                <?php endif; ?>
                <!-- THÊM DÒNG NÀY VÀO ĐÂY -->
                <a href="logout.php" class="icon-btn" title="Đăng xuất">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="icon-btn" title="Đăng nhập">
                    <i class="fa-regular fa-user"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>