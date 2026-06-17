<?php
session_start();
require __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../Controllers/AtendimentosController.php';

$controller = new AtendimentosController();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: /atendelab/app/Views/atendimentos.php');
    exit;
}

// Ação de alterar status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'alterar_status') {
    $controller->alterarStatus($id, $_POST['novo_status'], $_POST['observacao'] ?: null);
    header("Location: visualizar.php?id=$id");
    exit;
}

$atendimento = $controller->visualizar($id);
if (!$atendimento) {
    header('Location: /atendelab/app/Views/atendimentos.php');
    exit;
}

require __DIR__ . '/../layouts/header.php';

$badgeStatus = match($atendimento['status']) {
    'aberto' => 'bg-warning',
    'em andamento' => 'bg-primary',
    'concluido' => 'bg-success',
    default => 'bg-secondary'
};
$labelStatus = match($atendimento['status']) {
    'aberto' => 'Aberto',
    'em andamento' => 'Em andamento',
    'concluido' => 'Concluído',
    default => $atendimento['status']
};
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Atendimento #<?= $atendimento['id'] ?></h2>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalStatus">Alterar Status</button>
        <a href="/atendelab/app/Views/atendimentos.php" class="btn btn-secondary">Voltar</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><strong>Dados do Atendimento</strong></div>
            <div class="card-body">
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($atendimento['data_atendimento'])) ?></p>
                <p><strong>Hora:</strong> <?= $atendimento['hora_atendimento'] ?></p>
                <p><strong>Descrição:</strong> <?= htmlspecialchars($atendimento['descricao']) ?></p>
                <p><strong>Observação:</strong> <?= htmlspecialchars($atendimento['observacao'] ?? '—') ?></p>
                <p><strong>Status:</strong> <span class="badge <?= $badgeStatus ?>"><?= $labelStatus ?></span></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><strong>Pessoa Atendida</strong></div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?= htmlspecialchars($atendimento['pessoa_nome'] ?? '—') ?></p>
                <p><strong>Documento:</strong> <?= htmlspecialchars($atendimento['documento'] ?? '—') ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($atendimento['telefone'] ?? '—') ?></p>
                <p><strong>Curso:</strong> <?= htmlspecialchars($atendimento['curso'] ?? '—') ?></p>
                <p><strong>Período:</strong> <?= htmlspecialchars($atendimento['periodo'] ?? '—') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><strong>Tipo de Atendimento</strong></div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?= htmlspecialchars($atendimento['tipo_nome'] ?? '—') ?></p>
                <p><strong>Descrição:</strong> <?= htmlspecialchars($atendimento['tipo_descricao'] ?? '—') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><strong>Responsável</strong></div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?= htmlspecialchars($atendimento['responsavel_nome'] ?? '—') ?></p>
                <p><strong>E-mail:</strong> <?= htmlspecialchars($atendimento['responsavel_email'] ?? '—') ?></p>
                <p><strong>Perfil:</strong> <?= htmlspecialchars($atendimento['responsavel_perfil'] ?? '—') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alterar Status -->
<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="alterar_status">
                    <div class="mb-3">
                        <label class="form-label">Novo Status</label>
                        <select name="novo_status" class="form-select" required>
                            <option value="aberto" <?= $atendimento['status'] === 'aberto' ? 'selected' : '' ?>>Aberto</option>
                            <option value="em andamento" <?= $atendimento['status'] === 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
                            <option value="concluido" <?= $atendimento['status'] === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea name="observacao" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
