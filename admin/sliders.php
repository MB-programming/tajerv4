<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

// Bulk action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
    $ids = array_map('intval', (array)$_POST['ids']);
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    switch ($_POST['action'] ?? '') {
        case 'delete':
            db()->prepare("DELETE FROM sliders WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم حذف ' . count($ids) . ' سلايدر', 'type' => 'success']; break;
        case 'activate':
            db()->prepare("UPDATE sliders SET status='active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم تفعيل ' . count($ids) . ' سلايدر', 'type' => 'success']; break;
        case 'deactivate':
            db()->prepare("UPDATE sliders SET status='not_active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم إلغاء تفعيل ' . count($ids) . ' سلايدر', 'type' => 'success']; break;
    }
    header('Location: sliders.php'); exit;
}

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM sliders WHERE id=?')->execute([(int)$_GET['delete']]);
    $_SESSION['flash'] = ['msg' => 'تم الحذف', 'type' => 'success'];
    header('Location: sliders.php'); exit;
}
if (isset($_GET['toggle'])) {
    $r = db()->prepare('SELECT status FROM sliders WHERE id=?');
    $r->execute([(int)$_GET['toggle']]);
    $row = $r->fetch();
    db()->prepare('UPDATE sliders SET status=? WHERE id=?')
        ->execute([$row['status'] === 'active' ? 'not_active' : 'active', (int)$_GET['toggle']]);
    header('Location: sliders.php'); exit;
}

$rows = db()->query('SELECT * FROM sliders ORDER BY sort_order, id')->fetchAll();
$pageTitle = 'إدارة السلايدر';
require_once 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0" style="color:var(--brand);">
        <i class="fas fa-images me-2" style="color:var(--teal);"></i> السلايدر (<?= count($rows) ?>)
    </h5>
    <a href="slider_edit.php" class="btn btn-brand btn-sm"><i class="fas fa-plus me-1"></i> إضافة سلايدر</a>
</div>

<form id="bulkForm" method="post">
<input type="hidden" name="action" id="bulkActionInput">
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="check-col"><input type="checkbox" id="selectAll"></th>
                    <th>#</th><th>الصورة</th><th>العنوان (عربي)</th><th>العنوان (إنجليزي)</th><th>الرابط</th><th>الترتيب</th><th>الحالة</th><th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $i => $r): ?>
            <tr>
                <td class="check-col"><input type="checkbox" class="row-check" name="ids[]" value="<?= $r['id'] ?>"></td>
                <td><?= $i + 1 ?></td>
                <td>
                    <?php if ($r['image']): ?>
                    <img src="../<?= htmlspecialchars($r['image']) ?>" style="height:50px;width:90px;object-fit:cover;border-radius:6px;border:1px solid #eee;">
                    <?php else: ?>
                    <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['title_ar'] ?? '') ?: '—' ?></td>
                <td style="font-size:13px;color:#777;"><?= htmlspecialchars($r['title_en'] ?? '') ?: '—' ?></td>
                <td style="font-size:12px;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($r['link_url'] ?? '') ?: '—' ?></td>
                <td><?= (int)$r['sort_order'] ?></td>
                <td><?= $r['status'] === 'active' ? '<span class="badge-on">فعال</span>' : '<span class="badge-off">غير فعال</span>' ?></td>
                <td>
                    <a href="slider_edit.php?id=<?= $r['id'] ?>" class="btn-sm-action btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>
                    <a href="?toggle=<?= $r['id'] ?>" class="btn-sm-action btn-tog" title="تبديل"><i class="fas fa-toggle-<?= $r['status'] === 'active' ? 'on' : 'off' ?>"></i></a>
                    <a href="?delete=<?= $r['id'] ?>" class="btn-sm-action btn-del btn-delete-confirm" title="حذف"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
            <tr><td colspan="9" class="text-center text-muted py-4"><i class="fas fa-images fa-2x d-block mb-2" style="color:#ddd;"></i>لا توجد سلايدرات — <a href="slider_edit.php">أضف الأول</a></td></tr>
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
