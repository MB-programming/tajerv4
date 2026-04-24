<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$allowed = ['ar', 'en'];
$l = $_GET['l'] ?? 'ar';
if (in_array($l, $allowed)) {
    $_SESSION['lang'] = $l;
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
