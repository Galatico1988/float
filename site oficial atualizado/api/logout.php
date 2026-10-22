<?php
require_once __DIR__ . '/../config/app.php';
if(!isset($_SESSION)) {
    session_start();
}

session_destroy();

float_redirect(float_url('index.php'));
exit;
?>