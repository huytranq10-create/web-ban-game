<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
requireAdmin();

$msg = '';

// Xử lý Thêm / Sửa / Xóa game
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        xoa_game((int)$_POST['ma_game']);
        $msg = 'Đã ẩn game khỏi cửa hàng.';
    } elseif ($action === 'save') {
        $ma_game = (int)($_POST['ma_game'] ?? 0);
        $ten = trim($_POST['ten_game']);
        $gia = (float)$_POST['gia'];
        $mo_ta = trim($_POST['mo_ta']);
        $nph = trim($_POST['nha_phat_hanh']);
        $ngay_ph = $_POST['ngay_phat_hanh'] ?? '';
        $ma_theloai = (int)$_POST['ma_theloai'];
        $hinh_anh = trim($_POST['hinh_anh_cu'] ?? 'default.jpg');

        // Upload ảnh nếu admin chọn file mới
        if (!empty($_FILES['hinh_anh']['name'])) {
            $file_name = time() . '_' . basename($_FILES['hinh_anh']['name']);
            $target = '../assets/img/games/' . $file_name;
            if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $target)) {
                $hinh_anh = $file_name;
            }
        }

        if ($ma_game > 0) {
            sua_game($ma_game, $ten, $gia, $mo_ta, $hinh_anh, $nph, $ngay_ph, $ma_theloai, 1);
            $msg = 'Cập nhật thông tin game thành công!';
        } else {
            them_game($ten, $gia, $mo_ta, $hinh_anh, $nph, $ngay_ph, $ma_theloai);
            $msg = 'Thêm game mới thành công!';
        }
    }
}

$edit_game = null;
if (isset($_GET['edit'])) {
    $edit_game = lay_game_theo_id((int)$_GET['edit']);
}

$ds_theloai = lay_danh_sach_theloai();
$ds_game = lay_danh_sach_game();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Game - GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-logo"><span>GAME</span>ADMIN</div>
        <nav class="sidebar-menu">
            <a href="index.php" class="sidebar-link"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
            <a href="games.php" class="sidebar-link active"><i class="fa-solid fa-gamepad"></i> Quản lý Game</a>
            <a href="orders.php" class="sidebar-link"><i class="fa-solid fa-receipt"></i> Quản lý Đơn hàng</a>
            <a href="users.php" class="sidebar-link"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a>
            <a href="../index.php" class="sidebar-link" style="margin-top: auto;"><i class="fa-solid fa-arrow-left"></i> Về trang bán hàng</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">QUẢN LÝ GAME</h1>
        </div>

        <?php if ($msg): ?>
            <div class="alert"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <!-- Form Thêm / Sửa Game -->
        <div class="panel">
            <h3 style="margin-bottom: 16px; color: var(--accent-gold);">
                <?= $edit_game ? 'Sửa Game: ' . htmlspecialchars($edit_game['ten_game']) : 'Thêm Game Mới' ?>
            </h3>
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="ma_game" value="<?= $edit_game['ma_game'] ?? 0 ?>">
                <input type="hidden" name="hinh_anh_cu" value="<?= htmlspecialchars($edit_game['hinh_anh'] ?? '') ?>">

                <div class="form-group">
                    <label>Tên game *</label>
                    <input type="text" name="ten_game" class="form-control" required value="<?= htmlspecialchars($edit_game['ten_game'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Giá bán (VNĐ) *</label>
                    <input type="number" name="gia" class="form-control" required value="<?= $edit_game['gia'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Thể loại *</label>
                    <select name="ma_theloai" class="form-control" required>
                        <?php foreach ($ds_theloai as $tl): ?>
                            <option value="<?= $tl['ma_theloai'] ?>" <?= (($edit_game['ma_theloai'] ?? '') == $tl['ma_theloai']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tl['ten_theloai']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhà phát hành</label>
                    <input type="text" name="nha_phat_hanh" class="form-control" value="<?= htmlspecialchars($edit_game['nha_phat_hanh'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Ngày phát hành</label>
                    <input type="date" name="ngay_phat_hanh" class="form-control" value="<?= $edit_game['ngay_phat_hanh'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Ảnh poster (JPG/PNG)</label>
                    <input type="file" name="hinh_anh" class="form-control" accept="image/*">
                </div>

                <div class="form-group full">
                    <label>Mô tả game</label>
                    <textarea name="mo_ta" rows="3" class="form-control"><?= htmlspecialchars($edit_game['mo_ta'] ?? '') ?></textarea>
                </div>

                <div class="form-group full">
                    <button type="submit" class="btn-gold"><?= $edit_game ? 'Lưu thay đổi' : 'Thêm Game' ?></button>
                    <?php if ($edit_game): ?>
                        <a href="games.php" style="margin-left: 12px; color: var(--text-muted); font-size: 14px;">Hủy sửa</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Danh sách Game hiện có -->
        <div class="panel">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên Game</th>
                        <th>Thể loại</th>
                        <th>Giá bán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ds_game as $g): ?>
                        <tr>
                            <td>#<?= $g['ma_game'] ?></td>
                            <td><img src="../<?= htmlspecialchars($g['hinh_anh']) ?>" style="width: 40px; height: 52px; object-fit: cover; border-radius: 4px;"></td>
                            <td><strong><?= htmlspecialchars($g['ten_game']) ?></strong></td>
                            <td><?= htmlspecialchars($g['ten_theloai']) ?></td>
                            <td style="color: var(--accent-gold); font-weight: 700;"><?= formatPrice($g['gia']) ?></td>
                            <td style="display: flex; gap: 8px; align-items: center;">
                                <a href="games.php?edit=<?= $g['ma_game'] ?>" class="btn-gold" style="padding: 6px 12px; font-size: 12px;">Sửa</a>
                                <form method="POST" onsubmit="return confirm('Ẩn game này khỏi cửa hàng?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="ma_game" value="<?= $g['ma_game'] ?>">
                                    <button type="submit" class="btn-danger">Xóa</button>
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