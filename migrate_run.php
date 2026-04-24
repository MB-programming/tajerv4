<?php
/**
 * =====================================================
 * سكريبت نقل البيانات من قاعدة البيانات القديمة
 * =====================================================
 * 1. عدّل البيانات أدناه
 * 2. ارفع الملف على السيرفر في نفس مجلد الموقع
 * 3. افتحه في المتصفح مرة واحدة
 * 4. احذفه فوراً بعد الانتهاء!
 * =====================================================
 */

// ===================================================
// ⚙️ عدّل هنا فقط
// ===================================================
$OLD_DB_HOST = 'localhost';
$OLD_DB_NAME = 'tajagri_OLD';    // ← اسم قاعدة بياناتك القديمة
$OLD_DB_USER = 'tajagri';        // ← يوزر MySQL القديم
$OLD_DB_PASS = '';               // ← باسورد MySQL القديم

$NEW_DB_HOST = 'localhost';
$NEW_DB_NAME = 'tajagri_tajerv1'; // ← قاعدة البيانات الجديدة
$NEW_DB_USER = 'tajagri';         // ← يوزر MySQL الجديد
$NEW_DB_PASS = '';                // ← باسورد MySQL الجديد
// ===================================================

set_time_limit(120);
header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html><html dir="rtl" lang="ar"><head>
<meta charset="utf-8">
<style>
  body{font-family:Arial,sans-serif;padding:30px;background:#f5f5f5;}
  .box{background:#fff;padding:20px;border-radius:10px;max-width:800px;margin:auto;box-shadow:0 2px 10px rgba(0,0,0,.1);}
  h2{color:#35285C;border-bottom:2px solid #3AC0CA;padding-bottom:10px;}
  .ok{color:#28a745;font-weight:bold;}
  .err{color:#dc3545;font-weight:bold;}
  .info{color:#666;font-size:13px;margin:4px 0;}
  .section{margin:20px 0;padding:15px;background:#f8f9fa;border-radius:8px;border-right:4px solid #3AC0CA;}
  .done{background:#d4edda;border-color:#28a745;padding:20px;border-radius:8px;margin-top:20px;text-align:center;font-size:18px;}
  .warn{background:#fff3cd;border:1px solid #ffc107;padding:15px;border-radius:8px;margin-top:20px;}
</style>
</head><body><div class="box">';

echo '<h2>🔄 نقل البيانات من القاعدة القديمة</h2>';

// Connect to OLD database
try {
    $old = new PDO("mysql:host=$OLD_DB_HOST;dbname=$OLD_DB_NAME;charset=utf8mb4",
        $OLD_DB_USER, $OLD_DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo '<p class="ok">✅ اتصال بقاعدة البيانات القديمة: OK</p>';
} catch (PDOException $e) {
    echo '<p class="err">❌ فشل الاتصال بالقاعدة القديمة: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div></body></html>'; exit;
}

// Connect to NEW database
try {
    $new = new PDO("mysql:host=$NEW_DB_HOST;dbname=$NEW_DB_NAME;charset=utf8mb4",
        $NEW_DB_USER, $NEW_DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo '<p class="ok">✅ اتصال بقاعدة البيانات الجديدة: OK</p>';
} catch (PDOException $e) {
    echo '<p class="err">❌ فشل الاتصال بالقاعدة الجديدة: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div></body></html>'; exit;
}

$new->exec("SET NAMES utf8mb4");
$old->exec("SET NAMES utf8mb4");

// =====================================================
// دالة مساعدة للإدراج
// =====================================================
function insertRows($new, $table, $rows) {
    if (empty($rows)) return 0;
    $cols  = array_keys($rows[0]);
    $colStr = '`' . implode('`, `', $cols) . '`';
    $ph    = '(' . implode(', ', array_fill(0, count($cols), '?')) . ')';
    $stmt  = $new->prepare("INSERT IGNORE INTO `$table` ($colStr) VALUES $ph");
    $count = 0;
    foreach ($rows as $row) {
        $stmt->execute(array_values($row));
        $count++;
    }
    return $count;
}

// =====================================================
// 1. الإعدادات
// =====================================================
echo '<div class="section"><strong>⚙️ الإعدادات العامة</strong><br>';
try {
    $s  = $old->query("SELECT * FROM settings WHERE id=1")->fetch(PDO::FETCH_ASSOC);
    $st = $old->query("SELECT * FROM setting_translations WHERE setting_id=1")->fetchAll(PDO::FETCH_ASSOC);

    $data = ['id'=>1];
    foreach (['logo','home_image','about_image','branch_image','footer_image','address','post_address','phone','mobile','email','website'] as $col) {
        $data[$col] = $s[$col] ?? null;
    }
    $data['about_button_url'] = $s['about_button_url'] ?? null;

    foreach ($st as $t) {
        $l = $t['locale'];
        foreach (['about','vision','message','product_description','numbers_description','team_description','description','about_button_text'] as $col) {
            if (isset($t[$col])) $data[$col . '_' . $l] = $t[$col];
        }
    }

    $new->exec("DELETE FROM settings WHERE id=1");
    $cols  = '`' . implode('`, `', array_keys($data)) . '`';
    $ph    = implode(', ', array_fill(0, count($data), '?'));
    $new->prepare("INSERT INTO settings ($cols) VALUES ($ph)")->execute(array_values($data));
    echo '<span class="ok">✅ تم نقل الإعدادات</span>';
} catch (Exception $e) {
    echo '<span class="err">❌ ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

// =====================================================
// 2. المنتجات
// =====================================================
echo '<div class="section"><strong>🌱 المنتجات</strong><br>';
try {
    $products = $old->query("SELECT * FROM products WHERE deleted_at IS NULL ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $trans    = $old->query("SELECT * FROM product_translations ORDER BY product_id")->fetchAll(PDO::FETCH_ASSOC);

    $tMap = [];
    foreach ($trans as $t) $tMap[$t['product_id']][$t['locale']] = $t;

    $new->exec("TRUNCATE TABLE products");
    $count = 0;
    foreach ($products as $i => $p) {
        $row = [
            'id'         => $p['id'],
            'name_ar'    => $tMap[$p['id']]['ar']['name'] ?? '',
            'name_en'    => $tMap[$p['id']]['en']['name'] ?? '',
            'image'      => $p['image'] ?? null,
            'url'        => $tMap[$p['id']]['ar']['url'] ?? ($p['url'] ?? null),
            'sort_order' => $i + 1,
            'status'     => $p['status'],
            'created_at' => $p['created_at'],
            'updated_at' => $p['updated_at'],
        ];
        $new->prepare("INSERT IGNORE INTO products (id,name_ar,name_en,image,url,sort_order,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute(array_values($row));
        $count++;
    }
    echo "<span class=\"ok\">✅ تم نقل $count منتج</span>";
} catch (Exception $e) {
    echo '<span class="err">❌ ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

// =====================================================
// 3. الأرقام
// =====================================================
echo '<div class="section"><strong>📊 الأرقام والإحصائيات</strong><br>';
try {
    $numbers = $old->query("SELECT * FROM numbers WHERE deleted_at IS NULL ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $trans   = $old->query("SELECT * FROM number_translations ORDER BY number_id")->fetchAll(PDO::FETCH_ASSOC);

    $tMap = [];
    foreach ($trans as $t) $tMap[$t['number_id']][$t['locale']] = $t;

    $new->exec("TRUNCATE TABLE numbers");
    $count = 0;
    foreach ($numbers as $i => $n) {
        $new->prepare("INSERT IGNORE INTO numbers (id,number,description_ar,description_en,sort_order,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([
                $n['id'],
                $n['number'],
                $tMap[$n['id']]['ar']['description'] ?? '',
                $tMap[$n['id']]['en']['description'] ?? '',
                $i + 1,
                $n['status'],
                $n['created_at'],
                $n['updated_at'],
            ]);
        $count++;
    }
    echo "<span class=\"ok\">✅ تم نقل $count رقم</span>";
} catch (Exception $e) {
    echo '<span class="err">❌ ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

// =====================================================
// 4. الفريق
// =====================================================
echo '<div class="section"><strong>👥 الفريق</strong><br>';
try {
    $teams = $old->query("SELECT * FROM teams WHERE deleted_at IS NULL ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $trans = $old->query("SELECT * FROM team_translations ORDER BY team_id")->fetchAll(PDO::FETCH_ASSOC);

    $tMap = [];
    foreach ($trans as $t) $tMap[$t['team_id']][$t['locale']] = $t;

    $new->exec("TRUNCATE TABLE teams");
    $count = 0;
    foreach ($teams as $i => $tm) {
        $new->prepare("INSERT IGNORE INTO teams (id,name_ar,name_en,description_ar,description_en,mobile,email,image,sort_order,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([
                $tm['id'],
                $tMap[$tm['id']]['ar']['name']        ?? '',
                $tMap[$tm['id']]['en']['name']        ?? '',
                $tMap[$tm['id']]['ar']['description'] ?? '',
                $tMap[$tm['id']]['en']['description'] ?? '',
                $tm['mobile'] ?? '',
                $tm['email']  ?? '',
                $tm['image']  ?? null,
                $i + 1,
                $tm['status'],
                $tm['created_at'],
                $tm['updated_at'],
            ]);
        $count++;
    }
    echo "<span class=\"ok\">✅ تم نقل $count عضو</span>";
} catch (Exception $e) {
    echo '<span class="err">❌ ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

// =====================================================
// 5. الشركاء
// =====================================================
echo '<div class="section"><strong>🤝 الشركاء</strong><br>';
try {
    $partners = $old->query("SELECT * FROM partners WHERE deleted_at IS NULL ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $new->exec("TRUNCATE TABLE partners");
    $count = 0;
    foreach ($partners as $i => $pt) {
        $new->prepare("INSERT IGNORE INTO partners (id,image,sort_order,status,created_at,updated_at) VALUES (?,?,?,?,?,?)")
            ->execute([$pt['id'], $pt['image'], $i+1, $pt['status'], $pt['created_at'], $pt['updated_at']]);
        $count++;
    }
    echo "<span class=\"ok\">✅ تم نقل $count شريك</span>";
} catch (Exception $e) {
    echo '<span class="err">❌ ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

// =====================================================
// 6. الفروع
// =====================================================
echo '<div class="section"><strong>📍 الفروع</strong><br>';
try {
    $branches = $old->query("SELECT * FROM branches WHERE deleted_at IS NULL ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $trans    = $old->query("SELECT * FROM branch_translations ORDER BY branch_id")->fetchAll(PDO::FETCH_ASSOC);

    $tMap = [];
    foreach ($trans as $t) $tMap[$t['branch_id']][$t['locale']] = $t;

    $new->exec("TRUNCATE TABLE branches");
    $count = 0;
    foreach ($branches as $i => $b) {
        $new->prepare("INSERT IGNORE INTO branches (id,name_ar,name_en,phone,map_url,address_ar,address_en,working_hours_ar,working_hours_en,sort_order,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([
                $b['id'],
                $tMap[$b['id']]['ar']['name']          ?? '',
                $tMap[$b['id']]['en']['name']          ?? '',
                $b['phone']   ?? null,
                $b['map_url'] ?? null,
                $tMap[$b['id']]['ar']['address']       ?? null,
                $tMap[$b['id']]['en']['address']       ?? null,
                $tMap[$b['id']]['ar']['working_hours'] ?? null,
                $tMap[$b['id']]['en']['working_hours'] ?? null,
                $i + 1,
                $b['status'],
                $b['created_at'],
                $b['updated_at'],
            ]);
        $count++;
    }
    echo "<span class=\"ok\">✅ تم نقل $count فرع</span>";
} catch (Exception $e) {
    echo '<span class="err">❌ ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

// =====================================================
// 7. رسائل التواصل
// =====================================================
echo '<div class="section"><strong>✉️ رسائل التواصل</strong><br>';
try {
    $msgs = $old->query("SELECT * FROM connect_us ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $new->exec("TRUNCATE TABLE contact_messages");
    $count = 0;
    foreach ($msgs as $m) {
        $new->prepare("INSERT IGNORE INTO contact_messages (id,name,email,message,is_read,created_at) VALUES (?,?,?,?,?,?)")
            ->execute([$m['id'], $m['name'], $m['email'], $m['text'], $m['read'], $m['created_at']]);
        $count++;
    }
    echo "<span class=\"ok\">✅ تم نقل $count رسالة</span>";
} catch (Exception $e) {
    echo '<span class="err">❌ ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

// =====================================================
// Done
// =====================================================
echo '<div class="done">🎉 اكتمل نقل البيانات بنجاح!</div>';
echo '<div class="warn">
  ⚠️ <strong>مهم جداً:</strong> احذف هذا الملف <code>migrate_run.php</code> فوراً من السيرفر!
  <br><br>
  <a href="admin/login.php" style="background:#35285C;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:bold;">← الدخول للوحة التحكم</a>
  &nbsp;
  <a href="index.php" style="background:#3AC0CA;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:bold;">← عرض الموقع</a>
</div>';

echo '</div></body></html>';
