<?php

declare(strict_types=1);

namespace App\Services\Admin\Export;

/**
 * Contrato base para serviços de exportação.
 */
interface ExportadorInterface
{
    /**
     * Executa a exportação enviando headers e output diretamente ao navegador.
     *
     * @param array $dados Dados brutos a serem exportados.
     * @param array $config Configuração de metadados (ex.: nome do arquivo, títulos).
     */
    public function exportar(array $dados, array $config): void;
}
