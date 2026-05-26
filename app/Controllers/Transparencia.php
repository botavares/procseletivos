<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Database;

use App\Modules\Breadcrumbs\Breadcrumbs;

use App\Models\CargosModel;
use App\Models\EditaisModel;

use App\Services\Transparencia\TransparenciaService;
use App\Services\Transparencia\TransparenciaFormService;

class Transparencia extends BaseController
{
    protected array $breadcrumbs;

    public function __construct()
    {
        $breadcrumb = new Breadcrumbs();

        $this->breadcrumbs = [
            'breads' => $breadcrumb->buildAuto(),
        ];
    }

    /* =====================================================
     * Página principal da Transparência
     * ===================================================== */
    public function index(
        string $camada1 = 'pages',
        string $camada2 = 'Transparencia',
        string $page    = 'Transparencia'
    ) {
        return $this->renderPagina([
            'acao'      => 'transparencia',
            'urlVoltar' => site_url('Dashboard'),
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'    => $page,
        ]);
    }

    /* =====================================================
     * Carregamento da tabela via AJAX
     * ===================================================== */
    public function carregarTabela()
    {
        if (!$this->request->isAJAX()) {
            throw new PageNotFoundException();
        }

        $params = [
            'page'     => $this->request->getGet('page'),
            'per_page' => $this->request->getGet('per_page'),
            'search'   => $this->request->getGet('search'),
            'edital'   => $this->request->getGet('edital'),
            'cargo'    => $this->request->getGet('cargo'),
        ];

        $service = new TransparenciaService(
            Database::connect(),
            new CargosModel(),
            new EditaisModel()
        );

        $resultado = $service->listarClassificacao($params);

        return view(
            'pages/Transparencia/partials/_tabela_candidatos_view',
            $resultado
        );
    }

    /* =====================================================
     * Renderização principal
     * ===================================================== */
    private function renderPagina(array $params)
    {
        $this->validarView(
            $params['camada1'],
            $params['camada2'],
            $params['pagina']
        );

        $formService = new TransparenciaFormService(
            $this->request,
            new CargosModel(),
            new EditaisModel()
        );

        $dadosFormulario = $formService->listarClassificacao();

        $layout = checklogged() ? 'layoutLogado' : 'layoutSimples';

        return view(
            $layout,
            array_merge(
                $params,
                $this->breadcrumbs,
                $dadosFormulario
            )
        );
    }

    /* =====================================================
     * Validações auxiliares
     * ===================================================== */

    private function validarView(
        string $camada1,
        string $camada2,
        string $page
    ): void {
        $path = APPPATH . "Views/{$camada1}/{$camada2}/{$page}_view.php";

        if (!is_file($path)) {
            throw new PageNotFoundException(
                "Página não encontrada: {$page}"
            );
        }
    }
}