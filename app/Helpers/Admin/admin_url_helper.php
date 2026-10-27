<?php
/**
 * Helper de URL para o painel administrativo.
 * Prefixa automaticamente 'adm/' para garantir que links internos
 * do admin apontem para o grupo de rotas correto.
 */
if (!function_exists('base_url_adm')) {
    function base_url_adm(string $path = ''): string
    {
        return base_url('adm/' . ltrim($path, '/'));
    }
}
