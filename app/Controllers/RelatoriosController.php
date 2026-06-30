<?php

class RelatoriosController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function gerar(): void
    {
        $where = [];
        $params = [];

        if (!empty($_GET['status'])) {
            $where[] = "a.status = ?";
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['pessoa_id'])) {
            $where[] = "a.pessoa_id = ?";
            $params[] = (int)$_GET['pessoa_id'];
        }
        if (!empty($_GET['data_inicio'])) {
            $where[] = "a.data_atendimento >= ?";
            $params[] = $_GET['data_inicio'];
        }
        if (!empty($_GET['data_fim'])) {
            $where[] = "a.data_atendimento <= ?";
            $params[] = $_GET['data_fim'];
        }

        $sql = "SELECT a.*, p.nome AS pessoa_nome, t.nome AS tipo_nome, u.nome AS responsavel_nome
                FROM atendimentos a
                LEFT JOIN pessoas p ON a.pessoa_id = p.id
                LEFT JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                LEFT JOIN usuarios u ON a.usuario_id = u.id";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY a.data_atendimento DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'dados' => $atendimentos, 'total' => count($atendimentos)]);
    }
}
