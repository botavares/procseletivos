<?php

namespace App\Services\Aperfeicoamentos;

use App\Services\Base\AbstractGridService;
use App\Models\AperfeicoamentosModel;

class AperfeicoamentoGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new AperfeicoamentosModel());
        $this->setColumns(['Nome do Curso'])
             ->setOrder('ds_nome_curso', 'asc');
    }

    public function aperfeicoamentos(): array
    {
        return $this->get();
    }
}
