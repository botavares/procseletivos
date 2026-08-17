<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractGridService;
use App\Models\CargosModel;

class CargoGridService extends AbstractGridService{
    public function __construct()
    {
        parent::__construct(new CargosModel());
        $this->setColumns(['Nome do cargo', 'Carga Horária', 'Experiências','Escolaridades','Curso de aperfeiçoamento'])->setOrder('ds_nome_cargo', 'asc');
    }

    public function cargos(): array{
        return $this->get();
    }
}
