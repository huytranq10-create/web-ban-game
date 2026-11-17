<?php
// =====================================================================
// db.sample.php : FILE MẪU cấu hình kết nối MySQL (file này được commit)
//
// Cách dùng:
//   1. Copy file này thành config/db.php
//   2. Sửa các giá trị bên dưới cho đúng với máy của bạn
//   3. KHÔNG commit config/db.php (đã có trong .gitignore)
// =====================================================================

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'web_ban_game');
define('DB_USER', 'root');   // XAMPP mặc định là root
define('DB_PASS', '');       // XAMPP mặc định để trống
define('DB_CHARSET', 'utf8mb4');
