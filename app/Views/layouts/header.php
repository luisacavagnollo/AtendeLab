<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtendeLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/atendelab/app/Views/dashboard.php">AtendeLab</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/atendelab/app/Views/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/atendelab/app/Views/pessoas.php">Pessoas</a></li>
                <li class="nav-item"><a class="nav-link" href="/atendelab/app/Views/tipos_atendimento.php">Tipos de Atendimento</a></li>
                <li class="nav-item"><a class="nav-link" href="/atendelab/app/Views/atendimentos.php">Atendimentos</a></li>
                <?php if (($_SESSION['usuario_perfil'] ?? '') === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="/atendelab/app/Views/usuarios/listar.php">Usuários</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="/atendelab/app/Views/relatorio.php">Relatório</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <span class="text-light me-3"><?= $_SESSION['usuario_nome'] ?? '' ?></span>
                <a href="/atendelab/app/Views/auth/logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </div>
</nav>
<div class="container mt-4">
