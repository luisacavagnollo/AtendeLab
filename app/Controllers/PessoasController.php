<?php

class PessoasController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        $stmt = $this->pdo->query("SELECT * FROM pessoas ORDER BY nome");
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'dados' => $pessoas]);
    }

    public function buscarPorId(): void
    {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare("SELECT * FROM pessoas WHERE id = ?");
        $stmt->execute([$id]);
        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        if ($pessoa) {
            echo json_encode(['sucesso' => true, 'dados' => $pessoa]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Pessoa não encontrada']);
        }
    }

    public function criar(): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO pessoas (nome, documento, telefone, email, curso, periodo, observacoes, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'ativo')"
        );
        $stmt->execute([
            $_POST['nome'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['telefone'] ?? '',
            $_POST['email'] ?? '',
            $_POST['curso'] ?? '',
            $_POST['periodo'] ?? '',
            $_POST['observacoes'] ?? ''
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pessoa cadastrada com sucesso', 'id' => (int)$this->pdo->lastInsertId()]);
    }

    public function atualizar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare(
            "UPDATE pessoas SET nome=?, documento=?, telefone=?, email=?, curso=?, periodo=?, observacoes=? WHERE id=?"
        );
        $stmt->execute([
            $_POST['nome'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['telefone'] ?? '',
            $_POST['email'] ?? '',
            $_POST['curso'] ?? '',
            $_POST['periodo'] ?? '',
            $_POST['observacoes'] ?? '',
            $id
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pessoa atualizada com sucesso']);
    }

    public function inativar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare("UPDATE pessoas SET status = 'inativo' WHERE id = ?");
        $stmt->execute([$id]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pessoa inativada com sucesso']);
    }
}
