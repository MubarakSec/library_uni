<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $college = trim($_POST['college'] ?? '');
    $major = trim($_POST['major'] ?? '');

    // Validate required fields
    if ($first === '' || $last === '' || $email === '' || $pass === '') {
        $errors[] = 'الرجاء ملء جميع الحقول المطلوبة.';
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صالح.';
    }

    // Strong password validation
    if (strlen($pass) < 8) {
        $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
    }
    if (!preg_match('/[A-Z]/', $pass)) {
        $errors[] = 'كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل.';
    }
    if (!preg_match('/[a-z]/', $pass)) {
        $errors[] = 'كلمة المرور يجب أن تحتوي على حرف صغير واحد على الأقل.';
    }
    if (!preg_match('/[0-9]/', $pass)) {
        $errors[] = 'كلمة المرور يجب أن تحتوي على رقم واحد على الأقل.';
    }

    // Check if email already exists
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'البريد الإلكتروني مستخدم بالفعل.';
        } else {
            // Hash password and insert user
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password_hash, college, major) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$first, $last, $email, $hash, $college, $major]);

            // Set session and redirect
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            $_SESSION['user_name'] = $first;
            $_SESSION['user_role'] = 'student'; // Default role

            header('Location: /library_uni/front-end/pages/index.html');
            exit;
        }
    }
}

// Display errors
if ($errors) {
    echo 'أخطاء في التسجيل: ' . implode(' | ', $errors);
}
