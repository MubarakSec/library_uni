<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function current_user_name(): ?string
{
    return $_SESSION['user_name'] ?? null;
}

function is_logged_in(): bool
{
    return current_user_id() !== null;
}
