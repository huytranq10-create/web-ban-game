<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
requireAdmin();

$ds_game = lay_danh_sach_game();
$ds_don = lay_tat_ca_don_hang();
$ds_user = lay_danh_sach_nguoidung();

$doanh_thu = 0;
foreach ($ds_don as $don) {
    if ($don['trang_thai'] === 'da_thanh_toan') {
        $doanh_thu += $don['tong_tien'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị - GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-logo"><span>GAME</span>ADMIN</div>
        <nav class="sidebar-menu">
            <a href="index.php" class="sidebar-link active"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
            <a href="games.php" class="sidebar-link"><i class="fa-solid fa-gamepad"></i> Quản lý Game</a>
            <a href="orders.php" class="sidebar-link"><i class="fa-solid fa-receipt"></i> Quản lý Đơn hàng</a>
            <a href="users.php" class="sidebar-link"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a>
            <a href="../index.php" class="sidebar-link" style="margin-top: auto;"><i class="fa-solid fa-arrow-left"></i> Về trang bán hàng</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">TỔNG QUAN HỆ THỐNG</h1>
            <span style="color: var(--text-muted); font-size: 14px;">Xin chào, <?= htmlspecialchars($_SESSION['user']['ho_ten']) ?></span>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Tổng số Game đang bán</div>
                <div class="stat-value"><?= count($ds_game) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tổng số Đơn hàng</div>
                <div class="stat-value"><?= count($ds_don) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tổng Người dùng</div>
                <div class="stat-value"><?= count($ds_user) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Doanh thu (Đã thanh toán)</div>
                <div class="stat-value" style="font-size: 22px;"><?= formatPrice($doanh_thu) ?></div>
            </div>
        </div>
    </main>
</div>
</body>
</html>