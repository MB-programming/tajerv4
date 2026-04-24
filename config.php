<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'tajagri_tajer-v1');
define('DB_PASS', 'C)..JdIF6D)h%HI?');
define('DB_NAME', 'tajagri_tajerv1');
define('BASE_URL', ''); // e.g. '/standalone' if in subfolder

function db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die('<div style="padding:20px;font-family:sans-serif;color:red;">Database connection failed: ' . $e->getMessage() . '</div>');
        }
    }
    return $pdo;
}
