<?php

require_once __DIR__ . '/../../config/database.php';

class UsuariosController
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function cadastrar(array $dados): int|false
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES (:nome, :email, :senha, :perfil, :status)"
            );
            $stmt->bindValue(':nome', $dados['nome']);
            $stmt->bindValue(':email', $dados['email']);
            $stmt->bindValue(':senha', password_hash($dados['senha'], PASSWORD_DEFAULT));
            $stmt->bindValue(':perfil', $dados['perfil']);
            $stmt->bindValue(':status', $dados['status']);
            $stmt->execute();
            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function listar(): array|false
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT id, nome, email, perfil, status, criado_em FROM usuarios ORDER BY nome"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorId(int $id): array|false
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, nome, email, perfil, status, criado_em FROM usuarios WHERE id = :id"
            );
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar(int $id, array $dados): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, status = :status WHERE id = :id"
            );
            $stmt->bindValue(':nome', $dados['nome']);
            $stmt->bindValue(':email', $dados['email']);
            $stmt->bindValue(':perfil', $dados['perfil']);
            $stmt->bindValue(':status', $dados['status']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function inativar(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET status = 'inativo' WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao inativar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
