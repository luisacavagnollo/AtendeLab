<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$baseUrl = '/atendelab/public';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AtendeLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page d-flex align-items-center justify-content-center vh-100">
<div class="login-card p-4" style="width: 100%; max-width: 400px;">
    <h3 class="text-center mb-4">AtendeLab</h3>
    <p class="text-center text-muted mb-4" style="font-size: 0.9rem;">Sistema de Controle de Atendimentos</p>
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <form method="POST" action="<?= $baseUrl ?>/?controller=auth&action=entrar">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="seu@email.com" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" placeholder="••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Entrar</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
