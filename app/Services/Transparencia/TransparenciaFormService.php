<?php

declare(strict_types=1);

namespace App\Services\Transparencia;

use App\Models\CargosModel;
use App\Models\EditaisModel;
use App\Services\Interfaces\ITransparenciaFormService;

class TransparenciaFormService implements ITransparenciaFormService
{
    public function __construct(
        private \CodeIgniter\HTTP\IncomingRequest $request,
        private CargosModel $cargosModel,
        private EditaisModel $editaisModel,
    ) {}

    public function listarClassificacao(): array
    {
        $params = [
            'page'     => (int) $this->request->getGet('page') ?: 1,
            'per_page' => (int) $this->request->getGet('per_page') ?: 10,
            'search'   => trim((string) $this->request->getGet('search')),
            'cargo'    => (int) $this->request->getGet('cargo'),
            'edital'   => (int) $this->request->getGet('edital'),
        ];

        $service = service('transparenciaService');

        $cargos = $this->cargosModel->cargosEditaisAtivos();

        $editais = $this->editaisModel->listarEditaisVisiveis();

        return array_merge(
            $service->listarClassificacao($params),
            [
                'cargos'  => $cargos,
                'editais' => $editais,
                'filtros' => $params,
            ]
        );
    }
}
