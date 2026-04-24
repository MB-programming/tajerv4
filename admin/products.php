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
            db()->prepare("DELETE FROM products WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم حذف ' . count($ids) . ' منتج', 'type' => 'success']; break;
        case 'activate':
            db()->prepare("UPDATE products SET status='active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم تفعيل ' . count($ids) . ' منتج', 'type' => 'success']; break;
        case 'deactivate':
            db()->prepare("UPDATE products SET status='not_active' WHERE id IN ($ph)")->execute($ids);
            $_SESSION['flash'] = ['msg' => 'تم إلغاء تفعيل ' . count($ids) . ' منتج', 'type' => 'success']; break;
    }
    header('Location: products.php'); exit;
}

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_GET['delete']]);
    $_SESSION['flash'] = ['msg' => 'تم الحذف', 'type' => 'success'];
    header('Location: products.php'); exit;
}
if (isset($_GET['toggle'])) {
    $r = db()->prepare('SELECT status FROM products WHERE id=?'); $r->execute([(int)$_GET['toggle']]); $row = $r->fetch();
    db()->prepare('UPDATE products SET status=? WHERE id=?')->execute([$row['status']==='active'?'not_active':'active', (int)$_GET['toggle']]);
    header('Location: products.php'); exit;
}

$rows = db()->query('SELECT * FROM products ORDER BY sort_order, id')->fetchAll();
$pageTitle = 'إدارة المنتجات';
require_once 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0" style="color:var(--brand);">
        <i class="fas fa-seedling me-2" style="color:var(--teal);"></i> المنتجات (<?= count($rows) ?>)
    </h5>
    <a href="product_edit.php" class="btn btn-brand btn-sm"><i class="fas fa-plus me-1"></i> إضافة منتج</a>
</div>

<form id="bulkForm" method="post">
<input type="hidden" name="action" id="bulkActionInput">
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="check-col"><input type="checkbox" id="selectAll"></th>
                    <th>#</th><th>الصورة</th><th>الاسم (AR)</th><th>الاسم (EN)</th><th>الرابط</th><th>الحالة</th><th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $i => $r): ?>
            <tr>
                <td class="check-col"><input type="checkbox" class="row-check" name="ids[]" value="<?= $r['id'] ?>"></td>
                <td><?= $i + 1 ?></td>
                <td><?php if ($r['image']): ?><img src="../<?= htmlspecialchars($r['image']) ?>" style="height:40px;width:60px;object-fit:contain;border-radius:6px;border:1px solid #eee;"><?php else: ?>—<?php endif; ?></td>
                <td><?= htmlspecialchars($r['name_ar']) ?></td>
                <td style="font-size:13px;color:#777;"><?= htmlspecialchars($r['name_en']) ?></td>
                <td><?= $r['url'] ? '<a href="'.htmlspecialchars($r['url']).'" target="_blank" style="font-size:12px;color:var(--teal);">رابط</a>' : '—' ?></td>
                <td><?= $r['status']==='active' ? '<span class="badge-on">فعال</span>' : '<span class="badge-off">غير فعال</span>' ?></td>
                <td>
                    <a href="product_edit.php?id=<?= $r['id'] ?>" class="btn-sm-action btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>
                    <a href="?toggle=<?= $r['id'] ?>" class="btn-sm-action btn-tog" title="تبديل"><i class="fas fa-toggle-<?= $r['status']==='active'?'on':'off' ?>"></i></a>
                    <a href="?delete=<?= $r['id'] ?>" class="btn-sm-action btn-del btn-delete-confirm" title="حذف"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">لا توجد منتجات</td></tr>
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
