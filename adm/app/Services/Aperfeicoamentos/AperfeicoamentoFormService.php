<?php

namespace App\Services\Aperfeicoamentos;

use App\Services\Base\AbstractFormService;
use App\DTOs\Domain\Aperfeicoamentos\AperfeicoamentoDTO;

class AperfeicoamentoFormService extends AbstractFormService
{
    protected function normalize(): array
    {
        $data = [
            'pk_id_curso'  => $this->request->getPost('pk_id_curso'),
            'ds_nome_curso' => $this->request->getPost('ds_nome_curso'),
        ];

        return [
            'acao'         => $this->request->getPost('action') ?? 'create',
            'aperfeicoamento' => AperfeicoamentoDTO::fromRequest($data),
        ];
    }

    public function validate(array $data): void
    {
        $dto = $data['aperfeicoamento'];

        $this->require(['ds_nome_curso' => $dto->ds_nome_curso], 'ds_nome_curso', 'O nome do curso deve ser informado.');
    }
}
