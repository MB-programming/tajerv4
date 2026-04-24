<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

$admin = null;
if (isset($_GET['id'])) {
    $stmt = db()->prepare('SELECT id, username FROM admins WHERE id=?');
    $stmt->execute([(int)$_GET['id']]);
    $admin = $stmt->fetch();
    if (!$admin) { header('Location: admins.php'); exit; }
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    if (empty($username)) {
        $error = 'اسم المستخدم مطلوب';
    } elseif (!$admin && empty($password)) {
        $error = 'كلمة المرور مطلوبة عند إنشاء مستخدم جديد';
    } elseif (!empty($password) && $password !== $confirm) {
        $error = 'كلمة المرور وتأكيدها غير متطابقتين';
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } else {
        // Check username uniqueness
        $check = db()->prepare('SELECT COUNT(*) FROM admins WHERE username=? AND id!=?');
        $check->execute([$username, $admin['id'] ?? 0]);
        if ($check->fetchColumn()) {
            $error = 'اسم المستخدم مستخدم بالفعل';
        }
    }

    if (!$error) {
        if ($admin) {
            if (!empty($password)) {
                db()->prepare('UPDATE admins SET username=?, password=? WHERE id=?')
                   ->execute([$username, password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
            } else {
                db()->prepare('UPDATE admins SET username=? WHERE id=?')
                   ->execute([$username, $admin['id']]);
            }
            $_SESSION['flash'] = ['msg' => 'تم تعديل المستخدم بنجاح', 'type' => 'success'];
        } else {
            db()->prepare('INSERT INTO admins (username, password) VALUES (?, ?)')
               ->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['flash'] = ['msg' => 'تم إضافة المستخدم بنجاح', 'type' => 'success'];
        }
        header('Location: admins.php'); exit;
    }
}

$pageTitle = $admin ? 'تعديل مستخدم' : 'إضافة مستخدم';
require_once 'includes/header.php';
?>
<div class="card" style="max-width:500px;">
    <div class="card-header"><h5><?= $pageTitle ?></h5></div>
    <div class="card-body">
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group mb-3">
                <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" dir="ltr"
                       value="<?= htmlspecialchars($admin['username'] ?? '') ?>"
                       placeholder="admin" autocomplete="username" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">
                    كلمة المرور
                    <?= $admin ? '<small class="text-muted">(اترك فارغاً للإبقاء على الحالية)</small>' : '<span class="text-danger">*</span>' ?>
                </label>
                <div class="input-group">
                    <input type="password" name="password" id="pwd" class="form-control" dir="ltr"
                           placeholder="6 أحرف على الأقل" autocomplete="new-password"
                           <?= !$admin ? 'required' : '' ?>>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('pwd')">
                        <i class="fas fa-eye" id="pwd-icon"></i>
                    </button>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label">تأكيد كلمة المرور</label>
                <input type="password" name="confirm" id="cpwd" class="form-control" dir="ltr"
                       placeholder="أعد كتابة كلمة المرور" autocomplete="new-password">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-brand">
                    <i class="fas fa-save me-1"></i> <?= $admin ? 'حفظ التعديلات' : 'إضافة' ?>
                </button>
                <a href="admins.php" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<?php
$extraScript = <<<'JS'
<script>
function togglePwd(id) {
    const inp  = document.getElementById(id);
    const icon = document.getElementById(id + '-icon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
JS;
require_once 'includes/footer.php';
?>
