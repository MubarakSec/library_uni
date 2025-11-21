<?php
require __DIR__ . '/../config/session.php';

function require_role(string $role): void
{
    if (!is_logged_in() || !is_role($role)) {
        header('Location: /library_uni/front-end/pages/login.html');
        exit;
    }
}

function require_any_role(array $roles): void
{
    if (!is_logged_in() || !has_any_role($roles)) {
        header('Location: /library_uni/front-end/pages/login.html');
        exit;
    }
}
