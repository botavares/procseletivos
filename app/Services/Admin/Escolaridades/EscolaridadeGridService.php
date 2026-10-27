<?php

namespace App\Services\Admin\Escolaridades;

use App\Services\Admin\Base\AbstractGridService;
use App\Models\Admin\EscolaridadesModel;

class EscolaridadeGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new EscolaridadesModel());
        $this->setColumns(['Nome da Escolaridade', 'Instrução', 'Nível'])
             ->setOrder('ds_nome_escolaridade', 'asc');
    }

    public function escolaridades(): array
    {
        return $this->get();
    }
}
