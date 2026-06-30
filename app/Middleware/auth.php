<?php
/**
 * Middleware de autenticação
 * Verifica se o usuário está logado; caso contrário, redireciona para o login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: /atendelab/public/?controller=auth&action=exibirLogin');
    exit;
}
