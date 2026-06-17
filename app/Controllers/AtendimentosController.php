<?php

require_once __DIR__ . '/../../config/database.php';

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function visualizar(int $id): array|false
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT a.*, p.nome AS pessoa_nome, p.documento, p.telefone, p.curso, p.periodo,
                        t.nome AS tipo_nome, t.descricao AS tipo_descricao,
                        u.nome AS responsavel_nome, u.email AS responsavel_email, u.perfil AS responsavel_perfil
                 FROM atendimentos a
                 LEFT JOIN pessoas p ON a.pessoa_id = p.id
                 LEFT JOIN tipos_atendimentos t ON a.tipo_atendimento = t.id
                 LEFT JOIN usuarios u ON a.usuario_id = u.id
                 WHERE a.id = :id"
            );
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao visualizar atendimento: " . $e->getMessage());
            return false;
        }
    }

    public function alterarStatus(int $id, string $status, ?string $observacao = null): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE atendimentos SET status = :status, observacao = COALESCE(:obs, observacao) WHERE id = :id");
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':obs', $observacao);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao alterar status: " . $e->getMessage());
            return false;
        }
    }
}
