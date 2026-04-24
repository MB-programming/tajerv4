<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if ($u && $p) {
        $stmt = db()->prepare('SELECT * FROM admins WHERE username = ?');
        $stmt->execute([$u]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($p, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            header('Location: dashboard.php'); exit;
        }
    }
    $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>تسجيل الدخول - لوحة التحكم</title>
<link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { font-family: 'El Messiri', sans-serif; background: linear-gradient(135deg, #35285C 0%, #3AC0CA 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .login-card { background: #fff; border-radius: 20px; padding: 40px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
  .login-logo { text-align: center; margin-bottom: 30px; }
  .login-logo img { max-width: 180px; }
  .login-logo h4 { color: #35285C; font-weight: 700; margin-top: 10px; }
  .btn-login { background: linear-gradient(135deg, #35285C, #4a3a7d); color: #fff; border: none; padding: 12px; border-radius: 10px; font-size: 16px; font-family: 'El Messiri', sans-serif; }
  .btn-login:hover { opacity: .9; color: #fff; }
  .form-control { border-radius: 10px; font-family: 'El Messiri', sans-serif; }
  .form-control:focus { border-color: #3AC0CA; box-shadow: 0 0 0 .2rem rgba(58,192,202,.25); }
  label { color: #35285C; font-weight: 600; }
</style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <img src="../assets/images/logo.svg" alt="Logo">
        <h4>لوحة التحكم</h4>
    </div>
    <?php if ($error): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>اسم المستخدم</label>
            <input type="text" name="username" class="form-control" placeholder="اسم المستخدم" required autofocus>
        </div>
        <div class="form-group">
            <label>كلمة المرور</label>
            <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
        </div>
        <button type="submit" class="btn btn-login btn-block">تسجيل الدخول</button>
    </form>
</div>
</body>
</html>
