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
            db()->prepare("DELETE FROM branches WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم حذف ' . count($ids) . ' فرع', 'type' => 'success']; break;
        case 'activate':
            db()->prepare("UPDATE branches SET status='active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم تفعيل ' . count($ids) . ' فرع', 'type' => 'success']; break;
        case 'deactivate':
            db()->prepare("UPDATE branches SET status='not_active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم إلغاء تفعيل ' . count($ids) . ' فرع', 'type' => 'success']; break;
    }
    header('Location: branches.php'); exit;
}

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM branches WHERE id=?')->execute([(int)$_GET['delete']]);
    $_SESSION['flash'] = ['msg' => 'تم الحذف', 'type' => 'success'];
    header('Location: branches.php'); exit;
}
if (isset($_GET['toggle'])) {
    $r = db()->prepare('SELECT status FROM branches WHERE id=?'); $r->execute([(int)$_GET['toggle']]); $row = $r->fetch();
    db()->prepare('UPDATE branches SET status=? WHERE id=?')->execute([$row['status']==='active'?'not_active':'active', (int)$_GET['toggle']]);
    header('Location: branches.php'); exit;
}

$branches = db()->query('SELECT * FROM branches ORDER BY sort_order, id')->fetchAll();
$pageTitle = 'إدارة الفروع';
require_once 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0" style="color:var(--brand);">
        <i class="fas fa-map-marker-alt me-2" style="color:var(--teal);"></i> الفروع (<?= count($branches) ?>)
    </h5>
    <a href="branch_edit.php" class="btn btn-brand btn-sm"><i class="fas fa-plus me-1"></i> إضافة فرع</a>
</div>

<form id="bulkForm" method="post">
<input type="hidden" name="action" id="bulkActionInput">
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="check-col"><input type="checkbox" id="selectAll"></th>
                    <th>#</th><th>الاسم بالعربي</th><th>الاسم بالإنجليزي</th><th>الهاتف</th><th>أوقات الدوام</th><th>الحالة</th><th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($branches as $i => $b): ?>
            <tr>
                <td class="check-col"><input type="checkbox" class="row-check" name="ids[]" value="<?= $b['id'] ?>"></td>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($b['name_ar']) ?></td>
                <td style="font-size:13px;color:#777;"><?= htmlspecialchars($b['name_en']) ?></td>
                <td style="font-size:13px;"><?= htmlspecialchars($b['phone'] ?? '—') ?></td>
                <td style="font-size:12px;color:#777;"><?= htmlspecialchars($b['working_hours_ar'] ?? '—') ?></td>
                <td><?= $b['status']==='active' ? '<span class="badge-on">فعال</span>' : '<span class="badge-off">غير فعال</span>' ?></td>
                <td>
                    <a href="branch_edit.php?id=<?= $b['id'] ?>" class="btn-sm-action btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>
                    <a href="?toggle=<?= $b['id'] ?>" class="btn-sm-action btn-tog" title="تبديل"><i class="fas fa-toggle-<?= $b['status']==='active'?'on':'off' ?>"></i></a>
                    <a href="?delete=<?= $b['id'] ?>" class="btn-sm-action btn-del btn-delete-confirm" title="حذف"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$branches): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">لا توجد فروع</td></tr>
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
