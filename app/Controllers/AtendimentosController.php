<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        $sql = "SELECT a.*, p.nome AS pessoa_nome, t.nome AS tipo_nome, u.nome AS responsavel_nome
                FROM atendimentos a
                LEFT JOIN pessoas p ON a.pessoa_id = p.id
                LEFT JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.data_atendimento DESC, a.horario_atendimento DESC";
        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'dados' => $atendimentos]);
    }

    public function visualizar(): void
    {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare(
            "SELECT a.*, p.nome AS pessoa_nome, p.documento, p.telefone, p.curso, p.periodo,
                    t.nome AS tipo_nome, t.descricao AS tipo_descricao,
                    u.nome AS responsavel_nome, u.email AS responsavel_email, u.perfil AS responsavel_perfil
             FROM atendimentos a
             LEFT JOIN pessoas p ON a.pessoa_id = p.id
             LEFT JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
             LEFT JOIN usuarios u ON a.usuario_id = u.id
             WHERE a.id = ?"
        );
        $stmt->execute([$id]);
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        if ($atendimento) {
            echo json_encode(['sucesso' => true, 'dados' => $atendimento]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Atendimento não encontrado']);
        }
    }

    public function criar(): void
    {
        $usuarioId = $_SESSION['usuario']['id'] ?? 0;

        $stmt = $this->pdo->prepare(
            "INSERT INTO atendimentos (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status, data_atendimento, horario_atendimento)
             VALUES (?, ?, ?, ?, 'aberto', ?, ?)"
        );
        $stmt->execute([
            (int)($_POST['pessoa_id'] ?? 0),
            (int)($_POST['tipo_atendimento_id'] ?? 0),
            $usuarioId,
            $_POST['descricao'] ?? '',
            $_POST['data_atendimento'] ?? date('Y-m-d'),
            $_POST['horario_atendimento'] ?? date('H:i')
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Atendimento criado com sucesso', 'id' => (int)$this->pdo->lastInsertId()]);
    }

    public function alterarStatus(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $observacaoFinal = $_POST['observacao_final'] ?? null;

        $statusValidos = ['aberto', 'em_andamento', 'concluido'];
        if (!in_array($status, $statusValidos)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'mensagem' => 'Status inválido']);
            return;
        }

        if ($status === 'concluido' && $observacaoFinal) {
            $stmt = $this->pdo->prepare("UPDATE atendimentos SET status = ?, observacao_final = ? WHERE id = ?");
            $stmt->execute([$status, $observacaoFinal, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE atendimentos SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado com sucesso']);
    }

    public function opcoesFormulario(): void
    {
        $pessoas = $this->pdo->query("SELECT id, nome FROM pessoas WHERE status = 'ativo' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
        $tipos = $this->pdo->query("SELECT id, nome FROM tipos_atendimentos WHERE status = 'ativo' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => true, 'pessoas' => $pessoas, 'tipos' => $tipos]);
    }
}
