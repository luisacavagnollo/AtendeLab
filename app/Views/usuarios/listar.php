<?php
session_start();
require __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../Controllers/UsuariosController.php';

$controller = new UsuariosController();

// Ações via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'inativar') {
        $controller->inativar((int)$_POST['id']);
        $_SESSION['mensagem'] = 'Usuário inativado com sucesso.';
    } elseif ($action === 'excluir') {
        try {
            $controller->excluir((int)$_POST['id']);
            $_SESSION['mensagem'] = 'Usuário excluído com sucesso.';
        } catch (PDOException $e) {
            $_SESSION['erro'] = 'Não é possível excluir: usuário possui atendimentos vinculados.';
        }
    }
    header('Location: listar.php');
    exit;
}

$usuarios = $controller->listar() ?: [];

// Mensagens de feedback
$mensagem = $_SESSION['mensagem'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['mensagem'], $_SESSION['erro']);

require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Usuários</h2>
    <a href="criar.php" class="btn btn-primary">Novo Usuário</a>
</div>

<?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<?php if ($mensagem): ?>
    <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <?php
                $badgePerfil = match($u['perfil']) {
                    'admin' => 'bg-danger',
                    'atendente' => 'bg-primary',
                    'aluno' => 'bg-info',
                    default => 'bg-secondary'
                };
                ?>
                <span class="badge <?= $badgePerfil ?>"><?= $u['perfil'] ?></span>
            </td>
            <td>
                <span class="badge <?= $u['status'] === 'ativo' ? 'bg-success' : 'bg-secondary' ?>"><?= $u['status'] ?></span>
            </td>
            <td>
                <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <?php if ($u['status'] === 'ativo'): ?>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Inativar este usuário?')">
                        <input type="hidden" name="action" value="inativar">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="btn btn-sm btn-secondary">Inativar</button>
                    </form>
                <?php endif; ?>
                <form method="POST" class="d-inline" onsubmit="return confirm('Excluir este usuário permanentemente?')">
                    <input type="hidden" name="action" value="excluir">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
