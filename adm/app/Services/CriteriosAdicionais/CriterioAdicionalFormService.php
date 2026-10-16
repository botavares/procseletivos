<?php

namespace App\Services\CriteriosAdicionais;

use App\Services\Base\AbstractFormService;
use App\DTOs\Domain\CriteriosAdicionais\CriterioAdicionalDTO;

class CriterioAdicionalFormService extends AbstractFormService
{
    protected function normalize(): array
    {
        $data = [
            'pk_id_criterio'  => $this->request->getPost('pk_id_criterio'),
            'ds_nome_criterio' => $this->request->getPost('ds_nome_criterio'),
        ];

        return [
            'acao'    => $this->request->getPost('action') ?? 'create',
            'criterio' => CriterioAdicionalDTO::fromRequest($data),
        ];
    }

    public function validate(array $data): void
    {
        $dto = $data['criterio'];

        $this->require(['ds_nome_criterio' => $dto->ds_nome_criterio], 'ds_nome_criterio', 'O nome do critério deve ser informado.');
    }
}
