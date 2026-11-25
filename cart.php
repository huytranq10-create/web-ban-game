<?php
$page_title = "Giỏ hàng";
include 'includes/header.php';
requireLogin(); // Bắt buộc đăng nhập mới dùng giỏ hàng (vì giỏ hàng lưu theo ma_nguoidung trong MySQL)

$ma_nguoidung = $_SESSION['user']['ma_nguoidung'];

// Xử lý thêm nhanh từ trang chi tiết (?add=ma_game)
if (isset($_GET['add'])) {
    them_vao_gio_hang($ma_nguoidung, (int)$_GET['add'], 1);
    header('Location: cart.php');
    exit;
}

// Xử lý cập nhật số lượng hoặc xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $ma_game = (int)($_POST['ma_game'] ?? 0);

    if ($action === 'update' && $ma_game > 0) {
        $so_luong = (int)($_POST['so_luong'] ?? 1);
        cap_nhat_so_luong_gio_hang($ma_nguoidung, $ma_game, $so_luong);
    } elseif ($action === 'delete' && $ma_game > 0) {
        xoa_khoi_gio_hang($ma_nguoidung, $ma_game);
    }
    header('Location: cart.php');
    exit;
}

include 'includes/navbar.php';

$gio_hang = lay_gio_hang($ma_nguoidung);
$tong_tien = 0;
foreach ($gio_hang as $item) {
    $tong_tien += $item['thanh_tien'];
}
?>

<div class="container" style="padding: 40px 20px;">
    <h1 class="section-title" style="font-size: 26px; margin-bottom: 24px;">GIỎ HÀNG CỦA BẠN</h1>

    <?php if (!empty($gio_hang)): ?>
        <table class="data-table" id="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <?php foreach ($gio_hang as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-game-info">
                                <?php $g_info = lay_game_theo_id($item['ma_game']); ?>
                                    <img src="assets/img/games/<?= htmlspecialchars($g_info['hinh_anh'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($item['ten_game']) ?>">
                                <a href="game-detail.php?ma_game=<?= $item['ma_game'] ?>" style="font-weight: 700;">
                                    <?= htmlspecialchars($item['ten_game']) ?>
                                </a>
                            </div>
                        </td>
                        <td><?= formatPrice($item['gia']) ?></td>
                        <td>
                            <form method="POST" style="display: flex; gap: 6px; align-items: center;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="ma_game" value="<?= $item['ma_game'] ?>">
                                <input type="number" name="so_luong" value="<?= $item['so_luong'] ?>" min="1" max="99" class="qty-input">
                                <button type="submit" class="tag-item" style="cursor: pointer;">Cập nhật</button>
                            </form>
                        </td>
                        <td style="color: var(--accent-gold); font-weight: 700;"><?= formatPrice($item['thanh_tien']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="ma_game" value="<?= $item['ma_game'] ?>">
                                <button type="submit" class="btn-danger-sm"><i class="fa-solid fa-trash"></i> Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div>
                <span style="color: var(--text-muted); font-size: 14px;">Tổng thanh toán:</span>
                <div style="font-size: 28px; font-weight: 900; color: var(--accent-gold);" id="cart-total">
                    <?= formatPrice($tong_tien) ?>
                </div>
            </div>
            <div style="display: flex; gap: 14px;">
                <a href="games.php" class="view-all" style="padding: 14px 22px; font-size: 14px;">Tiếp tục mua game</a>
                <a href="checkout.php" class="btn-buy">TIẾN HÀNH ĐẶT HÀNG <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 0; background: var(--bg-card); border-radius: 12px;">
            <p style="color: var(--text-muted); margin-bottom: 20px;">Giỏ hàng của bạn đang trống.</p>
            <a href="games.php" class="btn-hero">KHÁM PHÁ KHO GAME</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>