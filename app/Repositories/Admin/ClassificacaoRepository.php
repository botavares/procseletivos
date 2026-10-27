<?php

declare(strict_types=1);

namespace App\Repositories\Admin;

use App\Models\Admin\ClassificacaoModel;
use App\Repositories\Admin\Interfaces\IClassificacaoRepository;

/**
 * Implementação de Repository para o domínio Classificação.
 */
class ClassificacaoRepository implements IClassificacaoRepository
{
    public function __construct(
        private ClassificacaoModel $model
    ) {}

    public function listarPorEditalCargo(int $idEdital, int $idCargo): array
    {
        return $this->model->listarClassificacao($idEdital, $idCargo);
    }
}
