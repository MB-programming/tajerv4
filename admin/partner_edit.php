<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

$partner = null;
if (isset($_GET['id'])) {
    $stmt = db()->prepare('SELECT * FROM partners WHERE id=?');
    $stmt->execute([(int)$_GET['id']]);
    $partner = $stmt->fetch();
    if (!$partner) { header('Location: partners.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status     = $_POST['status'] ?? 'active';

    $imagePath = $partner['image'] ?? null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $up = uploadFile($_FILES['image'], 'uploads');
        if ($up) {
            $imagePath = $up;
        } else {
            $error = 'فشل رفع الصورة. تأكد من أن الملف صورة صحيحة (jpg, png, svg, webp)';
        }
    }

    if (!isset($error)) {
        if ($partner) {
            db()->prepare("UPDATE partners SET name=?, image=?, sort_order=?, status=?, updated_at=NOW() WHERE id=?")
               ->execute([$name, $imagePath, $sort_order, $status, $partner['id']]);
            $_SESSION['flash'] = ['msg' => 'تم تعديل الشريك بنجاح', 'type' => 'success'];
        } else {
            if (!$imagePath) {
                $error = 'الصورة مطلوبة عند إضافة شريك جديد';
            } else {
                db()->prepare("INSERT INTO partners (name,image,sort_order,status) VALUES (?,?,?,?)")
                   ->execute([$name, $imagePath, $sort_order, $status]);
                $_SESSION['flash'] = ['msg' => 'تم إضافة الشريك بنجاح', 'type' => 'success'];
            }
        }
        if (!isset($error)) { header('Location: partners.php'); exit; }
    }
}

$pageTitle = $partner ? 'تعديل شريك' : 'إضافة شريك';
require_once 'includes/header.php';
?>
<div class="card" style="max-width:600px;">
    <div class="card-header"><h5><?= $pageTitle ?></h5></div>
    <div class="card-body">
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label class="form-label">اسم الشريك (اختياري)</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($partner['name'] ?? '') ?>"
                       placeholder="مثال: شركة أرامكو">
            </div>
            <div class="form-group mb-3">
                <label class="form-label">
                    صورة الشريك
                    <?= $partner ? '<small class="text-muted">(اترك فارغاً للإبقاء على الصورة الحالية)</small>' : '<span class="text-danger">*</span>' ?>
                </label>
                <?php if ($partner && $partner['image']): ?>
                <div class="mb-2">
                    <img src="../<?= htmlspecialchars($partner['image']) ?>"
                         style="height:70px;object-fit:contain;border:1px solid #eee;border-radius:8px;padding:4px;">
                </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*"
                       <?= !$partner ? 'required' : '' ?>>
                <img class="img-preview" style="display:none;margin-top:8px;max-height:80px;border-radius:8px;">
            </div>
            <div class="row">
                <div class="col-6 form-group mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="<?= (int)($partner['sort_order'] ?? 0) ?>">
                </div>
                <div class="col-6 form-group mb-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control">
                        <option value="active"     <?= ($partner['status'] ?? 'active') === 'active'     ? 'selected' : '' ?>>فعال</option>
                        <option value="not_active" <?= ($partner['status'] ?? '') === 'not_active' ? 'selected' : '' ?>>غير فعال</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-brand">
                    <i class="fas fa-save me-1"></i> <?= $partner ? 'حفظ التعديلات' : 'إضافة' ?>
                </button>
                <a href="partners.php" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
