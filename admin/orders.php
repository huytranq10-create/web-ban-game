<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
requireAdmin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ma_donhang'], $_POST['trang_thai'])) {
    if (cap_nhat_trang_thai_don_hang((int)$_POST['ma_donhang'], $_POST['trang_thai'])) {
        $msg = 'Đã cập nhật trạng thái đơn hàng #' . (int)$_POST['ma_donhang'];
    }
}

$ds_don = lay_tat_ca_don_hang();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng - GameStore</title>
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
            <a href="orders.php" class="sidebar-link active"><i class="fa-solid fa-receipt"></i> Quản lý Đơn hàng</a>
            <a href="users.php" class="sidebar-link"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a>
            <a href="../index.php" class="sidebar-link" style="margin-top: auto;"><i class="fa-solid fa-arrow-left"></i> Về trang bán hàng</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">QUẢN LÝ ĐƠN HÀNG</h1>
        </div>

        <?php if ($msg): ?>
            <div class="alert"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="panel">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Ghi chú</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ds_don as $don): ?>
                        <tr>
                            <td><strong>#<?= $don['ma_donhang'] ?></strong></td>
                            <td><?= htmlspecialchars($don['ho_ten']) ?> (<?= htmlspecialchars($don['ten_dangnhap']) ?>)</td>
                            <td><?= $don['ngay_dat'] ?></td>
                            <td style="color: var(--accent-gold); font-weight: 700;"><?= formatPrice($don['tong_tien']) ?></td>
                            <td><?= htmlspecialchars($don['ghi_chu'] ?? '') ?></td>
                            <td>
                                <form method="POST" style="display: flex; gap: 6px;">
                                    <input type="hidden" name="ma_donhang" value="<?= $don['ma_donhang'] ?>">
                                    <select name="trang_thai" class="form-control" style="padding: 6px; font-size: 13px;">
                                        <option value="cho_xu_ly" <?= $don['trang_thai'] === 'cho_xu_ly' ? 'selected' : '' ?>>Chờ xử lý</option>
                                        <option value="da_thanh_toan" <?= $don['trang_thai'] === 'da_thanh_toan' ? 'selected' : '' ?>>Đã thanh toán</option>
                                        <option value="da_huy" <?= $don['trang_thai'] === 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                    <button type="submit" class="btn-gold" style="padding: 6px 12px; font-size: 12px;">Lưu</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>