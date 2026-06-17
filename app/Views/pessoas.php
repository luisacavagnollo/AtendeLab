<?php
session_start();
require __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';

// Ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'cadastrar') {
        $stmt = $pdo->prepare("INSERT INTO pessoas (nome, documento, telefone, curso, periodo, status) VALUES (?, ?, ?, ?, ?, 'ativo')");
        $stmt->execute([$_POST['nome'], $_POST['documento'], $_POST['telefone'], $_POST['curso'], $_POST['periodo']]);
    } elseif ($acao === 'editar') {
        $stmt = $pdo->prepare("UPDATE pessoas SET nome=?, documento=?, telefone=?, curso=?, periodo=? WHERE id=?");
        $stmt->execute([$_POST['nome'], $_POST['documento'], $_POST['telefone'], $_POST['curso'], $_POST['periodo'], $_POST['id']]);
    } elseif ($acao === 'excluir') {
        $stmt = $pdo->prepare("DELETE FROM pessoas WHERE id=?");
        $stmt->execute([$_POST['id']]);
    }
    header('Location: /atendelab/app/Views/pessoas.php');
    exit;
}

$pessoas = $pdo->query("SELECT * FROM pessoas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM pessoas WHERE id=?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

require __DIR__ . '/layouts/header.php';
?>

<h2>Pessoas</h2>

<div class="card mb-4">
    <div class="card-body">
        <h5><?= $editando ? 'Editar Pessoa' : 'Nova Pessoa' ?></h5>
        <form method="POST">
            <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'cadastrar' ?>">
            <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="nome" class="form-control" placeholder="Nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="documento" class="form-control" placeholder="Documento" value="<?= htmlspecialchars($editando['documento'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="telefone" class="form-control" placeholder="Telefone" value="<?= htmlspecialchars($editando['telefone'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="curso" class="form-control" placeholder="Curso" value="<?= htmlspecialchars($editando['curso'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <input type="text" name="periodo" class="form-control" placeholder="Período" value="<?= htmlspecialchars($editando['periodo'] ?? '') ?>">
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
        <tr><th>Nome</th><th>Documento</th><th>Telefone</th><th>Curso</th><th>Período</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($pessoas as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td><?= htmlspecialchars($p['documento']) ?></td>
            <td><?= htmlspecialchars($p['telefone']) ?></td>
            <td><?= htmlspecialchars($p['curso']) ?></td>
            <td><?= htmlspecialchars($p['periodo']) ?></td>
            <td>
                <a href="pessoas.php?editar=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <form method="POST" class="d-inline" onsubmit="return confirm('Excluir esta pessoa?')">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/layouts/footer.php'; ?>
