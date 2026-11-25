<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
requireAdmin();

$ds_user = lay_danh_sach_nguoidung();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Người dùng - GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-logo"><span>GAME</span>ADMIN</div>
        <nav class="sidebar-menu">
            <a href="index.php" class="sidebar-link"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
            <a href="games.php" class="sidebar-link"><i class="fa-solid fa-gamepad"></i> Quản lý Game</a>
            <a href="orders.php" class="sidebar-link"><i class="fa-solid fa-receipt"></i> Quản lý Đơn hàng</a>
            <a href="users.php" class="sidebar-link active"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a>
            <a href="../index.php" class="sidebar-link" style="margin-top: auto;"><i class="fa-solid fa-arrow-left"></i> Về trang bán hàng</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">QUẢN LÝ NGƯỜI DÙNG</h1>
        </div>

        <div class="panel">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ds_user as $u): ?>
                        <tr>
                            <td>#<?= $u['ma_nguoidung'] ?></td>
                            <td><strong><?= htmlspecialchars($u['ho_ten']) ?></strong></td>
                            <td><?= htmlspecialchars($u['ten_dangnhap']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span style="color: <?= $u['vai_tro'] === 'admin' ? 'var(--accent-gold)' : 'var(--text-muted)' ?>; font-weight: 700;">
                                    <?= strtoupper($u['vai_tro']) ?>
                                </span>
                            </td>
                            <td><?= $u['ngay_tao'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>