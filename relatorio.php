<?php
session_start();
require 'includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Filtros
$where = [];
$params = [];
if (!empty($_GET['status'])) {
    $where[] = "a.status = ?";
    $params[] = $_GET['status'];
}
if (!empty($_GET['pessoa_id'])) {
    $where[] = "a.pessoa_id = ?";
    $params[] = $_GET['pessoa_id'];
}
if (!empty($_GET['data_inicio'])) {
    $where[] = "a.data_atendimento >= ?";
    $params[] = $_GET['data_inicio'];
}
if (!empty($_GET['data_fim'])) {
    $where[] = "a.data_atendimento <= ?";
    $params[] = $_GET['data_fim'];
}

$sql = "SELECT a.*, p.nome AS pessoa_nome, t.nome AS tipo_nome, u.nome AS responsavel
        FROM atendimentos a
        LEFT JOIN pessoas p ON a.pessoa_id = p.id
        LEFT JOIN tipos_atendimentos t ON a.tipo_atendimento = t.id
        LEFT JOIN usuarios u ON a.usuario_id = u.id";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY a.data_atendimento DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pessoas = $pdo->query("SELECT id, nome FROM pessoas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório - AtendeLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>@media print { .no-print { display: none; } }</style>
</head>
<body>
<div class="container mt-4">
    <div class="no-print">
        <h2>Relatório de Atendimentos</h2>
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Status...</option>
                    <option value="aberto" <?= ($_GET['status'] ?? '') === 'aberto' ? 'selected' : '' ?>>Aberto</option>
                    <option value="em andamento" <?= ($_GET['status'] ?? '') === 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
                    <option value="concluido" <?= ($_GET['status'] ?? '') === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="pessoa_id" class="form-select">
                    <option value="">Pessoa...</option>
                    <?php foreach ($pessoas as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($_GET['pessoa_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="data_inicio" class="form-control" value="<?= $_GET['data_inicio'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="data_fim" class="form-control" value="<?= $_GET['data_fim'] ?? '' ?>">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
            </div>
            <div class="col-md-1">
                <button type="button" onclick="window.print()" class="btn btn-outline-dark w-100">Imprimir</button>
            </div>
            <div class="col-md-1">
                <a href="dashboard.php" class="btn btn-outline-secondary w-100">Voltar</a>
            </div>
        </form>
    </div>

    <h3 class="mb-3">AtendeLab - Relatório de Atendimentos</h3>
    <p>Gerado em: <?= date('d/m/Y H:i') ?> | Total: <?= count($atendimentos) ?> registro(s)</p>

    <table class="table table-bordered table-sm">
        <thead class="table-dark">
            <tr><th>Data</th><th>Hora</th><th>Pessoa</th><th>Tipo</th><th>Descrição</th><th>Responsável</th><th>Status</th><th>Observação</th></tr>
        </thead>
        <tbody>
        <?php foreach ($atendimentos as $a): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($a['data_atendimento'])) ?></td>
                <td><?= $a['hora_atendimento'] ?></td>
                <td><?= htmlspecialchars($a['pessoa_nome']) ?></td>
                <td><?= htmlspecialchars($a['tipo_nome']) ?></td>
                <td><?= htmlspecialchars($a['descricao']) ?></td>
                <td><?= htmlspecialchars($a['responsavel']) ?></td>
                <td><?= $a['status'] ?></td>
                <td><?= htmlspecialchars($a['observacao'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
