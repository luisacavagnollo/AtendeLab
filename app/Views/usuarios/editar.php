<?php
session_start();
require __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../Controllers/UsuariosController.php';

$controller = new UsuariosController();
$erro = '';

$id = (int)($_GET['id'] ?? 0);
$usuario = $controller->buscarPorId($id);
if (!$usuario) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } else {
        $resultado = $controller->atualizar($id, [
            'nome' => $_POST['nome'],
            'email' => $email,
            'perfil' => $_POST['perfil'],
            'status' => $_POST['status'],
        ]);
        if ($resultado) {
            $_SESSION['mensagem'] = 'Usuário atualizado com sucesso.';
            header('Location: listar.php');
            exit;
        }
        $erro = 'Erro ao atualizar. Verifique se o e-mail já está em uso.';
    }
}

require __DIR__ . '/../layouts/header.php';
?>

<h2>Editar Usuário</h2>

<?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Perfil</label>
                <select name="perfil" class="form-select">
                    <option value="atendente" <?= $usuario['perfil'] === 'atendente' ? 'selected' : '' ?>>Atendente</option>
                    <option value="admin" <?= $usuario['perfil'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="aluno" <?= $usuario['perfil'] === 'aluno' ? 'selected' : '' ?>>Aluno</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="ativo" <?= $usuario['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="inativo" <?= $usuario['status'] === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
