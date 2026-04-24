<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

// Bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
    $ids = array_map('intval', (array)$_POST['ids']);
    if ($_POST['action'] === 'delete') {
        // Never delete self or last admin
        $ids = array_filter($ids, fn($id) => $id !== (int)$_SESSION['admin_id']);
        if (count($ids)) {
            $total = (int)db()->query("SELECT COUNT(*) FROM admins")->fetchColumn();
            if ($total - count($ids) < 1) {
                $_SESSION['flash'] = ['msg' => 'لا يمكن حذف جميع المديرين', 'type' => 'error'];
            } else {
                $ph = implode(',', array_fill(0, count($ids), '?'));
                db()->prepare("DELETE FROM admins WHERE id IN ($ph)")->execute(array_values($ids));
                $_SESSION['flash'] = ['msg' => 'تم حذف ' . count($ids) . ' مدير', 'type' => 'success'];
            }
        } else {
            $_SESSION['flash'] = ['msg' => 'لا يمكن حذف حسابك الخاص', 'type' => 'error'];
        }
    }
    header('Location: admins.php'); exit;
}

// Single delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === (int)$_SESSION['admin_id']) {
        $_SESSION['flash'] = ['msg' => 'لا يمكنك حذف حسابك الخاص', 'type' => 'error'];
    } else {
        $total = db()->query("SELECT COUNT(*) FROM admins")->fetchColumn();
        if ($total <= 1) {
            $_SESSION['flash'] = ['msg' => 'لا يمكن حذف المدير الوحيد في النظام', 'type' => 'error'];
        } else {
            db()->prepare('DELETE FROM admins WHERE id=?')->execute([$id]);
            $_SESSION['flash'] = ['msg' => 'تم الحذف', 'type' => 'success'];
        }
    }
    header('Location: admins.php'); exit;
}

$rows = db()->query("SELECT id, username, created_at FROM admins ORDER BY id")->fetchAll();
$pageTitle = 'إدارة المستخدمين';
require_once 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0" style="color:var(--brand);">
        <i class="fas fa-users-cog me-2" style="color:var(--teal);"></i> المستخدمون (<?= count($rows) ?>)
    </h5>
    <a href="admin_edit.php" class="btn btn-brand btn-sm"><i class="fas fa-plus me-1"></i> إضافة مستخدم</a>
</div>

<form id="bulkForm" method="post">
<input type="hidden" name="action" id="bulkActionInput">
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="check-col"><input type="checkbox" id="selectAll"></th>
                    <th>#</th><th>اسم المستخدم</th><th>تاريخ الإنشاء</th><th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $i => $r): ?>
            <tr>
                <td class="check-col">
                    <?php if ($r['id'] != $_SESSION['admin_id']): ?>
                    <input type="checkbox" class="row-check" name="ids[]" value="<?= $r['id'] ?>">
                    <?php endif; ?>
                </td>
                <td><?= $i + 1 ?></td>
                <td>
                    <?= htmlspecialchars($r['username']) ?>
                    <?php if ($r['id'] == $_SESSION['admin_id']): ?>
                    <span class="badge bg-primary ms-1" style="font-size:10px;">أنت</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:12px;"><?= date('Y/m/d', strtotime($r['created_at'])) ?></td>
                <td>
                    <a href="admin_edit.php?id=<?= $r['id'] ?>" class="btn-sm-action btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>
                    <?php if ($r['id'] != $_SESSION['admin_id']): ?>
                    <a href="?delete=<?= $r['id'] ?>" class="btn-sm-action btn-del btn-delete-confirm" title="حذف"><i class="fas fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">لا يوجد مستخدمون</td></tr>
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
        <option value="delete">حذف المحدد</option>
    </select>
    <button class="bulk-apply" id="applyBulk">تطبيق</button>
    <button class="bulk-cancel" id="cancelBulk">إلغاء التحديد</button>
</div>

<?php require_once 'includes/footer.php'; ?>
