<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';
$item = null;
if (isset($_GET['id'])) { $st=db()->prepare('SELECT * FROM teams WHERE id=?'); $st->execute([(int)$_GET['id']]); $item=$st->fetch(); if(!$item){header('Location: teams.php');exit;} }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d=['name_ar'=>trim($_POST['name_ar']??''),'name_en'=>trim($_POST['name_en']??''),'description_ar'=>trim($_POST['description_ar']??''),'description_en'=>trim($_POST['description_en']??''),'mobile'=>trim($_POST['mobile']??''),'email'=>trim($_POST['email']??''),'sort_order'=>(int)($_POST['sort_order']??0),'status'=>$_POST['status']??'active'];
    if(!empty($_FILES['image']['name'])){$up=uploadFile($_FILES['image'],'uploads');if($up)$d['image']=$up;}elseif($item){$d['image']=$item['image'];}
    if(empty($d['name_ar'])||empty($d['name_en'])){$error='الاسم مطلوب';}
    else{
        if($item){$sets=implode(', ',array_map(fn($k)=>"`$k`=?",array_keys($d)));$v=array_values($d);$v[]=$item['id'];db()->prepare("UPDATE teams SET $sets WHERE id=?")->execute($v);$_SESSION['flash']=['msg'=>'تم التعديل','type'=>'success'];}
        else{$c=implode(', ',array_map(fn($k)=>"`$k`",array_keys($d)));$p=implode(', ',array_fill(0,count($d),'?'));db()->prepare("INSERT INTO teams ($c) VALUES ($p)")->execute(array_values($d));$_SESSION['flash']=['msg'=>'تم الإضافة','type'=>'success'];}
        header('Location: teams.php'); exit;
    }
}
$pageTitle = $item ? 'تعديل عضو' : 'إضافة عضو';
require_once 'includes/header.php';
?>
<div class="card" style="max-width:720px;">
  <div class="card-header"><h5><?= $pageTitle ?></h5></div>
  <div class="card-body">
    <?php if(!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-6 form-group mb-3"><label class="form-label">الاسم (عربي) *</label><input type="text" name="name_ar" class="form-control" value="<?= htmlspecialchars($item['name_ar']??'') ?>" required></div>
        <div class="col-md-6 form-group mb-3"><label class="form-label">الاسم (إنجليزي) *</label><input type="text" name="name_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($item['name_en']??'') ?>" required></div>
        <div class="col-md-6 form-group mb-3"><label class="form-label">المسمى الوظيفي (عربي)</label><input type="text" name="description_ar" class="form-control" value="<?= htmlspecialchars($item['description_ar']??'') ?>"></div>
        <div class="col-md-6 form-group mb-3"><label class="form-label">المسمى الوظيفي (إنجليزي)</label><input type="text" name="description_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($item['description_en']??'') ?>"></div>
        <div class="col-md-6 form-group mb-3"><label class="form-label">الجوال</label><input type="text" name="mobile" class="form-control" dir="ltr" value="<?= htmlspecialchars($item['mobile']??'') ?>"></div>
        <div class="col-md-6 form-group mb-3"><label class="form-label">البريد الإلكتروني</label><input type="email" name="email" class="form-control" dir="ltr" value="<?= htmlspecialchars($item['email']??'') ?>"></div>
        <div class="col-md-12 form-group mb-3">
          <label class="form-label">الصورة الشخصية</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <?php if($item&&$item['image']): ?><img src="../<?= htmlspecialchars($item['image']) ?>" class="img-preview" style="display:block;border-radius:50%;"><?php else: ?><img class="img-preview" style="display:none;border-radius:50%;"><?php endif; ?>
        </div>
        <div class="col-md-4 form-group mb-3"><label class="form-label">الترتيب</label><input type="number" name="sort_order" class="form-control" value="<?= (int)($item['sort_order']??0) ?>"></div>
        <div class="col-md-4 form-group mb-3"><label class="form-label">الحالة</label><select name="status" class="form-control"><option value="active" <?= ($item['status']??'active')==='active'?'selected':'' ?>>فعال</option><option value="not_active" <?= ($item['status']??'')==='not_active'?'selected':'' ?>>غير فعال</option></select></div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save ml-1"></i> حفظ</button>
      <a href="teams.php" class="btn btn-secondary mr-2">إلغاء</a>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
