<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../app/Controllers/UsuariosController.php';

$controller = new UsuariosController();
$acao = $_GET['acao'] ?? '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

switch ($acao) {
    case 'cadastrar':
        $dados = [
            'nome' => 'Maria Silva',
            'email' => 'maria.silva@email.com',
            'senha' => 'senha123',
            'perfil' => 'atendente',
            'status' => 'ativo',
        ];
        $resultado = $controller->cadastrar($dados);
        echo json_encode(['sucesso' => $resultado !== false, 'id' => $resultado]);
        break;

    case 'listar':
        $resultado = $controller->listar();
        echo json_encode($resultado);
        break;

    case 'buscar':
        $resultado = $controller->buscarPorId($id);
        echo json_encode($resultado ?: ['erro' => 'Usuário não encontrado']);
        break;

    case 'atualizar':
        $dados = [
            'nome' => 'Maria Silva Atualizada',
            'email' => 'maria.atualizada@email.com',
            'perfil' => 'admin',
            'status' => 'ativo',
        ];
        $resultado = $controller->atualizar($id, $dados);
        echo json_encode(['sucesso' => $resultado]);
        break;

    case 'excluir':
        $resultado = $controller->excluir($id);
        echo json_encode(['sucesso' => $resultado]);
        break;

    default:
        echo json_encode(['erro' => 'Ação inválida. Use: cadastrar, listar, buscar, atualizar, excluir']);
        break;
}
