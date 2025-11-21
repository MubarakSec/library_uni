<?php
/**
 * User Registration Handler
 * Handles new user registration with validation
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve POST data
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

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صالح.';
    }

    // Strong password validation (min 8 chars, uppercase, lowercase, number)
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
            // Hash password using bcrypt
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            
            // FIX: Correct INSERT statement with all columns including 'role'
            $stmt = $pdo->prepare('
                INSERT INTO users (first_name, last_name, email, password_hash, college, major, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $first,
                $last,
                $email,
                $hash,
                $college ?: null,  // NULL if empty
                $major ?: null,     // NULL if empty
                'student'           // Default role for new registrations
            ]);

            // Set session variables
            $userId = (int) $pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $first;
            $_SESSION['user_role'] = 'student';  // Store role in session
            $_SESSION['CREATED'] = time();       // Session creation time

            // Redirect to homepage
            header('Location: /library_uni/front-end/pages/index.html');
            exit;
        }
    }
}

// Display errors (production: should use JSON or template)
if ($errors) {
    echo '<div style="color: red; padding: 20px; direction: rtl;">';
    echo '<h3>أخطاء في التسجيل:</h3><ul>';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul></div>';
}
