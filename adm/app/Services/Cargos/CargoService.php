<?php
namespace App\Services\Cargos;
use App\Services\Base\AbstractCrudService;
use App\Models\CargosModel;

class CargoService extends AbstractCrudService{
    public function __construct(){
        parent::__construct();
    }
    public function salvar(array $Cargo): int{
        return $this->transactional(function () use ($Cargo) {
            $model = new CargosModel();
            if(! $model->save($Cargo)){
                throw new \RuntimeException(
                'Erro ao salvar Cargo: ' . implode('; ', $model->errors()));
            }
            $id = $Cargo['pk_id_Cargo'] ?? $model->getInsertID();
            if (empty($id)) {
                throw new \RuntimeException('ID do Cargo nao foi gerado');
            }   
            return $id;
        });
    }

    public function deletar(int $id): void{
        $this->transactional(function () use ($id) {
            $model = new CargosModel();
            if(! $model->delete($id)){
                throw new \RuntimeException('Erro ao excluir o Cargo');
            }
        });
    }
    public function listarCargosId($idCargo = null){
        $model = new CargosModel();
        if ($idCargo !== null) {
            return $model->find($idCargo);
        }
        
    }
}