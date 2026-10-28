<?php

/**
 * Helpers de autenticação
 * 
 * Mantém compatibilidade com código legado que usa checklogged()
 * enquanto delega para o SessaoService moderno.
 */

if (!function_exists('checklogged')) {
    /**
     * Verifica se o usuário está logado na sessão.
     *
     * @return bool
     */
    function checklogged(): bool
    {
        return (bool) session('logged_in');
    }
}

if (!function_exists('base_url_adm')) {
    /**
     * Gera URL para o painel administrativo com prefixo /adm/
     *
     * @param string $path
     * @return string
     */
    function base_url_adm(string $path = ''): string
    {
        return base_url('adm/' . ltrim($path, '/'));
    }
}
