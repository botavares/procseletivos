<?php
namespace App\Services\Cargos;
//acionando o servico construtor de grid padrão
use App\Services\Base\AbstractGridService;
// chamando o model de Cargos
use App\Models\CargosModel;

class CargoGridService extends AbstractGridService
{
    public function __construct(){
        //instanciando o model
        parent::__construct(new CargosModel());
        $this->setColumns(['Nome do setor'])->setOrder('ds_nome_Cargo', 'asc');
    }
    public function Cargos(): array{
        return $this->get();
    }
}