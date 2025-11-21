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

    if ($first === '' || $last === '' || $email === '' || $pass === '') {
        $errors[] = 'Please fill all required fields.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    }

    if (strlen($pass) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email already exists.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password_hash, college, major) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$first, $last, $email, $hash, $college, $major]);

            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            $_SESSION['user_name'] = $first;

            header('Location: /library_uni/front-end/pages/index.html');
            exit;
        }
    }
}

if ($errors) {
    echo 'Registration error: ' . implode(' | ', $errors);
}
