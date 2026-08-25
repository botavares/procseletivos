<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractFormService;
use App\DTOs\Domain\Cargos\CargosExperienciasDTO;

class CargosExperienciasFormService extends AbstractFormService
{
    protected function normalize(): array
    {
        $data = [
            'pk_id_cargos_experiencia' => $this->request->getPost('pk_id_cargos_experiencia'),
            'fk_id_cargo'               => $this->request->getPost('fk_id_cargo'),
            'fk_id_experiencia'         => $this->request->getPost('fk_id_experiencia'),
            'ds_quantidade_minima'      => $this->request->getPost('ds_quantidade_minima'),
            'ds_quantidade_maxima'      => $this->request->getPost('ds_quantidade_maxima'),
            'ds_multiplicador'          => $this->request->getPost('ds_multiplicador'),
            'ds_tipo_campo'             => $this->request->getPost('ds_tipo_campo'),
        ];

        return [
            'acao'                 => $this->request->getPost('acao') ?? 'create',
            'cargosExperiencias'   => CargosExperienciasDTO::fromRequest($data),
        ];
    }

    public function validate(array $data): void
    {
        $dto = $data['cargosExperiencias'];

        $this->require(['fk_id_cargo' => $dto->fk_id_cargo], 'fk_id_cargo', 'O cargo deve ser informado.');
        $this->require(['fk_id_experiencia' => $dto->fk_id_experiencia], 'fk_id_experiencia', 'A experiência deve ser selecionada.');
        $this->require(['ds_quantidade_minima' => $dto->ds_quantidade_minima], 'ds_quantidade_minima', 'A quantidade mínima deve ser informada.');
        $this->require(['ds_quantidade_maxima' => $dto->ds_quantidade_maxima], 'ds_quantidade_maxima', 'A quantidade máxima deve ser informada.');
        $this->require(['ds_multiplicador' => $dto->ds_multiplicador], 'ds_multiplicador', 'O multiplicador deve ser informado.');
        $this->require(['ds_tipo_campo' => $dto->ds_tipo_campo], 'ds_tipo_campo', 'O tipo de campo deve ser informado.');

        // Regra adicional: máximo deve ser >= mínimo
        if ($dto->ds_quantidade_minima !== null && $dto->ds_quantidade_maxima !== null) {
            if ($dto->ds_quantidade_maxima < $dto->ds_quantidade_minima) {
                throw new \Exception('A quantidade máxima deve ser maior ou igual à quantidade mínima.');
            }
        }
    }
}
