<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

// CSV Export
if (isset($_GET['export'])) {
    $rows = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="messages_' . date('Y-m-d') . '.csv"');
    $f = fopen('php://output', 'w');
    fwrite($f, "\xEF\xBB\xBF");
    fputcsv($f, ['#', 'الاسم', 'البريد الإلكتروني', 'الرسالة', 'الحالة', 'التاريخ']);
    foreach ($rows as $i => $r) {
        fputcsv($f, [$i+1, $r['name'], $r['email'], $r['message'], $r['is_read']?'مقروء':'غير مقروء', date('Y/m/d H:i', strtotime($r['created_at']))]);
    }
    fclose($f); exit;
}

// Bulk action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
    $ids = array_map('intval', (array)$_POST['ids']);
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    switch ($_POST['action'] ?? '') {
        case 'delete':
            db()->prepare("DELETE FROM contact_messages WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم حذف ' . count($ids) . ' رسالة', 'type' => 'success']; break;
        case 'mark_read':
            db()->prepare("UPDATE contact_messages SET is_read=1 WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم تحديد ' . count($ids) . ' رسالة كمقروءة', 'type' => 'success']; break;
        case 'mark_unread':
            db()->prepare("UPDATE contact_messages SET is_read=0 WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم تحديد ' . count($ids) . ' رسالة كغير مقروءة', 'type' => 'success']; break;
    }
    header('Location: messages.php'); exit;
}

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM contact_messages WHERE id=?')->execute([(int)$_GET['delete']]);
    $_SESSION['flash'] = ['msg' => 'تم الحذف', 'type' => 'success'];
    header('Location: messages.php'); exit;
}
if (isset($_GET['read'])) {
    db()->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([(int)$_GET['read']]);
    header('Location: messages.php'); exit;
}

$rows = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
db()->query('UPDATE contact_messages SET is_read=1');
$pageTitle = 'الرسائل الواردة';
require_once 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0" style="color:var(--brand);">
        <i class="fas fa-envelope me-2" style="color:var(--teal);"></i> الرسائل الواردة (<?= count($rows) ?>)
    </h5>
    <a href="?export=1" class="btn btn-teal btn-sm"><i class="fas fa-file-excel me-1"></i> تصدير Excel</a>
</div>

<form id="bulkForm" method="post">
<input type="hidden" name="action" id="bulkActionInput">
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="check-col"><input type="checkbox" id="selectAll"></th>
                    <th>#</th><th>الاسم</th><th>البريد الإلكتروني</th><th>الرسالة</th><th>التاريخ</th><th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $i => $r): ?>
            <tr style="<?= !$r['is_read'] ? 'background:#fff8e7;font-weight:600;' : '' ?>">
                <td class="check-col"><input type="checkbox" class="row-check" name="ids[]" value="<?= $r['id'] ?>"></td>
                <td>
                    <?= $i + 1 ?>
                    <?php if (!$r['is_read']): ?>
                    <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">جديد</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a></td>
                <td style="max-width:280px;white-space:pre-wrap;font-size:13px;"><?= htmlspecialchars($r['message']) ?></td>
                <td style="font-size:12px;white-space:nowrap;"><?= date('Y/m/d H:i', strtotime($r['created_at'])) ?></td>
                <td>
                    <a href="?delete=<?= $r['id'] ?>" class="btn-sm-action btn-del btn-delete-confirm" title="حذف"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">لا توجد رسائل</td></tr>
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
        <option value="mark_read">تحديد كمقروء</option>
        <option value="mark_unread">تحديد كغير مقروء</option>
        <option value="delete">حذف المحدد</option>
    </select>
    <button class="bulk-apply" id="applyBulk">تطبيق</button>
    <button class="bulk-cancel" id="cancelBulk">إلغاء التحديد</button>
</div>

<?php require_once 'includes/footer.php'; ?>
