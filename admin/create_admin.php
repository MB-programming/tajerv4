<?php
/**
 * Run this file ONCE to create/reset admin password, then DELETE it.
 * Access: http://yoursite/standalone/admin/create_admin.php?pass=YourNewPassword
 */
require_once __DIR__ . '/../config.php';

$pass = $_GET['pass'] ?? '';
if (!$pass || strlen($pass) < 6) {
    die('Provide ?pass=YourPassword (min 6 chars)');
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = db()->prepare('INSERT INTO admins (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password=?');
$stmt->execute(['admin', $hash, $hash]);

echo '<h3 style="font-family:sans-serif;color:green;">Admin password set successfully!</h3>';
echo '<p style="font-family:sans-serif;">Username: <strong>admin</strong> | Password: <strong>' . htmlspecialchars($pass) . '</strong></p>';
echo '<p style="font-family:sans-serif;color:red;"><strong>DELETE this file now!</strong> <a href="login.php">Go to Login</a></p>';
