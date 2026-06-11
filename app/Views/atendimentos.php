<?php
session_start();
require __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'cadastrar') {
        $stmt = $pdo->prepare("INSERT INTO atendimentos (pessoa_id, tipo_atendimento, usuario_id, data_atendimento, hora_atendimento, descricao, status) VALUES (?, ?, ?, ?, ?, ?, 'aberto')");
        $stmt->execute([$_POST['pessoa_id'], $_POST['tipo_atendimento'], $_SESSION['usuario_id'], $_POST['data_atendimento'], $_POST['hora_atendimento'], $_POST['descricao']]);
    } elseif ($acao === 'status') {
        $novoStatus = $_POST['novo_status'];
        $obs = $_POST['observacao'] ?? null;
        $stmt = $pdo->prepare("UPDATE atendimentos SET status=?, observacao=COALESCE(?, observacao) WHERE id=?");
        $stmt->execute([$novoStatus, $obs, $_POST['id']]);
    } elseif ($acao === 'excluir') {
        $stmt = $pdo->prepare("DELETE FROM atendimentos WHERE id=?");
        $stmt->execute([$_POST['id']]);
    }
    header('Location: /atendelab/app/Views/atendimentos.php');
    exit;
}

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
$sql .= " ORDER BY a.data_atendimento DESC, a.hora_atendimento DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pessoas = $pdo->query("SELECT id, nome FROM pessoas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$tipos = $pdo->query("SELECT id, nome FROM tipos_atendimentos WHERE status='ativo' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/layouts/header.php';
?>

<h2>Atendimentos</h2>

<!-- Formulário de novo atendimento -->
<div class="card mb-4">
    <div class="card-body">
        <h5>Novo Atendimento</h5>
        <form method="POST">
            <input type="hidden" name="acao" value="cadastrar">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="pessoa_id" class="form-select" required>
                        <option value="">Pessoa...</option>
                        <?php foreach ($pessoas as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tipo_atendimento" class="form-select" required>
                        <option value="">Tipo...</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="data_atendimento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-1">
                    <input type="time" name="hora_atendimento" class="form-control" value="<?= date('H:i') ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="descricao" class="form-control" placeholder="Descrição" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
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
                <input type="date" name="data_inicio" class="form-control" placeholder="De" value="<?= $_GET['data_inicio'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="data_fim" class="form-control" placeholder="Até" value="<?= $_GET['data_fim'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
            </div>
            <div class="col-md-1">
                <a href="atendimentos.php" class="btn btn-outline-secondary w-100">Limpar</a>
            </div>
        </form>
    </div>
</div>

<!-- Listagem -->
<table class="table table-striped">
    <thead>
        <tr><th>Data</th><th>Hora</th><th>Pessoa</th><th>Tipo</th><th>Descrição</th><th>Responsável</th><th>Status</th><th>Ações</th></tr>
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
            <td>
                <span class="badge bg-<?= $a['status'] === 'aberto' ? 'warning' : ($a['status'] === 'em andamento' ? 'primary' : 'success') ?>">
                    <?= $a['status'] ?>
                </span>
            </td>
            <td>
                <?php if ($a['status'] === 'aberto'): ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="acao" value="status">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <input type="hidden" name="novo_status" value="em andamento">
                        <button class="btn btn-sm btn-primary">Iniciar</button>
                    </form>
                <?php elseif ($a['status'] === 'em andamento'): ?>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalConcluir<?= $a['id'] ?>">Concluir</button>
                    <!-- Modal -->
                    <div class="modal fade" id="modalConcluir<?= $a['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Concluir Atendimento</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="acao" value="status">
                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                        <input type="hidden" name="novo_status" value="concluido">
                                        <label class="form-label">Observação final:</label>
                                        <textarea name="observacao" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Concluir</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <form method="POST" class="d-inline" onsubmit="return confirm('Excluir?')">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/layouts/footer.php'; ?>
