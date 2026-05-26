<?php

namespace App\Services\Classificacao;

use Config\Database;
use App\Models\ClassificacaoModel;
use App\Models\EditaisModel;
use App\Models\CargosModel;

/**
 * Serviço principal responsável por
 * coordenar todo o reprocessamento.
 */
class ClassificacaoService{
    public function reprocessar(int $edital, int $cargo): void{
        $db = Database::connect();

        $calculator = new PontuacaoCalculatorService($db);
        $processor  = new ClassificacaoProcessorService($db, $calculator);
        $persist    = new ClassificacaoPersistService($db);

        $dados = $processor->processar($edital, $cargo);

        $persist->salvar($edital, $cargo, $dados);
    }

    public function listarClassificacao($idEdital, $idCargo){
        $classificacaoModel = new ClassificacaoModel();
        return $classificacaoModel->listarClassificacao($idEdital, $idCargo);
    }
    public function listarCargos(){
        $cargosModel = new CargosModel();
        return $cargosModel->findAll();
    }

}