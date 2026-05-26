<?php

namespace App\Services\Editais;

use App\Models\EditaisModel;
use App\Services\Base\AbstractGridService;

class EditalGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new EditaisModel());

        $this->setColumns([
            'Número do edital',
            'Data inicial',
            'Data final',
            'Status'
        ])
        ->setOrder('ds_data_inicial', 'asc');
    }

    public function ativos(): array
    {
        return $this->get(['ds_status' => '1']);
    }

    public function encerrados(): array
    {
        return $this->get(['ds_status' => '0']);
    }
}
