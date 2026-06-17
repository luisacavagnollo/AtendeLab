<?php
session_start();
require __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require __DIR__ . '/layouts/header.php';

$total = $pdo->query("SELECT COUNT(*) FROM atendimentos")->fetchColumn();
$abertos = $pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'aberto'")->fetchColumn();
$concluidos = $pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'concluido'")->fetchColumn();
$hoje = $pdo->query("SELECT COUNT(*) FROM atendimentos WHERE data_atendimento = CURDATE()")->fetchColumn();
?>

<h2>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h2>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center text-bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total de Atendimentos</h5>
                <p class="card-text display-6"><?= $total ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center text-bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Atendimentos Abertos</h5>
                <p class="card-text display-6"><?= $abertos ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center text-bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Atendimentos Concluídos</h5>
                <p class="card-text display-6"><?= $concluidos ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center text-bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Atendimentos Hoje</h5>
                <p class="card-text display-6"><?= $hoje ?></p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
