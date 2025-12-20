<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();
$id = (int)$_GET['id'];
$conn->query("DELETE FROM kamar WHERE id = $id");
header('Location: index.php'); exit;
?>
