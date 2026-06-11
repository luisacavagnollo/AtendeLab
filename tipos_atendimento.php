<?php
session_start();
require 'includes/auth.php';
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'cadastrar') {
        $stmt = $pdo->prepare("INSERT INTO tipos_atendimentos (nome, descricao, status) VALUES (?, ?, 'ativo')");
        $stmt->execute([$_POST['nome'], $_POST['descricao']]);
    } elseif ($acao === 'editar') {
        $stmt = $pdo->prepare("UPDATE tipos_atendimentos SET nome=?, descricao=? WHERE id=?");
        $stmt->execute([$_POST['nome'], $_POST['descricao'], $_POST['id']]);
    } elseif ($acao === 'excluir') {
        $stmt = $pdo->prepare("DELETE FROM tipos_atendimentos WHERE id=?");
        $stmt->execute([$_POST['id']]);
    }
    header('Location: tipos_atendimento.php');
    exit;
}

$tipos = $pdo->query("SELECT * FROM tipos_atendimentos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM tipos_atendimentos WHERE id=?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

require 'includes/header.php';
?>

<h2>Tipos de Atendimento</h2>

<div class="card mb-4">
    <div class="card-body">
        <h5><?= $editando ? 'Editar Tipo' : 'Novo Tipo' ?></h5>
        <form method="POST">
            <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'cadastrar' ?>">
            <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="nome" class="form-control" placeholder="Nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="descricao" class="form-control" placeholder="Descrição" value="<?= htmlspecialchars($editando['descricao'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><?= $editando ? 'Salvar' : 'Cadastrar' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr><th>Nome</th><th>Descrição</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($tipos as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['nome']) ?></td>
            <td><?= htmlspecialchars($t['descricao']) ?></td>
            <td><?= $t['status'] ?></td>
            <td>
                <a href="tipos_atendimento.php?editar=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <form method="POST" class="d-inline" onsubmit="return confirm('Excluir este tipo?')">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require 'includes/footer.php'; ?>
