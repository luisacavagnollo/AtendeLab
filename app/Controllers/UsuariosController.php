<?php

class UsuariosController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        $stmt = $this->pdo->query("SELECT id, nome, email, perfil, status, criado_em FROM usuarios ORDER BY nome");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'dados' => $usuarios]);
    }

    public function buscarPorId(): void
    {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare("SELECT id, nome, email, perfil, status, criado_em FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        if ($usuario) {
            echo json_encode(['sucesso' => true, 'dados' => $usuario]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
        }
    }

    public function criar(): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES (?, ?, ?, ?, 'ativo')"
        );
        $stmt->execute([
            $_POST['nome'] ?? '',
            $_POST['email'] ?? '',
            password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT),
            $_POST['perfil'] ?? 'atendente'
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário criado com sucesso', 'id' => (int)$this->pdo->lastInsertId()]);
    }

    public function atualizar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare(
            "UPDATE usuarios SET nome=?, email=?, perfil=? WHERE id=?"
        );
        $stmt->execute([
            $_POST['nome'] ?? '',
            $_POST['email'] ?? '',
            $_POST['perfil'] ?? 'atendente',
            $id
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso']);
    }

    public function inativar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare("UPDATE usuarios SET status = 'inativo' WHERE id = ?");
        $stmt->execute([$id]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário inativado com sucesso']);
    }
}
