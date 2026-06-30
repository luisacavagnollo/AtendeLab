<?php
/**
 * AtendeLab - Front Controller
 * Toda requisição passa por aqui e é despachada via routes.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL para uso nas views
$baseUrl = '/atendelab/public';

// Inclui configuração do banco
require_once __DIR__ . '/../config/database.php';

// Inclui o roteador
require_once __DIR__ . '/../routes.php';
