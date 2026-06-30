<?php

class AuthController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function exibirLogin(): void
    {
        if (isset($_SESSION['usuario']['id'])) {
            header('Location: /atendelab/public/?controller=frontend&action=dashboard');
            exit;
        }
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function entrar(): void
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND status = 'ativo'");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'perfil' => $usuario['perfil']
            ];
            header('Location: /atendelab/public/?controller=frontend&action=dashboard');
            exit;
        }

        $erro = 'E-mail ou senha incorretos.';
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /atendelab/public/?controller=auth&action=exibirLogin');
        exit;
    }
}
