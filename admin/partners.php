<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

// Bulk action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
    $ids = array_map('intval', (array)$_POST['ids']);
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    switch ($_POST['action'] ?? '') {
        case 'delete':
            $imgs = db()->prepare("SELECT image FROM partners WHERE id IN ($ph)");
            $imgs->execute($ids); $imgs = $imgs->fetchAll();
            db()->prepare("DELETE FROM partners WHERE id IN ($ph)")->execute($ids);
            foreach ($imgs as $img) { $f = __DIR__ . '/../../' . $img['image']; if (is_file($f)) @unlink($f); }
            $_SESSION['flash'] = ['msg' => 'تم حذف ' . count($ids) . ' شريك', 'type' => 'success']; break;
        case 'activate':
            db()->prepare("UPDATE partners SET status='active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم تفعيل ' . count($ids) . ' شريك', 'type' => 'success']; break;
        case 'deactivate':
            db()->prepare("UPDATE partners SET status='not_active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم إلغاء تفعيل ' . count($ids) . ' شريك', 'type' => 'success']; break;
    }
    header('Location: partners.php'); exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $r = db()->prepare('SELECT image FROM partners WHERE id=?'); $r->execute([$id]); $r = $r->fetch();
    if ($r) { $f = __DIR__ . '/../../' . $r['image']; if (is_file($f)) @unlink($f); }
    db()->prepare('DELETE FROM partners WHERE id=?')->execute([$id]);
    $_SESSION['flash'] = ['msg' => 'تم الحذف', 'type' => 'success'];
    header('Location: partners.php'); exit;
}
if (isset($_GET['toggle'])) {
    $r = db()->prepare('SELECT status FROM partners WHERE id=?'); $r->execute([(int)$_GET['toggle']]); $row = $r->fetch();
    db()->prepare('UPDATE partners SET status=? WHERE id=?')->execute([$row['status']==='active'?'not_active':'active', (int)$_GET['toggle']]);
    header('Location: partners.php'); exit;
}

$rows = db()->query("SELECT * FROM partners ORDER BY sort_order, id")->fetchAll();
$pageTitle = 'إدارة الشركاء';
require_once 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0" style="color:var(--brand);">
        <i class="fas fa-handshake me-2" style="color:var(--teal);"></i> الشركاء (<?= count($rows) ?>)
    </h5>
    <div class="d-flex gap-2">
        <a href="partner_edit.php" class="btn btn-brand btn-sm"><i class="fas fa-plus me-1"></i> إضافة شريك</a>
        <a href="partners_bulk.php" class="btn btn-teal btn-sm"><i class="fas fa-cloud-upload-alt me-1"></i> رفع جماعي</a>
    </div>
</div>

<form id="bulkForm" method="post">
<input type="hidden" name="action" id="bulkActionInput">
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="check-col"><input type="checkbox" id="selectAll"></th>
                    <th>#</th><th>الصورة</th><th>الاسم</th><th>الترتيب</th><th>الحالة</th><th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $i => $r): ?>
            <tr>
                <td class="check-col"><input type="checkbox" class="row-check" name="ids[]" value="<?= $r['id'] ?>"></td>
                <td><?= $i + 1 ?></td>
                <td><img src="../<?= e($r['image']) ?>" style="height:48px;width:72px;object-fit:contain;border:1px solid #eee;border-radius:6px;padding:2px;"></td>
                <td><?= $r['name'] ? e($r['name']) : '<span class="text-muted" style="font-size:12px;">—</span>' ?></td>
                <td><?= (int)$r['sort_order'] ?></td>
                <td><?= $r['status']==='active' ? '<span class="badge-on">فعال</span>' : '<span class="badge-off">غير فعال</span>' ?></td>
                <td>
                    <a href="partner_edit.php?id=<?= $r['id'] ?>" class="btn-sm-action btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>
                    <a href="?toggle=<?= $r['id'] ?>" class="btn-sm-action btn-tog" title="تبديل"><i class="fas fa-toggle-<?= $r['status']==='active'?'on':'off' ?>"></i></a>
                    <a href="?delete=<?= $r['id'] ?>" class="btn-sm-action btn-del btn-delete-confirm" title="حذف"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">لا يوجد شركاء</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</form>

<div class="bulk-bar" id="bulkBar">
    <span class="bulk-count">0 محدد</span>
    <select class="bulk-select" id="bulkAction">
        <option value="">— اختر إجراء —</option>
        <option value="activate">تفعيل المحدد</option>
        <option value="deactivate">إلغاء تفعيل المحدد</option>
        <option value="delete">حذف المحدد</option>
    </select>
    <button class="bulk-apply" id="applyBulk">تطبيق</button>
    <button class="bulk-cancel" id="cancelBulk">إلغاء التحديد</button>
</div>

<?php require_once 'includes/footer.php'; ?>
