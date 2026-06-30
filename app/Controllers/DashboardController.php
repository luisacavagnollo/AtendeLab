<?php

class DashboardController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function resumo(): void
    {
        $totalPessoas = $this->pdo->query("SELECT COUNT(*) FROM pessoas WHERE status = 'ativo'")->fetchColumn();
        $totalTipos = $this->pdo->query("SELECT COUNT(*) FROM tipos_atendimentos WHERE status = 'ativo'")->fetchColumn();
        $totalAtendimentos = $this->pdo->query("SELECT COUNT(*) FROM atendimentos")->fetchColumn();
        $abertos = $this->pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'aberto'")->fetchColumn();
        $emAndamento = $this->pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'em_andamento'")->fetchColumn();
        $concluidos = $this->pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'concluido'")->fetchColumn();

        $stmtRecentes = $this->pdo->query(
            "SELECT a.id, a.descricao, a.status, a.data_atendimento, p.nome AS pessoa_nome
             FROM atendimentos a
             LEFT JOIN pessoas p ON a.pessoa_id = p.id
             ORDER BY a.criado_em DESC LIMIT 5"
        );
        $recentes = $stmtRecentes->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'sucesso' => true,
            'indicadores' => [
                'total_pessoas' => (int)$totalPessoas,
                'total_tipos' => (int)$totalTipos,
                'total_atendimentos' => (int)$totalAtendimentos,
                'abertos' => (int)$abertos,
                'em_andamento' => (int)$emAndamento,
                'concluidos' => (int)$concluidos
            ],
            'atendimentos_recentes' => $recentes
        ]);
    }
}
