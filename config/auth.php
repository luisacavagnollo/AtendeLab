<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /atendelab/app/Views/auth/login.php');
    exit;
}
