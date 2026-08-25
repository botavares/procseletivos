<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractFormService;
use App\DTOs\Domain\Cargos\CargosEscolaridadesDTO;

class CargosEscolaridadesFormService extends AbstractFormService
{
    protected function normalize(): array
    {
        $data = [
            'pk_id_cargos_escolaridade' => $this->request->getPost('pk_id_cargos_escolaridade'),
            'fk_id_cargo'               => $this->request->getPost('fk_id_cargo'),
            'fk_id_escolaridade'         => $this->request->getPost('fk_id_escolaridade'),
            'ds_pontuacao_minima'      => $this->request->getPost('ds_pontuacao_minima'),
            'ds_pontuacao_maxima'      => $this->request->getPost('ds_pontuacao_maxima'),
            'ds_multiplicador'          => $this->request->getPost('ds_multiplicador'),
            'ds_tipo_campo'             => $this->request->getPost('ds_tipo_campo'),
        ];

        return [
            'acao'                 => $this->request->getPost('acao') ?? 'create',
            'cargosEscolaridades'   => CargosEscolaridadesDTO::fromRequest($data),
        ];
    }

    public function validate(array $data): void
    {
        $dto = $data['cargosEscolaridades'];

        $this->require(['fk_id_cargo' => $dto->fk_id_cargo], 'fk_id_cargo', 'O cargo deve ser informado.');
        $this->require(['fk_id_escolaridade' => $dto->fk_id_escolaridade], 'fk_id_escolaridade', 'A escolaridade deve ser selecionada.');
        $this->require(['ds_pontuacao_minima' => $dto->ds_pontuacao_minima], 'ds_pontuacao_minima', 'A pontuação mínima deve ser informada.');
        $this->require(['ds_pontuacao_maxima' => $dto->ds_pontuacao_maxima], 'ds_pontuacao_maxima', 'A pontuação máxima deve ser informada.');
        $this->require(['ds_multiplicador' => $dto->ds_multiplicador], 'ds_multiplicador', 'O multiplicador deve ser informado.');
        $this->require(['ds_tipo_campo' => $dto->ds_tipo_campo], 'ds_tipo_campo', 'O tipo de campo deve ser informado.');

        // Regra adicional: máximo deve ser >= mínimo
        if ($dto->ds_pontuacao_minima !== null && $dto->ds_pontuacao_maxima !== null) {
            if ($dto->ds_pontuacao_maxima < $dto->ds_pontuacao_minima) {
                throw new \Exception('A pontuação máxima deve ser maior ou igual à pontuação mínima.');
            }
        }
    }
}
