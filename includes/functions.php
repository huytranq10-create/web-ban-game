<?php
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function formatPrice($price) {
    return number_format((float)$price, 0, ',', '.') . ' ₫';
}

function isLoggedIn() {
    initSession();
    return isset($_SESSION['user']) && !empty($_SESSION['user']['ma_nguoidung']);
}

function isAdmin() {
    if (!isLoggedIn()) return false;
    return ($_SESSION['user']['vai_tro'] ?? '') === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../login.php');
        exit;
    }
}