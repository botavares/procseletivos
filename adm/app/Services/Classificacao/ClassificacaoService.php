<?php

namespace App\Services\Classificacao;

use Config\Database;
use App\Models\ClassificacaoModel;
use App\Models\EditaisModel;
use App\Models\CargosModel;

/**
 * Servico principal responsavel por
 * coordenar todo o reprocessamento.
 */
class ClassificacaoService
{
    /**
     * Reprocessa a classificacao completa.
     * Agora detecta automaticamente se o cargo possui desempate dinamico.
     */
    public function reprocessar(int $edital, int $cargo): void
    {
        $db = Database::connect();

        $calculator = new PontuacaoCalculatorService($db);
        $processor  = new ClassificacaoProcessorService($db, $calculator);
        $persist    = new ClassificacaoPersistService($db);

        $dados = $processor->processar($edital, $cargo);

        $persist->salvar($edital, $cargo, $dados);
    }

    /**
     * Lista classificacao por edital e cargo.
     * Retorna dados com scores dinamicos se existirem.
     */
    public function listarClassificacao($idEdital, $idCargo)
    {
        $classificacaoModel = new ClassificacaoModel();
        return $classificacaoModel->listarClassificacao($idEdital, $idCargo);
    }

    public function listarCargos()
    {
        $cargosModel = new CargosModel();
        return $cargosModel->findAll();
    }
}
