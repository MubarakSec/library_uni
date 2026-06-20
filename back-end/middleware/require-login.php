<?php
require_once __DIR__ . '/../config/session.php';

if (!is_logged_in()) {
    header('Location: /front-end/pages/login.html');
    exit;
}
