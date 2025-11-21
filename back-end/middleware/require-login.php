<?php
require __DIR__ . '/../config/session.php';

if (!is_logged_in()) {
    header('Location: /library_uni/front-end/pages/login.html');
    exit;
}
