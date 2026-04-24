<?php
if (!isset($pageTitle)) $pageTitle = 'لوحة التحكم';
$flash = null;
if (!empty($_SESSION['flash'])) { $flash = $_SESSION['flash']; unset($_SESSION['flash']); }
$currentPage = basename($_SERVER['PHP_SELF']);

$_unread = 0;
try { $_unread = (int)db()->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn(); } catch(Throwable $e) {}

$navItems = [
    ['file'=>'dashboard.php',  'icon'=>'fas fa-home',          'label'=>'الرئيسية'],
    ['file'=>'settings.php',   'icon'=>'fas fa-sliders-h',     'label'=>'الإعدادات'],
    ['file'=>'products.php',   'icon'=>'fas fa-seedling',      'label'=>'المنتجات',      'edit'=>'product_edit.php'],
    ['file'=>'numbers.php',    'icon'=>'fas fa-chart-pie',     'label'=>'الإحصائيات',   'edit'=>'number_edit.php'],
    ['file'=>'teams.php',      'icon'=>'fas fa-users',         'label'=>'الفريق',        'edit'=>'team_edit.php'],
    ['file'=>'partners.php',   'icon'=>'fas fa-handshake',     'label'=>'الشركاء',       'edit'=>'partner_edit.php', 'edit2'=>'partners_bulk.php'],
    ['file'=>'branches.php',   'icon'=>'fas fa-map-marker-alt','label'=>'الفروع',        'edit'=>'branch_edit.php'],
    ['file'=>'messages.php',   'icon'=>'fas fa-envelope',      'label'=>'الرسائل',       'badge'=>$_unread],
    ['file'=>'admins.php',     'icon'=>'fas fa-users-cog',     'label'=>'المستخدمون',    'edit'=>'admin_edit.php'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?> — تاج الزراعية</title>
<link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<style>
/* ─── Variables ─── */
:root{
  --brand:    #35285C;
  --brand2:   #4a3a7d;
  --teal:     #3AC0CA;
  --teal2:    #2a9eaa;
  --green:    #7EB595;
  --bg:       #f0f3f8;
  --sidebar:  260px;
  --radius:   14px;
  --shadow:   0 2px 16px rgba(0,0,0,.07);
}
*{font-family:'El Messiri',sans-serif;box-sizing:border-box;}
body{background:var(--bg);margin:0;padding:0;}
a{text-decoration:none;}

/* ─── Sidebar ─── */
.sidebar{
  width:var(--sidebar); height:100vh;
  background:linear-gradient(175deg,#1a1233 0%,#35285C 60%,#2d3a60 100%);
  position:fixed; top:0; right:0; z-index:200;
  display:flex; flex-direction:column;
  box-shadow:4px 0 20px rgba(0,0,0,.25);
  transition:.3s;
}
.sb-logo{
  padding:22px 20px 18px;
  border-bottom:1px solid rgba(255,255,255,.08);
  text-align:center;
}
.sb-logo img{max-width:120px;filter:brightness(0) invert(1);opacity:.9;}
.sb-logo span{display:block;color:rgba(255,255,255,.5);font-size:11px;margin-top:6px;letter-spacing:1px;text-transform:uppercase;}

.sb-nav{flex:1;padding:12px 0;overflow-y:auto;}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.15);border-radius:10px;}

.sb-section{
  color:rgba(255,255,255,.3);font-size:10px;
  padding:14px 20px 4px;letter-spacing:1.5px;text-transform:uppercase;
}
.sb-link{
  display:flex;align-items:center;gap:12px;
  padding:11px 20px;margin:2px 10px;border-radius:10px;
  color:rgba(255,255,255,.65);font-size:14px;font-weight:500;
  transition:.2s;cursor:pointer;
}
.sb-link i{width:18px;text-align:center;font-size:15px;}
.sb-link:hover{background:rgba(58,192,202,.15);color:#3AC0CA;}
.sb-link.active{background:linear-gradient(90deg,rgba(58,192,202,.25),rgba(58,192,202,.05));color:#fff;border-right:3px solid #3AC0CA;}
.sb-link.active i{color:#3AC0CA;}

.sb-footer{
  padding:16px 20px;border-top:1px solid rgba(255,255,255,.08);
  display:flex;align-items:center;gap:10px;
}
.sb-footer .avatar{
  width:36px;height:36px;background:var(--teal);border-radius:50%;
  display:flex;align-items:center;justify-content:center;font-size:14px;color:#fff;flex-shrink:0;
}
.sb-footer .uname{color:rgba(255,255,255,.85);font-size:13px;font-weight:600;}
.sb-footer .urole{color:rgba(255,255,255,.4);font-size:11px;}
.sb-footer .logout-btn{margin-right:auto;color:rgba(255,255,255,.4);font-size:16px;transition:.2s;}
.sb-footer .logout-btn:hover{color:#ff6b6b;}

/* ─── Topbar ─── */
.topbar{
  margin-right:var(--sidebar);height:64px;
  background:#fff;border-bottom:1px solid #e8eaed;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 28px;position:sticky;top:0;z-index:100;
  box-shadow:0 1px 4px rgba(0,0,0,.04);
}
.topbar-left{display:flex;align-items:center;gap:10px;}
.topbar-page{font-weight:700;font-size:16px;color:var(--brand);}
.topbar-right{display:flex;align-items:center;gap:12px;}
.topbar-btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;
  transition:.2s;cursor:pointer;border:none;
}
.btn-view{background:rgba(58,192,202,.1);color:var(--teal);}
.btn-view:hover{background:var(--teal);color:#fff;}
.btn-out{background:rgba(220,53,69,.08);color:#dc3545;}
.btn-out:hover{background:#dc3545;color:#fff;}

/* ─── Content ─── */
.main-content{margin-right:var(--sidebar);padding:28px;min-height:calc(100vh - 64px);}

/* ─── Stat cards ─── */
.stat-card{border-radius:var(--radius);padding:22px 20px;color:#fff;position:relative;overflow:hidden;}
.stat-card::before{content:'';position:absolute;top:-20px;left:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.08);}
.stat-card::after{content:'';position:absolute;bottom:-30px;right:-10px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.05);}
.stat-card .sc-icon{font-size:28px;opacity:.8;margin-bottom:12px;}
.stat-card .sc-num{font-size:36px;font-weight:700;line-height:1;}
.stat-card .sc-lbl{font-size:13px;opacity:.85;margin-top:4px;}

/* ─── Cards ─── */
.card{border:none;border-radius:var(--radius);box-shadow:var(--shadow);background:#fff;}
.card-header{background:#fff;border-bottom:1px solid #f0f2f5;border-radius:var(--radius) var(--radius) 0 0 !important;padding:16px 20px;}
.card-header h5,.card-header h6{color:var(--brand);font-weight:700;margin:0;}
.card-body{padding:20px;}

/* ─── Tables ─── */
.table{border-collapse:separate;border-spacing:0;}
.table thead th{background:#f7f8fc;color:var(--brand);font-weight:700;font-size:13px;border-bottom:2px solid #e8eaf0;padding:12px 16px;}
.table tbody td{padding:12px 16px;vertical-align:middle;font-size:14px;border-bottom:1px solid #f2f4f8;}
.table tbody tr:last-child td{border-bottom:none;}
.table tbody tr:hover td{background:#fafbff;}

/* ─── Badges ─── */
.badge-on{background:#e8f5e9;color:#2e7d32;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;}
.badge-off{background:#fce4ec;color:#c62828;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;}

/* ─── Forms ─── */
.form-label{font-weight:600;color:#444;font-size:14px;margin-bottom:5px;}
.form-control,.form-select{border-radius:9px;border:1.5px solid #e0e4ed;padding:9px 13px;font-family:'El Messiri',sans-serif;font-size:14px;transition:.2s;}
.form-control:focus,.form-select:focus{border-color:var(--teal);box-shadow:0 0 0 3px rgba(58,192,202,.12);outline:none;}
textarea.form-control{resize:vertical;min-height:90px;}
.img-preview{max-width:110px;max-height:80px;border-radius:9px;border:2px solid #e0e4ed;object-fit:cover;margin-top:8px;display:none;}

/* ─── Buttons ─── */
.btn-brand{background:linear-gradient(135deg,var(--brand),var(--brand2));color:#fff;border:none;border-radius:9px;padding:9px 22px;font-weight:600;font-family:'El Messiri',sans-serif;font-size:14px;transition:.2s;}
.btn-brand:hover{opacity:.88;color:#fff;}
.btn-teal{background:linear-gradient(135deg,var(--teal),var(--teal2));color:#fff;border:none;border-radius:9px;padding:9px 22px;font-weight:600;font-family:'El Messiri',sans-serif;font-size:14px;transition:.2s;}
.btn-teal:hover{opacity:.88;color:#fff;}
.btn-sm-action{width:32px;height:32px;border-radius:8px;border:1.5px solid;display:inline-flex;align-items:center;justify-content:center;font-size:13px;transition:.2s;cursor:pointer;background:transparent;}
.btn-edit{border-color:#3AC0CA;color:#3AC0CA;}
.btn-edit:hover{background:#3AC0CA;color:#fff;}
.btn-del{border-color:#e53935;color:#e53935;}
.btn-del:hover{background:#e53935;color:#fff;}
.btn-tog{border-color:#fb8c00;color:#fb8c00;}
.btn-tog:hover{background:#fb8c00;color:#fff;}

/* ─── Section title ─── */
.section-heading{font-size:15px;font-weight:700;color:var(--brand);padding-bottom:10px;border-bottom:2px solid var(--teal);margin-bottom:20px;display:inline-block;}

/* ─── Alert ─── */
.alert{border-radius:10px;border:none;padding:13px 18px;font-size:14px;}
.alert-success{background:#e8f5e9;color:#1b5e20;}
.alert-danger{background:#fce4ec;color:#880e4f;}

/* ─── Upload box ─── */
.upload-box{border:2px dashed #c8d0e0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:.2s;}
.upload-box:hover{border-color:var(--teal);background:rgba(58,192,202,.04);}
.upload-box input[type=file]{opacity:0;position:absolute;width:100%;height:100%;top:0;left:0;cursor:pointer;}
.upload-box .ub-icon{font-size:28px;color:#b0b8c8;margin-bottom:8px;}
.upload-box .ub-text{font-size:13px;color:#888;}

/* ─── Bulk Action Bar ─── */
.bulk-bar{display:none;position:fixed;bottom:0;right:var(--sidebar);left:0;background:var(--brand);color:#fff;padding:14px 24px;align-items:center;gap:14px;z-index:500;box-shadow:0 -4px 20px rgba(0,0,0,.18);}
.bulk-bar.show{display:flex;}
.bulk-count{font-weight:700;font-size:14px;background:rgba(255,255,255,.2);padding:4px 12px;border-radius:20px;}
.bulk-select{background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.35);color:#fff;border-radius:8px;padding:7px 14px;font-family:'El Messiri',sans-serif;font-size:13px;font-weight:600;}
.bulk-select option{color:#333;background:#fff;}
.bulk-apply{background:#fff;color:var(--brand);border:none;border-radius:8px;padding:8px 20px;font-weight:700;font-size:13px;font-family:'El Messiri',sans-serif;cursor:pointer;transition:.2s;}
.bulk-apply:hover{background:var(--teal);color:#fff;}
.bulk-cancel{color:rgba(255,255,255,.6);font-size:13px;cursor:pointer;margin-right:auto;background:none;border:none;font-family:'El Messiri',sans-serif;transition:.2s;}
.bulk-cancel:hover{color:#fff;}
th.check-col,td.check-col{width:40px;text-align:center;padding:8px 6px!important;}
input[type=checkbox].row-check,input[type=checkbox]#selectAll{width:16px;height:16px;cursor:pointer;accent-color:var(--teal);}

/* ─── Responsive ─── */
@media(max-width:991px){
  .sidebar{right:-var(--sidebar);transform:translateX(var(--sidebar));}
  .topbar,.main-content{margin-right:0;}
  .bulk-bar{right:0;}
}
</style>
</head>
<body>

<!-- ══════════ Sidebar ══════════ -->
<div class="sidebar" id="sidebar">
  <div class="sb-logo">
    <img src="../assets/images/logo.svg" alt="Logo">
    <span>Admin Panel</span>
  </div>

  <nav class="sb-nav">
    <div class="sb-section">القائمة الرئيسية</div>

    <?php foreach ($navItems as $item):
      $files = [$item['file']];
      if (!empty($item['edit']))  $files[] = $item['edit'];
      if (!empty($item['edit2'])) $files[] = $item['edit2'];
      $isActive = in_array($currentPage, $files);
    ?>
    <a href="<?= $item['file'] ?>" class="sb-link <?= $isActive ? 'active' : '' ?>">
      <i class="<?= $item['icon'] ?>"></i>
      <span><?= $item['label'] ?></span>
      <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
      <span class="ms-auto badge bg-warning text-dark" style="font-size:10px;padding:3px 7px;border-radius:20px;"><?= $item['badge'] ?></span>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>

    <div class="sb-section">روابط</div>
    <a href="../index.php" target="_blank" class="sb-link">
      <i class="fas fa-globe"></i><span>عرض الموقع</span>
    </a>
  </nav>

  <div class="sb-footer">
    <div class="avatar"><i class="fas fa-user" style="font-size:14px;"></i></div>
    <div>
      <div class="uname"><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></div>
      <div class="urole">مدير النظام</div>
    </div>
    <a href="logout.php" class="logout-btn" title="تسجيل الخروج"><i class="fas fa-sign-out-alt"></i></a>
  </div>
</div>

<!-- ══════════ Topbar ══════════ -->
<div class="topbar">
  <div class="topbar-left">
    <i class="fas fa-leaf" style="color:var(--teal);font-size:18px;"></i>
    <span class="topbar-page"><?= htmlspecialchars($pageTitle) ?></span>
  </div>
  <div class="topbar-right">
    <a href="../index.php" target="_blank" class="topbar-btn btn-view">
      <i class="fas fa-eye"></i> عرض الموقع
    </a>
    <a href="logout.php" class="topbar-btn btn-out">
      <i class="fas fa-sign-out-alt"></i> خروج
    </a>
  </div>
</div>

<!-- ══════════ Main Content ══════════ -->
<div class="main-content">

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4" role="alert">
  <i class="fas fa-<?= $flash['type']==='success'?'check-circle':'exclamation-circle' ?> me-2"></i>
  <?= htmlspecialchars($flash['msg']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
