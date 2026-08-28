<?php
namespace App\Services;

use App\Services\Base\AbstractCrudService;
use App\Models\EditaisModel;
use App\Models\EditaisCargosModel;


class EditaisService extends AbstractCrudService{
    protected EditaisModel $modelEditais;
    protected EditaisCargosModel $modelEditaisCargos;

    public function __construct(){
        $this->modelEditais = new EditaisModel();
        $this->modelEditaisCargos = new EditaisCargosModel();
    }

    public function listarTodosEditais(): array{
        return $this->modelEditais->listarTodosEditais()->getResultArray();
    }
    public function buscarEditaisAtivosCargos(){
        return $this->modelEditaisCargos->getEditaisAtivosCargos();
    }
    public function estaAtivo($idEdital){
        $dadosEdital = $this->modelEditais->where('pk_id_edital', $idEdital)->first();
        
        $dataInicialEdital = date('d/m/Y', strtotime($dadosEdital->ds_data_inicial));
        $dataFinalEdital = date('d/m/Y', strtotime($dadosEdital->ds_data_termino));

        //se hoje não estiver entre a data inicial e final do edital, redireciona para a página de opções de cadastro
        $hoje = date('Y-m-d');
        if ($hoje < $dadosEdital->ds_data_inicial || $hoje > $dadosEdital->ds_data_termino) {
            $resposta = [
                'response' => false,
                'mensagemError' => "O período de cadastro para este edital é de {$dataInicialEdital} a {$dataFinalEdital}."
            ];
            return $resposta;
        }
        return true;
    }
}