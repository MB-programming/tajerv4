<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';
$item = null;
if (isset($_GET['id'])) { $st=db()->prepare('SELECT * FROM numbers WHERE id=?'); $st->execute([(int)$_GET['id']]); $item=$st->fetch(); if(!$item){header('Location: numbers.php');exit;} }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d=['number'=>trim($_POST['number']??''),'description_ar'=>trim($_POST['description_ar']??''),'description_en'=>trim($_POST['description_en']??''),'sort_order'=>(int)($_POST['sort_order']??0),'status'=>$_POST['status']??'active'];
    if(empty($d['number'])){$error='الرقم مطلوب';}
    else {
        if($item){$sets=implode(', ',array_map(fn($k)=>"`$k`=?",array_keys($d)));$v=array_values($d);$v[]=$item['id'];db()->prepare("UPDATE numbers SET $sets WHERE id=?")->execute($v);$_SESSION['flash']=['msg'=>'تم التعديل','type'=>'success'];}
        else{$c=implode(', ',array_map(fn($k)=>"`$k`",array_keys($d)));$p=implode(', ',array_fill(0,count($d),'?'));db()->prepare("INSERT INTO numbers ($c) VALUES ($p)")->execute(array_values($d));$_SESSION['flash']=['msg'=>'تم الإضافة','type'=>'success'];}
        header('Location: numbers.php'); exit;
    }
}
$pageTitle = $item ? 'تعديل رقم' : 'إضافة رقم';
require_once 'includes/header.php';
?>
<div class="card" style="max-width:580px;">
  <div class="card-header"><h5><?= $pageTitle ?></h5></div>
  <div class="card-body">
    <?php if(!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group mb-3"><label class="form-label">الرقم *</label><input type="text" name="number" class="form-control" value="<?= htmlspecialchars($item['number']??'') ?>" placeholder="+100" required></div>
      <div class="form-group mb-3"><label class="form-label">الوصف (عربي)</label><input type="text" name="description_ar" class="form-control" value="<?= htmlspecialchars($item['description_ar']??'') ?>"></div>
      <div class="form-group mb-3"><label class="form-label">الوصف (إنجليزي)</label><input type="text" name="description_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($item['description_en']??'') ?>"></div>
      <div class="row">
        <div class="col-md-6 form-group mb-3"><label class="form-label">الترتيب</label><input type="number" name="sort_order" class="form-control" value="<?= (int)($item['sort_order']??0) ?>"></div>
        <div class="col-md-6 form-group mb-3"><label class="form-label">الحالة</label><select name="status" class="form-control"><option value="active" <?= ($item['status']??'active')==='active'?'selected':'' ?>>فعال</option><option value="not_active" <?= ($item['status']??'')==='not_active'?'selected':'' ?>>غير فعال</option></select></div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save ml-1"></i> حفظ</button>
      <a href="numbers.php" class="btn btn-secondary mr-2">إلغاء</a>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
