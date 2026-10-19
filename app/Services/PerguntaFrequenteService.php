<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PerguntasFrequentesModel;

class PerguntaFrequenteService
{
    public function __construct(
        private PerguntasFrequentesModel $perguntasFrequentesModel,
    ) {}

    public function listarTodas(): array
    {
        return $this-\u003eperguntasFrequentesModel-\u003efindAll();
    }

    public function buscarPorId(int $id): ?object
    {
        return $this-\u003eperguntasFrequentesModel-\u003ewhere('pk_id_pergunta', $id)-\u003efirst();
    }
}
