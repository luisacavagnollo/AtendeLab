<?php

class TiposAtendimentosController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        $stmt = $this->pdo->query("SELECT * FROM tipos_atendimentos ORDER BY nome");
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'dados' => $tipos]);
    }

    public function buscarPorId(): void
    {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare("SELECT * FROM tipos_atendimentos WHERE id = ?");
        $stmt->execute([$id]);
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        if ($tipo) {
            echo json_encode(['sucesso' => true, 'dados' => $tipo]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo não encontrado']);
        }
    }

    public function criar(): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tipos_atendimentos (nome, descricao, status) VALUES (?, ?, 'ativo')"
        );
        $stmt->execute([
            $_POST['nome'] ?? '',
            $_POST['descricao'] ?? ''
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tipo cadastrado com sucesso', 'id' => (int)$this->pdo->lastInsertId()]);
    }

    public function atualizar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare(
            "UPDATE tipos_atendimentos SET nome=?, descricao=? WHERE id=?"
        );
        $stmt->execute([
            $_POST['nome'] ?? '',
            $_POST['descricao'] ?? '',
            $id
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tipo atualizado com sucesso']);
    }

    public function inativar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare("UPDATE tipos_atendimentos SET status = 'inativo' WHERE id = ?");
        $stmt->execute([$id]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tipo inativado com sucesso']);
    }
}
