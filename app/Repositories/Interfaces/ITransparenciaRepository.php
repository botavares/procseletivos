<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface ITransparenciaRepository
{
    /**
     * Lista classificação com filtros aplicados
     */
    public function listarClassificacao(
        int $pagina,
        int $porPagina,
        int $cargo,
        string $busca,
        bool $usaDesempateDinamico,
        array $colunasDinamicas,
        array $criteriosAdicionais,
        ?int $idProcesso = null
    ): array;

    /**
     * Busca colunas dinâmicas (scores) para o cargo/process
     */
    public function buscarColunasDinamicas(int $cargo, string $busca, ?int $idProcesso = null): array;

    /**
     * Carrega nomes das referências do cargo
     */
    public function carregarNomesReferencias(int $cargo): array;

    /**
     * Verifica quais colunas de pontuação estão vazias
     */
    public function obterColunasOcultas(int $cargo, string $busca, ?int $idProcesso = null): array;

    /**
     * Busca critérios adicionais ativos para o cargo/processo
     */
    public function obterCriteriosAdicionaisAtivos(int $cargo, string $busca, ?int $idProcesso = null): array;
}
