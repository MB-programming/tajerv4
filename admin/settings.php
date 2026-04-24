<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

// Ensure new columns exist (runs silently if already present)
foreach (['head_code TEXT', 'body_code TEXT', 'nav_json TEXT',
          'address_en VARCHAR(255)', 'post_address_en VARCHAR(255)'] as $_c) {
    try { db()->exec("ALTER TABLE settings ADD COLUMN $_c DEFAULT NULL"); } catch(Throwable $e) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = db()->query('SELECT * FROM settings WHERE id=1')->fetch();

    $fields = ['address','address_en','post_address','post_address_en','phone','mobile','email','website','about_button_url',
               'about_ar','about_en','vision_ar','vision_en','message_ar','message_en',
               'product_description_ar','product_description_en','numbers_description_ar','numbers_description_en',
               'team_description_ar','team_description_en','description_ar','description_en',
               'about_button_text_ar','about_button_text_en',
               'head_code','body_code','nav_json'];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = trim($_POST[$f] ?? '');
    }

    $imgFields = ['logo','home_image','about_image','branch_image','footer_image'];
    foreach ($imgFields as $f) {
        if (!empty($_FILES[$f]['name'])) {
            $uploaded = uploadFile($_FILES[$f], 'uploads');
            if ($uploaded) $data[$f] = $uploaded;
        }
    }

    if ($s) {
        $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($data)));
        db()->prepare("UPDATE settings SET $sets WHERE id=1")->execute(array_values($data));
    } else {
        $data['id'] = 1;
        $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $vals = implode(', ', array_fill(0, count($data), '?'));
        db()->prepare("INSERT INTO settings ($cols) VALUES ($vals)")->execute(array_values($data));
    }

    $_SESSION['flash'] = ['msg' => 'تم حفظ الإعدادات بنجاح', 'type' => 'success'];
    header('Location: settings.php'); exit;
}

$s = db()->query('SELECT * FROM settings WHERE id=1')->fetch() ?: [];
$pageTitle = 'الإعدادات العامة';
require_once 'includes/header.php';
?>

<form method="post" enctype="multipart/form-data">
<div class="row">
  <!-- Images -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header"><h5><i class="fas fa-images" style="color:#3AC0CA;margin-left:8px;"></i>الصور</h5></div>
      <div class="card-body">
        <div class="row">
          <?php
          $imgFields = [
            'logo'         => 'الشعار',
            'home_image'   => 'صورة الرئيسية',
            'about_image'  => 'صورة قسم عن الشركة',
            'branch_image' => 'صورة قسم الفروع',
            'footer_image' => 'صورة الفوتر',
          ];
          foreach ($imgFields as $field => $label):
            $current = $s[$field] ?? '';
          ?>
          <div class="col-md-4 col-lg-2 mb-3">
            <div class="form-group">
              <label class="form-label"><?= $label ?></label>
              <input type="file" name="<?= $field ?>" class="form-control form-control-sm" accept="image/*">
              <?php if ($current): ?>
              <img src="../<?= htmlspecialchars($current) ?>" class="img-preview" style="display:block;margin-top:8px;">
              <?php else: ?>
              <img class="img-preview" style="display:none;margin-top:8px;">
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Info -->
  <div class="col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header"><h5><i class="fas fa-phone" style="color:#3AC0CA;margin-left:8px;"></i>بيانات التواصل</h5></div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="form-label">العنوان (عربي)</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($s['address'] ?? '') ?>">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="form-label">العنوان (إنجليزي)</label>
            <input type="text" name="address_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($s['address_en'] ?? '') ?>">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="form-label">صندوق البريد (عربي)</label>
            <input type="text" name="post_address" class="form-control" value="<?= htmlspecialchars($s['post_address'] ?? '') ?>">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="form-label">صندوق البريد (إنجليزي)</label>
            <input type="text" name="post_address_en" class="form-control" dir="ltr" value="<?= htmlspecialchars($s['post_address_en'] ?? '') ?>">
          </div>
        </div>
        <?php
        $contactFields = ['phone'=>'الهاتف','mobile'=>'الجوال','email'=>'البريد الإلكتروني','website'=>'الموقع الإلكتروني'];
        foreach ($contactFields as $f => $l): ?>
        <div class="form-group mb-3">
          <label class="form-label"><?= $l ?></label>
          <input type="text" name="<?= $f ?>" class="form-control" value="<?= htmlspecialchars($s[$f] ?? '') ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- About button -->
  <div class="col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header"><h5><i class="fas fa-link" style="color:#3AC0CA;margin-left:8px;"></i>زرار قسم "عن الشركة"</h5></div>
      <div class="card-body">
        <div class="form-group mb-3">
          <label class="form-label">نص الزرار (عربي)</label>
          <input type="text" name="about_button_text_ar" class="form-control" placeholder="تعرف على المزيد" value="<?= htmlspecialchars($s['about_button_text_ar'] ?? '') ?>">
        </div>
        <div class="form-group mb-3">
          <label class="form-label">نص الزرار (إنجليزي)</label>
          <input type="text" name="about_button_text_en" class="form-control" placeholder="Discover More" value="<?= htmlspecialchars($s['about_button_text_en'] ?? '') ?>">
        </div>
        <div class="form-group mb-3">
          <label class="form-label">رابط الزرار (URL)</label>
          <input type="text" name="about_button_url" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($s['about_button_url'] ?? '') ?>">
          <small class="text-muted">اتركه فارغاً لإخفاء الزرار</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Translatable texts -->
  <?php
  $textGroups = [
    'عن الشركة'            => ['about_ar'                => 'النص (عربي)',  'about_en'                => 'النص (إنجليزي)'],
    'الرؤية'               => ['vision_ar'               => 'النص (عربي)',  'vision_en'               => 'النص (إنجليزي)'],
    'الرسالة'              => ['message_ar'              => 'النص (عربي)',  'message_en'              => 'النص (إنجليزي)'],
    'وصف المنتجات'         => ['product_description_ar'  => 'النص (عربي)',  'product_description_en'  => 'النص (إنجليزي)'],
    'وصف الأرقام'          => ['numbers_description_ar'  => 'النص (عربي)',  'numbers_description_en'  => 'النص (إنجليزي)'],
    'وصف الفريق'           => ['team_description_ar'     => 'النص (عربي)',  'team_description_en'     => 'النص (إنجليزي)'],
    'وصف الفوتر'           => ['description_ar'          => 'النص (عربي)',  'description_en'          => 'النص (إنجليزي)'],
  ];
  foreach ($textGroups as $groupTitle => $fields):
  ?>
  <div class="col-md-6 mb-4">
    <div class="card">
      <div class="card-header"><h5><?= $groupTitle ?></h5></div>
      <div class="card-body">
        <?php foreach ($fields as $f => $l): ?>
        <div class="form-group mb-3">
          <label class="form-label"><?= $l ?></label>
          <textarea name="<?= $f ?>" class="form-control" rows="3"><?= htmlspecialchars($s[$f] ?? '') ?></textarea>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

  <!-- Nav Items -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header"><h5><i class="fas fa-bars" style="color:#3AC0CA;margin-left:8px;"></i>عناصر القائمة الرئيسية</h5></div>
      <div class="card-body">
        <p class="text-muted" style="font-size:13px;margin-bottom:12px;">ترتيب العناصر من أعلى لأسفل هو ترتيب الظهور في القائمة. عناصر «زرار» تظهر بنمط الزرار.</p>
        <div class="table-responsive">
        <table class="table table-sm" id="navTable">
          <thead>
            <tr>
              <th style="width:30px;"></th>
              <th>النص (عربي)</th>
              <th>النص (إنجليزي)</th>
              <th>الرابط</th>
              <th style="width:70px;text-align:center;">زرار</th>
              <th style="width:70px;text-align:center;">مُفعّل</th>
              <th style="width:50px;"></th>
            </tr>
          </thead>
          <tbody id="navItemsBody"></tbody>
        </table>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addNavRow()">
          <i class="fas fa-plus me-1"></i> إضافة عنصر
        </button>
        <input type="hidden" name="nav_json" id="navJsonInput">
      </div>
    </div>
  </div>

  <!-- Head / Body Code -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header"><h5><i class="fas fa-code" style="color:#3AC0CA;margin-left:8px;"></i>كود مخصص (Head / Body)</h5></div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">كود &lt;head&gt; <small class="text-muted">(Google Analytics, Meta tags…)</small></label>
            <textarea name="head_code" class="form-control" rows="6" style="font-family:monospace;font-size:13px;"><?= htmlspecialchars($s['head_code'] ?? '') ?></textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">كود &lt;body&gt; <small class="text-muted">(Chat widgets, tracking scripts…)</small></label>
            <textarea name="body_code" class="form-control" rows="6" style="font-family:monospace;font-size:13px;"><?= htmlspecialchars($s['body_code'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="text-left mb-4">
  <button type="submit" class="btn btn-primary btn-lg px-5">
    <i class="fas fa-save ml-2"></i> حفظ الإعدادات
  </button>
</div>
</form>

<script>
var navData = <?= json_encode(json_decode($s['nav_json'] ?? '[]', true) ?: []) ?>;
if (!Array.isArray(navData) || !navData.length) {
    navData = [
        {label_ar:'منتجاتنا',         label_en:'Our Products',  href:'#products',      enabled:true, is_button:false},
        {label_ar:'شركاؤنا',           label_en:'Our Partners',  href:'#partners',      enabled:true, is_button:false},
        {label_ar:'فروعنا',            label_en:'Our Branches',  href:'#branches',      enabled:true, is_button:false},
        {label_ar:'عن تاج',            label_en:'About Taj Agri',href:'#about-section', enabled:true, is_button:false},
        {label_ar:'تواصل معنا',        label_en:'Contact Us',    href:'#footer',        enabled:true, is_button:false},
        {label_ar:'المتجر الإلكتروني',label_en:'Online Store',  href:'https://tajagri.sa/', enabled:true, is_button:true}
    ];
}

function escVal(v) { return (v||'').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }
function makeRow(item) {
    return '<tr draggable="true" ondragstart="dragStart(event)" ondragover="dragOver(event)" ondrop="dropRow(event)">'
        + '<td style="cursor:grab;color:#bbb;text-align:center;">⠿</td>'
        + '<td><input type="text" class="form-control form-control-sm nav-ar" value="' + escVal(item.label_ar) + '"></td>'
        + '<td><input type="text" class="form-control form-control-sm nav-en" value="' + escVal(item.label_en) + '"></td>'
        + '<td><input type="text" class="form-control form-control-sm nav-href" value="' + escVal(item.href) + '"></td>'
        + '<td style="text-align:center;"><input type="checkbox" class="nav-btn" style="width:16px;height:16px;" ' + (item.is_button ? 'checked' : '') + '></td>'
        + '<td style="text-align:center;"><input type="checkbox" class="nav-enabled" style="width:16px;height:16px;" ' + (item.enabled !== false ? 'checked' : '') + '></td>'
        + '<td><button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" onclick="removeRow(this)" title="حذف"><i class="fas fa-trash"></i></button></td>'
        + '</tr>';
}
function renderNav() {
    var tbody = document.getElementById('navItemsBody');
    tbody.innerHTML = navData.map(makeRow).join('');
}
function addNavRow() {
    navData.push({label_ar:'',label_en:'',href:'',enabled:true,is_button:false});
    renderNav();
}
function removeRow(btn) {
    var row = btn.closest('tr');
    var idx = Array.from(row.parentNode.children).indexOf(row);
    navData.splice(idx,1);
    renderNav();
}
function collectNav() {
    var rows = document.querySelectorAll('#navItemsBody tr');
    var result = [];
    rows.forEach(function(r) {
        result.push({
            label_ar:  r.querySelector('.nav-ar').value,
            label_en:  r.querySelector('.nav-en').value,
            href:      r.querySelector('.nav-href').value,
            is_button: r.querySelector('.nav-btn').checked,
            enabled:   r.querySelector('.nav-enabled').checked
        });
    });
    return result;
}

var dragSrc = null;
function dragStart(e) { dragSrc = e.currentTarget; }
function dragOver(e)  { e.preventDefault(); }
function dropRow(e) {
    e.preventDefault();
    if (!dragSrc || dragSrc === e.currentTarget) return;
    var rows = Array.from(document.querySelectorAll('#navItemsBody tr'));
    var from = rows.indexOf(dragSrc), to = rows.indexOf(e.currentTarget);
    if (from < 0 || to < 0) return;
    navData = collectNav();
    var moved = navData.splice(from, 1)[0];
    navData.splice(to, 0, moved);
    renderNav();
}

document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('navJsonInput').value = JSON.stringify(collectNav());
});

renderNav();
</script>

<?php require_once 'includes/footer.php'; ?>
