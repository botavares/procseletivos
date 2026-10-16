<?php

namespace App\Services\Escolaridades;

use App\Services\Base\AbstractFormService;
use App\DTOs\Domain\Escolaridades\EscolaridadeDTO;

class EscolaridadeFormService extends AbstractFormService
{
    protected function normalize(): array
    {
        $data = [
            'pk_id_escolaridade'  => $this->request->getPost('pk_id_escolaridade'),
            'ds_nome_escolaridade' => $this->request->getPost('ds_nome_escolaridade'),
            'ds_instrucao'        => $this->request->getPost('ds_instrucao'),
            'fk_id_nivel'         => $this->request->getPost('fk_id_nivel'),
        ];

        return [
            'acao'         => $this->request->getPost('action') ?? 'create',
            'escolaridade' => EscolaridadeDTO::fromRequest($data),
        ];
    }

    public function validate(array $data): void
    {
        $dto = $data['escolaridade'];

        $this->require(['ds_nome_escolaridade' => $dto->ds_nome_escolaridade], 'ds_nome_escolaridade', 'O nome da escolaridade deve ser informado.');
        $this->require(['fk_id_nivel' => $dto->fk_id_nivel], 'fk_id_nivel', 'O nível deve ser selecionado.');
    }
}
