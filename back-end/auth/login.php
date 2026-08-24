<?php
/**
 * User Login Handler
 * Authenticates users and creates session with role information
 * Includes brute-force protection (rate-limiting per session/IP)
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /front-end/pages/login.html');
    exit;
}

// Rate limiting: 5 failed attempts per 15 minutes
$lockoutTime = 900; // 15 minutes
$maxAttempts = 5;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['first_failed_login'] = time();
}

if ($_SESSION['login_attempts'] >= $maxAttempts) {
    if (time() - $_SESSION['first_failed_login'] < $lockoutTime) {
        $remaining = ceil(($lockoutTime - (time() - $_SESSION['first_failed_login'])) / 60);
        echo '<div style="color: red; padding: 20px; direction: rtl; font-family: sans-serif;">';
        echo "تم حظر محاولات تسجيل الدخول مؤقتاً بسبب تكرار المحاولات الخاطئة. يرجى المحاولة بعد $remaining دقيقة.";
        echo '</div>';
        exit;
    } else {
        // Reset after lockout expiration
        $_SESSION['login_attempts'] = 0;
        $_SESSION['first_failed_login'] = time();
    }
}

$email = trim($_POST['email'] ?? '');
$pass = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
    echo '<div style="color: red; padding: 20px; direction: rtl; font-family: sans-serif;">يرجى إدخال البريد الإلكتروني وكلمة المرور.</div>';
    exit;
}

$stmt = $pdo->prepare('
    SELECT id, first_name, last_name, password_hash, role 
    FROM users 
    WHERE email = ?
');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($pass, $user['password_hash'])) {
    // Reset rate limiter on successful login
    unset($_SESSION['login_attempts'], $_SESSION['first_failed_login']);
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['first_name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['CREATED'] = time();
    $_SESSION['LAST_ACTIVITY'] = time();

    header('Location: /front-end/pages/index.html');
    exit;
}

// Increment failed attempts
$_SESSION['login_attempts']++;
if ($_SESSION['login_attempts'] === 1) {
    $_SESSION['first_failed_login'] = time();
}

$remainingAttempts = max(0, $maxAttempts - $_SESSION['login_attempts']);

echo '<div style="color: red; padding: 20px; direction: rtl; font-family: sans-serif;">';
echo 'خطأ في تسجيل الدخول. البريد الإلكتروني أو كلمة المرور غير صحيحة.<br>';
if ($remainingAttempts > 0) {
    echo "<span style=\"color: #666; font-size: 0.9em;\">المحاولات المتبقية: $remainingAttempts</span>";
} else {
    echo "<span style=\"color: #e00; font-size: 0.9em;\">تم تجاوز الحد الأقصى للمحاولات. تم القفل لمدة 15 دقيقة.</span>";
}
echo '</div>';
