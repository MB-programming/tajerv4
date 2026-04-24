<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

$branch = null;
if (isset($_GET['id'])) {
    $stmt = db()->prepare('SELECT * FROM branches WHERE id=?');
    $stmt->execute([(int)$_GET['id']]);
    $branch = $stmt->fetch();
    if (!$branch) { header('Location: branches.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [
        'name_ar'          => trim($_POST['name_ar'] ?? ''),
        'name_en'          => trim($_POST['name_en'] ?? ''),
        'phone'            => trim($_POST['phone'] ?? ''),
        'map_url'          => trim($_POST['map_url'] ?? ''),
        'address_ar'       => trim($_POST['address_ar'] ?? ''),
        'address_en'       => trim($_POST['address_en'] ?? ''),
        'working_hours_ar' => trim($_POST['working_hours_ar'] ?? ''),
        'working_hours_en' => trim($_POST['working_hours_en'] ?? ''),
        'sort_order'       => (int)($_POST['sort_order'] ?? 0),
        'status'           => $_POST['status'] ?? 'active',
    ];

    if (empty($d['name_ar']) || empty($d['name_en'])) {
        $error = 'الاسم (عربي وإنجليزي) مطلوب';
    } else {
        if ($branch) {
            $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($d)));
            $vals = array_values($d);
            $vals[] = $branch['id'];
            db()->prepare("UPDATE branches SET $sets WHERE id=?")->execute($vals);
            $_SESSION['flash'] = ['msg' => 'تم التعديل بنجاح', 'type' => 'success'];
        } else {
            $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($d)));
            $ph   = implode(', ', array_fill(0, count($d), '?'));
            db()->prepare("INSERT INTO branches ($cols) VALUES ($ph)")->execute(array_values($d));
            $_SESSION['flash'] = ['msg' => 'تم الإضافة بنجاح', 'type' => 'success'];
        }
        header('Location: branches.php'); exit;
    }
}

$pageTitle = $branch ? 'تعديل فرع' : 'إضافة فرع';
require_once 'includes/header.php';
?>
<div class="card" style="max-width:780px;">
  <div class="card-header"><h5><?= $pageTitle ?></h5></div>
  <div class="card-body">
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="row">
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">الاسم (عربي) <span class="text-danger">*</span></label>
          <input type="text" name="name_ar" class="form-control" value="<?= htmlspecialchars($branch['name_ar'] ?? '') ?>" required>
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">الاسم (إنجليزي) <span class="text-danger">*</span></label>
          <input type="text" name="name_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($branch['name_en'] ?? '') ?>" required>
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">رقم الهاتف</label>
          <input type="text" name="phone" class="form-control" dir="ltr" value="<?= htmlspecialchars($branch['phone'] ?? '') ?>" placeholder="+966xxxxxxxxx">
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">رابط الخريطة (Google Maps)</label>
          <input type="text" name="map_url" class="form-control" dir="ltr" value="<?= htmlspecialchars($branch['map_url'] ?? '') ?>" placeholder="https://maps.google.com/...">
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">العنوان (عربي)</label>
          <input type="text" name="address_ar" class="form-control" value="<?= htmlspecialchars($branch['address_ar'] ?? '') ?>">
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">العنوان (إنجليزي)</label>
          <input type="text" name="address_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($branch['address_en'] ?? '') ?>">
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">أوقات الدوام (عربي)</label>
          <input type="text" name="working_hours_ar" class="form-control" value="<?= htmlspecialchars($branch['working_hours_ar'] ?? '') ?>" placeholder="السبت - الخميس: 8ص - 5م">
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">أوقات الدوام (إنجليزي)</label>
          <input type="text" name="working_hours_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($branch['working_hours_en'] ?? '') ?>" placeholder="Sat - Thu: 8AM - 5PM">
        </div>
        <div class="col-md-4 form-group mb-3">
          <label class="form-label">ترتيب العرض</label>
          <input type="number" name="sort_order" class="form-control" value="<?= (int)($branch['sort_order'] ?? 0) ?>">
        </div>
        <div class="col-md-4 form-group mb-3">
          <label class="form-label">الحالة</label>
          <select name="status" class="form-control">
            <option value="active" <?= ($branch['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>فعال</option>
            <option value="not_active" <?= ($branch['status'] ?? '') === 'not_active' ? 'selected' : '' ?>>غير فعال</option>
          </select>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save ml-1"></i> حفظ</button>
        <a href="branches.php" class="btn btn-secondary">إلغاء</a>
      </div>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
