<?php
session_start();
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function require_login() {
    if (!is_logged_in()) {
        header('Location: /auth/login.php'); exit;
    }
}
function require_role($roles = []) {
    if (!is_logged_in()) { header('Location: /auth/login.php'); exit; }
    if (!in_array($_SESSION['user_role'], (array)$roles)) {
        http_response_code(403); echo 'Akses ditolak'; exit;
    }
}
?>
