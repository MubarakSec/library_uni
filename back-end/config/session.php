<?php
// Load environment if needed
if (!isset($_ENV['SECURE_COOKIES'])) {
    require_once __DIR__ . '/env.php';
    EnvLoader::load(__DIR__ . '/../../');
}

// Configure session security
if (session_status() === PHP_SESSION_NONE) {
    $secureCookies = filter_var(EnvLoader::get('SECURE_COOKIES', 'false'), FILTER_VALIDATE_BOOLEAN);
    $sessionLifetime = (int)EnvLoader::get('SESSION_LIFETIME', 7200);
    
    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path' => '/',
        'domain' => '',
        'secure' => $secureCookies, // true in production with HTTPS
        'httponly' => true,  // Prevent XSS attacks
        'samesite' => 'Strict'  // CSRF protection
    ]);
    
    session_start();
    
    // Regenerate session ID periodically (every 30 minutes)
    if (!isset($_SESSION['LAST_ACTIVITY']) || 
        (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        session_regenerate_id(true);
        $_SESSION['LAST_ACTIVITY'] = time();
    }
    
    // Session timeout check
    if (isset($_SESSION['CREATED']) && 
        (time() - $_SESSION['CREATED'] > $sessionLifetime)) {
        session_unset();
        session_destroy();
        session_start();
    }
    
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    }
}

// Helper functions
function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function current_user_name(): ?string
{
    return $_SESSION['user_name'] ?? null;
}

function current_user_role(): ?string
{
    return $_SESSION['user_role'] ?? 'student'; // Default role
}

function is_logged_in(): bool
{
    return current_user_id() !== null;
}

function is_role(string $role): bool
{
    return current_user_role() === $role;
}

function has_any_role(array $roles): bool
{
    return in_array(current_user_role(), $roles);
}
