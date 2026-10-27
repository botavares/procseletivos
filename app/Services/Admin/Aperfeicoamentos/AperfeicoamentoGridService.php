<?php

namespace App\Services\Admin\Aperfeicoamentos;

use App\Services\Admin\Base\AbstractGridService;
use App\Models\Admin\AperfeicoamentosModel;

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
