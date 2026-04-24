<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

$slider = null;
if (isset($_GET['id'])) {
    $stmt = db()->prepare('SELECT * FROM sliders WHERE id=?');
    $stmt->execute([(int)$_GET['id']]);
    $slider = $stmt->fetch();
    if (!$slider) { header('Location: sliders.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [
        'title_ar'    => trim($_POST['title_ar'] ?? ''),
        'title_en'    => trim($_POST['title_en'] ?? ''),
        'subtitle_ar' => trim($_POST['subtitle_ar'] ?? ''),
        'subtitle_en' => trim($_POST['subtitle_en'] ?? ''),
        'link_url'    => trim($_POST['link_url'] ?? ''),
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        'status'      => $_POST['status'] ?? 'active',
    ];

    $imagePath = $slider['image'] ?? null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $up = uploadFile($_FILES['image'], 'uploads');
        if ($up) {
            $imagePath = $up;
        } else {
            $error = 'فشل رفع الصورة. تأكد من أن الملف صورة صحيحة (jpg, png, webp)';
        }
    }

    if (!isset($error)) {
        if (!$slider && !$imagePath) {
            $error = 'الصورة مطلوبة عند إضافة سلايدر جديد';
        } else {
            $d['image'] = $imagePath;
            if ($slider) {
                $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($d)));
                $vals = array_values($d);
                $vals[] = $slider['id'];
                db()->prepare("UPDATE sliders SET $sets WHERE id=?")->execute($vals);
                $_SESSION['flash'] = ['msg' => 'تم التعديل بنجاح', 'type' => 'success'];
            } else {
                $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($d)));
                $ph   = implode(', ', array_fill(0, count($d), '?'));
                db()->prepare("INSERT INTO sliders ($cols) VALUES ($ph)")->execute(array_values($d));
                $_SESSION['flash'] = ['msg' => 'تم الإضافة بنجاح', 'type' => 'success'];
            }
            header('Location: sliders.php'); exit;
        }
    }
}

$pageTitle = $slider ? 'تعديل سلايدر' : 'إضافة سلايدر';
require_once 'includes/header.php';
?>
<div class="card" style="max-width:780px;">
  <div class="card-header"><h5><?= $pageTitle ?></h5></div>
  <div class="card-body">
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <div class="row">
        <div class="col-12 form-group mb-3">
          <label class="form-label">
            صورة السلايدر
            <?= $slider ? '<small class="text-muted">(اترك فارغاً للإبقاء على الصورة الحالية)</small>' : '<span class="text-danger">*</span>' ?>
          </label>
          <?php if ($slider && $slider['image']): ?>
          <div class="mb-2">
            <img src="../<?= htmlspecialchars($slider['image']) ?>" style="max-height:130px;object-fit:cover;border-radius:8px;border:1px solid #eee;">
          </div>
          <?php endif; ?>
          <input type="file" name="image" class="form-control" accept="image/*" <?= !$slider ? 'required' : '' ?>>
          <img class="img-preview" style="display:none;margin-top:8px;max-height:130px;border-radius:8px;">
          <small class="text-muted">يُفضّل استخدام صور عرضية (landscape) بدقة عالية</small>
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">العنوان (عربي)</label>
          <input type="text" name="title_ar" class="form-control" value="<?= htmlspecialchars($slider['title_ar'] ?? '') ?>" placeholder="اختياري — يظهر على الصورة">
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">العنوان (إنجليزي)</label>
          <input type="text" name="title_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($slider['title_en'] ?? '') ?>" placeholder="Optional — shown over image">
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">النص الفرعي (عربي)</label>
          <textarea name="subtitle_ar" class="form-control" rows="2" placeholder="اختياري"><?= htmlspecialchars($slider['subtitle_ar'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6 form-group mb-3">
          <label class="form-label">النص الفرعي (إنجليزي)</label>
          <textarea name="subtitle_en" class="form-control" dir="ltr" rows="2" placeholder="Optional"><?= htmlspecialchars($slider['subtitle_en'] ?? '') ?></textarea>
        </div>
        <div class="col-md-4 form-group mb-3">
          <label class="form-label">رابط (عند النقر على الصورة)</label>
          <input type="text" name="link_url" class="form-control" dir="ltr" value="<?= htmlspecialchars($slider['link_url'] ?? '') ?>" placeholder="https://... أو اتركه فارغاً">
        </div>
        <div class="col-md-4 form-group mb-3">
          <label class="form-label">ترتيب العرض</label>
          <input type="number" name="sort_order" class="form-control" value="<?= (int)($slider['sort_order'] ?? 0) ?>">
        </div>
        <div class="col-md-4 form-group mb-3">
          <label class="form-label">الحالة</label>
          <select name="status" class="form-control">
            <option value="active"     <?= ($slider['status'] ?? 'active') === 'active'     ? 'selected' : '' ?>>فعال</option>
            <option value="not_active" <?= ($slider['status'] ?? '') === 'not_active' ? 'selected' : '' ?>>غير فعال</option>
          </select>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-brand"><i class="fas fa-save me-1"></i> <?= $slider ? 'حفظ التعديلات' : 'إضافة' ?></button>
        <a href="sliders.php" class="btn btn-secondary">إلغاء</a>
      </div>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
