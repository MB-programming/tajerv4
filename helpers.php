<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function lang() {
    return $_SESSION['lang'] ?? 'ar';
}

function isRtl() {
    return lang() === 'ar';
}

function t($key) {
    // Returns the correct language key from a row: $row['name_ar'] or $row['name_en']
    return lang() === 'ar' ? ($key . '_ar') : ($key . '_en');
}

function col($prefix) {
    // Return column name with lang suffix: e.g. col('name') => 'name_ar'
    return $prefix . '_' . lang();
}

function val($row, $prefix) {
    $col = $prefix . '_' . lang();
    return $row[$col] ?? ($row[$prefix . '_ar'] ?? '');
}

function setting() {
    static $setting = null;
    if ($setting === null) {
        $setting = db()->query('SELECT * FROM settings WHERE id=1')->fetch();
    }
    return $setting;
}

function uploadFile($file, $folder = 'uploads') {
    $dir = __DIR__ . '/assets/' . $folder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','svg','webp'];
    if (!in_array($ext, $allowed)) return null;
    $name = uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $dir . $name)) {
        return 'assets/' . $folder . '/' . $name;
    }
    return null;
}

function imgUrl($path) {
    if (!$path) return '';
    if (substr($path, 0, 4) === 'http') return $path;
    return BASE_URL . '/' . ltrim($path, '/');
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function flash($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function getFlash() {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function labels() {
    $l = lang();
    return [
        'Our Product home'     => $l === 'ar' ? 'منتجاتنا' : 'Our Products',
        'Our Partners'         => $l === 'ar' ? 'شركاؤنا' : 'Our Partners',
        'Our branches '        => $l === 'ar' ? 'فروعنا' : 'Our Branches',
        'Get to know Taj Agri '=> $l === 'ar' ? 'تعرف على تاج الزراعية' : 'Get to know Taj Agri',
        'Call us'              => $l === 'ar' ? 'اتصل بنا' : 'Call us',
        'Online store'         => $l === 'ar' ? 'المتجر الالكتروني' : 'Online store',
        'Numbers Target'       => $l === 'ar' ? 'أرقام حققناها' : 'Numbers We Achieved',
        'About the company'    => $l === 'ar' ? 'عن الشركة' : 'About the Company',
        'Our vision'           => $l === 'ar' ? 'رؤيتنا' : 'Our Vision',
        'Message we believe in'=> $l === 'ar' ? 'رسالة نؤمن بها' : 'Message We Believe In',
        'Our products'         => $l === 'ar' ? 'منتجاتنا' : 'Our Products',
        'Our team'             => $l === 'ar' ? 'فريقنا' : 'Our Team',
        'Proud of being in partnership with '=> $l === 'ar' ? 'نفخر بالشراكة مع' : 'Proud of being in partnership with',
        'Our branches around the Kingdom'    => $l === 'ar' ? 'فروعنا حول المملكة' : 'Our Branches Across the Kingdom',
        'Get Directions'       => $l === 'ar' ? 'احصل على الاتجاهات' : 'Get Directions',
        'Mail box'             => $l === 'ar' ? 'ص.ب' : 'Mail box',
        'Phone'                => $l === 'ar' ? 'الهاتف' : 'Phone',
        'Mobile'               => $l === 'ar' ? 'الجوال' : 'Mobile',
        'Email'                => $l === 'ar' ? 'البريد الإلكتروني' : 'Email',
        'Website'              => $l === 'ar' ? 'الموقع' : 'Website',
        'Contact us'           => $l === 'ar' ? 'اتصل بنا' : 'Contact us',
        'Name'                 => $l === 'ar' ? 'الاسم' : 'Name',
        'E-mail'               => $l === 'ar' ? 'البريد الإلكتروني' : 'E-mail',
        'Message text'         => $l === 'ar' ? 'نص الرسالة' : 'Message',
        'send'                 => $l === 'ar' ? 'إرسال' : 'Send',
        'All rights reserved'  => $l === 'ar' ? 'جميع الحقوق محفوظة لشركة تاج الزراعية 2024' : 'All rights reserved to Taj Agricultural Company 2024',
    ];
}

function lbl($key) {
    return labels()[$key] ?? $key;
}
