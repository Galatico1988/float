<?php
require_once __DIR__ . '/../../config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    float_redirect(float_url('pages/login.php'));
    exit;
}
