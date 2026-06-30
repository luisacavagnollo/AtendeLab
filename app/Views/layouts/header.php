<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config-view.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtendeLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= $baseUrl ?>/?controller=frontend&action=dashboard">AtendeLab</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/?controller=frontend&action=dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/?controller=frontend&action=pessoas">Pessoas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/?controller=frontend&action=tiposAtendimentos">Tipos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/?controller=frontend&action=atendimentos">Atendimentos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/?controller=frontend&action=relatorios">Relatório</a></li>
                <?php if (($_SESSION['usuario']['perfil'] ?? '') === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/?controller=frontend&action=usuarios">Usuários</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <span class="text-light me-3"><?= htmlspecialchars($_SESSION['usuario']['nome'] ?? '') ?></span>
                <a href="<?= $baseUrl ?>/?controller=auth&action=logout" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </div>
</nav>
<div class="container mt-4">
<script src="<?= $baseUrl ?>/assets/js/api.js"></script>
