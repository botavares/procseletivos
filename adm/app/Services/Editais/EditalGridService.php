<?php

namespace App\Services\Editais;

use App\Models\EditaisModel;
use App\Services\Base\AbstractGridService;
use App\Enums\EditalStatusEnum;

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

    public function ativos(): array{
        //editais ds_status 1 e 2
        $ativos     = $this->get(['ds_status' => EditalStatusEnum::ATIVO->value]);
        $publicados = $this->get(['ds_status' => EditalStatusEnum::PUBLICADO->value]);

        return [
            'data'    => array_merge($ativos['data'], $publicados['data']),
            'columns' => $this->columns,
        ];
    }

    public function encerrados(): array
    {
        return $this->get(['ds_status' => EditalStatusEnum::ENCERRADO->value]);
    }
}
