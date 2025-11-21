<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, first_name, password_hash, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'] ?? 'student';

        header('Location: /library_uni/front-end/pages/index.html');
        exit;
    }
}

echo 'Invalid login.';
