<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id'])) {
    header('Location: ../../public/pages/login.php');
    exit;
}
