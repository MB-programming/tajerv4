<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

// Cache clear handler
if (isset($_POST['clear_cache'])) {
    $cleared = array();
    // PHP OPcache
    if (function_exists('opcache_reset')) {
        opcache_reset();
        $cleared[] = 'OPcache';
    }
    // APCu
    if (function_exists('apcu_clear_cache')) {
        apcu_clear_cache();
        $cleared[] = 'APCu';
    }
    // APC
    if (function_exists('apc_clear_cache')) {
        apc_clear_cache('user');
        apc_clear_cache('opcode');
        $cleared[] = 'APC';
    }
    // File cache folder (if exists)
    $cacheDir = __DIR__ . '/../../cache/';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '*.{php,html,cache}', GLOB_BRACE);
        if ($files) {
            foreach ($files as $f) { @unlink($f); }
            $cleared[] = 'File Cache (' . count($files) . ' ملف)';
        }
    }
    $msg = empty($cleared) ? 'لم يُعثر على كاش قابل للمسح (OPcache معطّل على السيرفر)' : 'تم مسح: ' . implode(' + ', $cleared);
    $_SESSION['flash'] = array('msg' => $msg, 'type' => empty($cleared) ? 'error' : 'success');
    header('Location: dashboard.php'); exit;
}

$counts = array(
    'products' => db()->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn(),
    'branches' => db()->query("SELECT COUNT(*) FROM branches WHERE status='active'")->fetchColumn(),
    'teams'    => db()->query("SELECT COUNT(*) FROM teams WHERE status='active'")->fetchColumn(),
    'partners' => db()->query("SELECT COUNT(*) FROM partners WHERE status='active'")->fetchColumn(),
    'numbers'  => db()->query("SELECT COUNT(*) FROM numbers WHERE status='active'")->fetchColumn(),
    'messages' => db()->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn(),
);
$recentMessages = db()->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 6")->fetchAll();
$pageTitle = 'لوحة التحكم';
require_once 'includes/header.php';
?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
<?php
$stats = array(
  array('label'=>'منتج',         'count'=>$counts['products'], 'icon'=>'fa-seedling',       'grad'=>'linear-gradient(135deg,#35285C,#5a3e99)'),
  array('label'=>'فرع',          'count'=>$counts['branches'], 'icon'=>'fa-map-marker-alt', 'grad'=>'linear-gradient(135deg,#3AC0CA,#1e8a9a)'),
  array('label'=>'عضو فريق',    'count'=>$counts['teams'],    'icon'=>'fa-users',           'grad'=>'linear-gradient(135deg,#7B5EA7,#5a3e88)'),
  array('label'=>'شريك',        'count'=>$counts['partners'], 'icon'=>'fa-handshake',       'grad'=>'linear-gradient(135deg,#43a047,#2e7d32)'),
  array('label'=>'إحصائية',     'count'=>$counts['numbers'],  'icon'=>'fa-chart-pie',       'grad'=>'linear-gradient(135deg,#fb8c00,#e65100)'),
  array('label'=>'رسالة جديدة', 'count'=>$counts['messages'], 'icon'=>'fa-envelope',        'grad'=>'linear-gradient(135deg,#e53935,#b71c1c)'),
);
foreach ($stats as $st): ?>
<div class="col-6 col-md-4 col-lg-2">
  <div class="stat-card" style="background:<?= $st['grad'] ?>;">
    <div class="sc-icon"><i class="fas <?= $st['icon'] ?>"></i></div>
    <div class="sc-num"><?= $st['count'] ?></div>
    <div class="sc-lbl"><?= $st['label'] ?></div>
  </div>
</div>
<?php endforeach; ?>
</div>

<div class="row g-4">
  <!-- Quick links + Cache Clear -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-bolt" style="color:var(--teal);"></i>
        <h6>وصول سريع</h6>
      </div>
      <div class="card-body p-2">
        <?php
        $links = array(
          array('settings.php',  'fas fa-sliders-h',    'الإعدادات العامة',  '#35285C'),
          array('branches.php',  'fas fa-map-marker-alt','الفروع',            '#3AC0CA'),
          array('products.php',  'fas fa-seedling',      'المنتجات',          '#43a047'),
          array('teams.php',     'fas fa-users',         'الفريق',            '#7B5EA7'),
          array('partners.php',  'fas fa-handshake',     'الشركاء',           '#fb8c00'),
          array('numbers.php',   'fas fa-chart-pie',     'الإحصائيات',        '#17a2b8'),
          array('messages.php',  'fas fa-envelope',      'الرسائل',           '#e53935'),
        );
        foreach ($links as $l):
            list($href, $icon, $lbl, $color) = $l;
        ?>
        <a href="<?= $href ?>" class="d-flex align-items-center gap-3 p-3 rounded-3 mb-1" style="background:#f7f8fc;color:#333;transition:.2s;" onmouseover="this.style.background='#eef0f8'" onmouseout="this.style.background='#f7f8fc'">
          <span style="width:32px;height:32px;background:<?= $color ?>22;border-radius:8px;display:flex;align-items:center;justify-content:center;">
            <i class="<?= $icon ?>" style="color:<?= $color ?>;font-size:14px;"></i>
          </span>
          <span style="font-size:14px;font-weight:600;"><?= $lbl ?></span>
          <i class="fas fa-chevron-left ms-auto" style="font-size:11px;color:#bbb;"></i>
        </a>
        <?php endforeach; ?>

        <!-- Cache Clear Button -->
        <div style="border-top:1px solid #f0f2f5;margin-top:8px;padding-top:8px;">
          <form method="post" onsubmit="return confirm('مسح الكاش؟ الصفحة ستُحدَّث.');">
            <input type="hidden" name="clear_cache" value="1">
            <button type="submit" class="d-flex align-items-center gap-3 p-3 rounded-3 w-100" style="background:#fff3cd;color:#7a5800;border:none;font-family:inherit;cursor:pointer;transition:.2s;" onmouseover="this.style.background='#ffe69c'" onmouseout="this.style.background='#fff3cd'">
              <span style="width:32px;height:32px;background:#ffc10733;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-broom" style="color:#e65100;font-size:14px;"></i>
              </span>
              <span style="font-size:14px;font-weight:600;">مسح الكاش</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent messages -->
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <i class="fas fa-envelope" style="color:var(--teal);"></i>
          <h6>آخر الرسائل</h6>
        </div>
        <a href="messages.php" class="topbar-btn btn-teal" style="font-size:12px;padding:5px 14px;">عرض الكل</a>
      </div>
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead><tr><th>الاسم</th><th>البريد</th><th>الرسالة</th><th>التاريخ</th></tr></thead>
          <tbody>
          <?php foreach ($recentMessages as $m): ?>
          <tr>
            <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
            <td style="font-size:12px;color:#666;"><?= htmlspecialchars($m['email']) ?></td>
            <td style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:13px;">
              <?php if (!$m['is_read']): ?><span class="badge-on" style="font-size:10px;padding:2px 7px;margin-left:4px;">جديد</span><?php endif; ?>
              <?= htmlspecialchars($m['message']) ?>
            </td>
            <td style="font-size:12px;color:#999;white-space:nowrap;"><?= date('d/m/Y', strtotime($m['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$recentMessages): ?>
          <tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block" style="color:#ddd;"></i>لا توجد رسائل</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
