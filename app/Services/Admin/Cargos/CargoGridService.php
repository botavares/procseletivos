<?php

namespace App\Services\Admin\Cargos;

use App\Services\Admin\Base\AbstractGridService;
use App\Models\Admin\CargosModel;

class CargoGridService extends AbstractGridService{
    public function __construct()
    {
        parent::__construct(new CargosModel());
        $this->setColumns(['Nome do cargo', 'Carga Horária', 'Experiências','Escolaridades','Curso de aperfeiçoamento','Critérios Adicionais'])->setOrder('ds_nome_cargo', 'asc');
    }

    public function cargos(): array{
        return $this->get();
    }
}
