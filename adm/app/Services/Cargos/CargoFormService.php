<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractFormService;
use App\DTOs\Domain\Cargos\CargoDTO;

class CargoFormService extends AbstractFormService
{
    protected function normalize(): array
    {
        $cargoData = [
            'pk_id_cargo'       => $this->request->getPost('pk_id_cargo'),
            'fk_id_secretaria'  => $this->request->getPost('fk_id_secretaria'),
            'ds_nome_cargo'     => $this->request->getPost('ds_nome_cargo'),
            'ds_carga_horaria'  => $this->request->getPost('ds_carga_horaria'),
            'ds_nivel'          => $this->request->getPost('ds_nivel'),
        ];

        return [
            'acao'  => $this->request->getPost('action') ?? 'create',
            'cargo' => CargoDTO::fromRequest($cargoData),
        ];
    }

    public function validate(array $data): void
    {
        $cargo = $data['cargo'];

        $this->require(['ds_nome_cargo' => $cargo->ds_nome_cargo], 'ds_nome_cargo', 'O nome do cargo deve ser informado.');
        $this->require(['ds_carga_horaria' => $cargo->ds_carga_horaria], 'ds_carga_horaria', 'A carga horária deve ser informada.');
        $this->require(['ds_nivel' => $cargo->ds_nivel], 'ds_nivel', 'O nível de escolaridade deve ser informado.');
    }
}
