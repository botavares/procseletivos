<?php

declare(strict_types=1);

namespace App\Services\Transparencia;

use App\Services\Interfaces\ITransparenciaFormService;

class TransparenciaFormService implements ITransparenciaFormService
{
    public function __construct(
        private \CodeIgniter\HTTP\IncomingRequest $request,
    ) {}

    public function listarClassificacao(): array
    {
        $params = [
            'page'     => (int) $this->request->getGet('page') ?: 1,
            'per_page' => (int) $this->request->getGet('per_page') ?: 10,
            'search'   => trim((string) $this->request->getGet('search')),
            'cargo'    => (int) $this->request->getGet('cargo'),
            'processo' => (int) $this->request->getGet('processo'),
        ];

        $service = service('transparenciaService');

        $processos = service('processosSeletivosService')->listarProcessosTransparencia();

        return array_merge(
            $service->listarClassificacao($params),
            [
                'processos' => $processos,
                'filtros'   => $params,
            ]
        );
    }
}
