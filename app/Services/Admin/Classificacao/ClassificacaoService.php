<?php

namespace App\Services\Admin\Classificacao;

use Config\Database;
use App\Repositories\Admin\Interfaces\IClassificacaoRepository;

/**
 * Servico principal responsavel por
 * coordenar todo o reprocessamento.
 */
class ClassificacaoService
{
    public function __construct(
        ?IClassificacaoRepository $classificacaoRepository = null
    ) {
        $this->classificacaoRepository = $classificacaoRepository ?? service('classificacaoRepository');
    }

    protected IClassificacaoRepository $classificacaoRepository;

    /**
     * Reprocessa a classificacao completa.
     * Agora detecta automaticamente se o cargo possui desempate dinamico.
     */
    public function reprocessar(int $processo, int $cargo): void
    {
        $db = Database::connect();

        $calculator = new PontuacaoCalculatorService($db);
        $processor  = new ClassificacaoProcessorService($db, $calculator);
        $persist    = new ClassificacaoPersistService($db);

        $dados = $processor->processar($processo, $cargo);

        $persist->salvar($processo, $cargo, $dados);
    }

    /**
     * Lista classificacao por processo e cargo.
     * Retorna dados com scores dinamicos se existirem.
     */
    public function listarClassificacao($idProcesso, $idCargo)
    {
        return $this->classificacaoRepository->listarPorProcessoCargo($idProcesso, $idCargo);
    }
}
