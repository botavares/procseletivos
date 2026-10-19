<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SetoresModel;

class SetorService
{
    public function __construct(
        private SetoresModel $setoresModel,
    ) {}

    public function listarTodos(): array
    {
        return $this-\u003esetoresModel-\u003eorderBy('pk_id_setor', 'asc')-\u003efindAll();
    }
}
