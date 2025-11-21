<?php
require __DIR__ . '/../config/session.php';

// Clear session data
$_SESSION = [];

// Delete session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_unset();
session_destroy();

header('Location: /library_uni/front-end/pages/login.html');
exit;
