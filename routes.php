<?php
/**
 * AtendeLab - Roteador principal
 * Despacha requisições com base nos parâmetros controller e action.
 */

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'exibirLogin';

switch ($controller) {

    case 'auth':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        $ctrl = new AuthController($pdo);
        switch ($action) {
            case 'exibirLogin': $ctrl->exibirLogin(); break;
            case 'entrar': $ctrl->entrar(); break;
            case 'logout': $ctrl->logout(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    case 'frontend':
        require_once __DIR__ . '/app/Controllers/FrontendController.php';
        $ctrl = new FrontendController();
        switch ($action) {
            case 'dashboard': $ctrl->dashboard(); break;
            case 'pessoas': $ctrl->pessoas(); break;
            case 'tiposAtendimentos': $ctrl->tiposAtendimentos(); break;
            case 'atendimentos': $ctrl->atendimentos(); break;
            case 'relatorios': $ctrl->relatorios(); break;
            case 'usuarios': $ctrl->usuarios(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    case 'dashboard':
        require_once __DIR__ . '/app/Middleware/auth.php';
        require_once __DIR__ . '/app/Controllers/DashboardController.php';
        $ctrl = new DashboardController($pdo);
        switch ($action) {
            case 'resumo': $ctrl->resumo(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    case 'pessoas':
        require_once __DIR__ . '/app/Middleware/auth.php';
        require_once __DIR__ . '/app/Controllers/PessoasController.php';
        $ctrl = new PessoasController($pdo);
        switch ($action) {
            case 'listar': $ctrl->listar(); break;
            case 'buscar':
            case 'buscarPorId': $ctrl->buscarPorId(); break;
            case 'criar': $ctrl->criar(); break;
            case 'atualizar': $ctrl->atualizar(); break;
            case 'inativar': $ctrl->inativar(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    case 'tipos':
        require_once __DIR__ . '/app/Middleware/auth.php';
        require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
        $ctrl = new TiposAtendimentosController($pdo);
        switch ($action) {
            case 'listar': $ctrl->listar(); break;
            case 'buscar':
            case 'buscarPorId': $ctrl->buscarPorId(); break;
            case 'criar': $ctrl->criar(); break;
            case 'atualizar': $ctrl->atualizar(); break;
            case 'inativar': $ctrl->inativar(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    case 'atendimentos':
        require_once __DIR__ . '/app/Middleware/auth.php';
        require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
        $ctrl = new AtendimentosController($pdo);
        switch ($action) {
            case 'listar': $ctrl->listar(); break;
            case 'visualizar': $ctrl->visualizar(); break;
            case 'criar': $ctrl->criar(); break;
            case 'alterarStatus':
            case 'atualizarStatus': $ctrl->alterarStatus(); break;
            case 'opcoesFormulario': $ctrl->opcoesFormulario(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    case 'relatorios':
        require_once __DIR__ . '/app/Middleware/auth.php';
        require_once __DIR__ . '/app/Controllers/RelatoriosController.php';
        $ctrl = new RelatoriosController($pdo);
        switch ($action) {
            case 'gerar': $ctrl->gerar(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    case 'usuarios':
        require_once __DIR__ . '/app/Middleware/auth.php';
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        $ctrl = new UsuariosController($pdo);
        switch ($action) {
            case 'listar': $ctrl->listar(); break;
            case 'buscar':
            case 'buscarPorId': $ctrl->buscarPorId(); break;
            case 'criar': $ctrl->criar(); break;
            case 'atualizar': $ctrl->atualizar(); break;
            case 'inativar': $ctrl->inativar(); break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
        }
        break;

    default:
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => false, 'mensagem' => 'Controller não encontrado']);
}
