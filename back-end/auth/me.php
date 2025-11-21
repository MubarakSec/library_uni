<?php
require __DIR__ . '/../config/session.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    echo json_encode([
        'logged_in' => false,
    ]);
    exit;
}

echo json_encode([
    'logged_in' => true,
    'id' => current_user_id(),
    'name' => current_user_name(),
    'role' => current_user_role(),
]);
