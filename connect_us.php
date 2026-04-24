<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['text'] ?? '');

    if ($name && $email && $message) {
        $stmt = db()->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        flash(lang() === 'ar' ? 'تم إرسال رسالتك بنجاح' : 'Your message was sent successfully');
    } else {
        flash(lang() === 'ar' ? 'يرجى ملء جميع الحقول' : 'Please fill all fields', 'error');
    }
}
redirect('index.php#footer');
