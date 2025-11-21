<?php
require __DIR__ . '/../config/session.php';

session_unset();
session_destroy();

header('Location: /library_uni/front-end/pages/login.html');
exit;
