<?php

namespace App\Services\CriteriosAdicionais;

use App\Services\Base\AbstractGridService;
use App\Models\CriteriosAdicionaisModel;

class CriterioAdicionalGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new CriteriosAdicionaisModel());
        $this->setColumns(['Nome do Critério'])
             ->setOrder('ds_nome_criterio', 'asc');
    }

    public function criterios(): array
    {
        return $this->get();
    }
}
