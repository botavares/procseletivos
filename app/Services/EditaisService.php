<?php
namespace App\Services;

use App\Services\Base\AbstractCrudService;
use App\Models\EditaisModel;
use App\Models\EditaisCargosModel;
use App\Models\CargosModel;


class EditaisService extends AbstractCrudService{
    protected EditaisModel $modelEditais;
    protected EditaisCargosModel $modelEditaisCargos;
    protected CargosModel $modelCargos;

    public function __construct(){
        $this->modelEditais = new EditaisModel();
        $this->modelEditaisCargos = new EditaisCargosModel();
        $this->modelCargos = new CargosModel();
    }

    public function listarTodosEditais(): array{
        return $this->modelEditais->listarTodosEditais()->getResultArray();
    }

    public function buscarEditaisAtivosCargos(){
        return $this->modelEditaisCargos->getEditaisAtivosCargos();
    }

    public function listarEditaisAtivos(): array{
        return $this->modelEditais->where('ds_status', '1')->orderBy('ds_data_inicial', 'DESC')->findAll();
    }

    public function listarEditaisEncerrados(): array{
        return $this->modelEditais->where('ds_status', '0')->orderBy('ds_data_inicial', 'DESC')->findAll();
    }

    public function listarTodosCargos(): array{
        return $this->modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll();
    }

    public function listarCargosPorEditais(array $editais): array{
        $db = \Config\Database::connect();
        $cargosPorEdital = [];
        foreach ($editais as $edital) {
            $cargosVinculados = $db->table('tb_editais_cargos')
                ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo')
                ->where('tb_editais_cargos.fk_id_edital', $edital->pk_id_edital)
                ->get()
                ->getResult();
            $cargosPorEdital[$edital->pk_id_edital] = $cargosVinculados;
        }
        return $cargosPorEdital;
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