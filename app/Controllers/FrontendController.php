<?php

class FrontendController
{
    public function dashboard(): void
    {
        require __DIR__ . '/../Middleware/auth.php';
        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function pessoas(): void
    {
        require __DIR__ . '/../Middleware/auth.php';
        require __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tiposAtendimentos(): void
    {
        require __DIR__ . '/../Middleware/auth.php';
        require __DIR__ . '/../Views/tipos-atendimentos/index.php';
    }

    public function atendimentos(): void
    {
        require __DIR__ . '/../Middleware/auth.php';
        require __DIR__ . '/../Views/atendimentos/index.php';
    }

    public function relatorios(): void
    {
        require __DIR__ . '/../Middleware/auth.php';
        require __DIR__ . '/../Views/relatorios/index.php';
    }

    public function usuarios(): void
    {
        require __DIR__ . '/../Middleware/auth.php';
        if (($_SESSION['usuario']['perfil'] ?? '') !== 'admin') {
            header('Location: /atendelab/public/?controller=frontend&action=dashboard');
            exit;
        }
        require __DIR__ . '/../Views/usuarios/index.php';
    }
}
