<?php
/**
 * User Login Handler
 * Authenticates users and creates session with role information
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    // FIX: Also select the 'role' column from database
    $stmt = $pdo->prepare('
        SELECT id, first_name, last_name, password_hash, role 
        FROM users 
        WHERE email = ?
    ');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify password and set session
    if ($user && password_verify($pass, $user['password_hash'])) {
        // Set all session variables
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['user_role'] = $user['role'];  // FIX: Store role from database
        $_SESSION['CREATED'] = time();           // Session creation timestamp

        // Redirect to homepage
        header('Location: /library_uni/front-end/pages/index.html');
        exit;
    }
}

// Show error (production: should redirect to login page with error message)
echo '<div style="color: red; padding: 20px; direction: rtl;">خطأ في تسجيل الدخول. البريد الإلكتروني أو كلمة المرور غير صحيحة.</div>';
