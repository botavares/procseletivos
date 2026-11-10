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
    public function reprocessar(int $processo, int $cargo, ?int $secretaria = null): void
    {
        $db = Database::connect();

        $calculator = new PontuacaoCalculatorService($db);
        $processor  = new ClassificacaoProcessorService($db, $calculator);
        $persist    = new ClassificacaoPersistService($db);

        if ($secretaria === null) {
            $secretaria = $this->resolverSecretaria($processo, $cargo);
        }

        $dados = $processor->processar($processo, $cargo);

        $persist->salvar($processo, $cargo, $dados, $secretaria);
    }

    /**
     * Resolve fk_id_secretaria a partir do cargo ou do processo.
     */
    private function resolverSecretaria(int $processo, int $cargo): int
    {
        $db = Database::connect();

        $cargoRow = $db->table('tb_cargos')
            ->where('pk_id_cargo', $cargo)
            ->get()
            ->getRow();

        if (!empty($cargoRow->fk_id_secretaria)) {
            return (int) $cargoRow->fk_id_secretaria;
        }

        $processoRow = $db->table('tb_processos')
            ->where('pk_id_processo', $processo)
            ->get()
            ->getRow();

        return (int) ($processoRow->fk_id_secretaria ?? 0);
    }

    /**
     * Lista classificacao por processo e cargo.
     * Retorna dados com scores dinamicos se existirem.
     */
    public function listarClassificacao($idProcesso, $idCargo, ?int $idSecretaria = null)
    {
        return $this->classificacaoRepository->listarPorProcessoCargo($idProcesso, $idCargo, $idSecretaria);
    }
}
