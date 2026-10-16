<?php

namespace App\Services\Experiencias;

use App\Services\Base\AbstractFormService;
use App\DTOs\Domain\Experiencias\ExperienciaDTO;

class ExperienciaFormService extends AbstractFormService
{
    protected function normalize(): array
    {
        $data = [
            'pk_id_experiencia'  => $this->request->getPost('pk_id_experiencia'),
            'ds_nome_experiencia' => $this->request->getPost('ds_nome_experiencia'),
            'ds_tipo_experiencia' => $this->request->getPost('ds_tipo_experiencia'),
        ];

        return [
            'acao'       => $this->request->getPost('action') ?? 'create',
            'experiencia' => ExperienciaDTO::fromRequest($data),
        ];
    }

    public function validate(array $data): void
    {
        $dto = $data['experiencia'];

        $this->require(['ds_nome_experiencia' => $dto->ds_nome_experiencia], 'ds_nome_experiencia', 'O nome da experiência deve ser informado.');
        $this->require(['ds_tipo_experiencia' => $dto->ds_tipo_experiencia], 'ds_tipo_experiencia', 'O tipo de experiência deve ser informado.');
    }
}
