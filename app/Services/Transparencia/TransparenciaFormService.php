<?php

namespace App\Services\Transparencia;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\CargosModel;
use App\Models\EditaisModel;
use Config\Database;

class TransparenciaFormService
{
    public function __construct(
        private IncomingRequest $request,
        private CargosModel $cargosModel,
        private EditaisModel $editaisModel
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

        $service = new TransparenciaService(
            Database::connect(),
            $this->cargosModel,
            $this->editaisModel
        );

        $cargos = $this->cargosModel->cargosEditaisAtivos();

        $editais = $this->editaisModel
            ->orderBy('pk_id_edital', 'DESC')
            ->findAll();

        return array_merge(
            $service->listarClassificacao($params),
            [
                'cargos'  => $cargos,
                'editais' => $editais,
                'filtros' => $params
            ]
        );
    }
}