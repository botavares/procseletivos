<?php

namespace App\Services\Experiencias;

use App\Services\Base\AbstractGridService;
use App\Models\ExperienciasModel;

class ExperienciaGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new ExperienciasModel());
        $this->setColumns(['Nome da Experiência', 'Tipo de Experiência'])
             ->setOrder('ds_nome_experiencia', 'asc');
    }

    public function experiencias(): array
    {
        return $this->get();
    }
}
