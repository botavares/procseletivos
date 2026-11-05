<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;
use App\Services\Base\SessaoService;
use App\Services\Editais\EditaisPublicoService;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Controller da página pública de Editais (Processos Seletivos).
 */
class Editais extends BaseController
{
    protected EditaisPublicoService $editaisPublicoService;
    protected SessaoService $sessaoService;

    public function __construct(
        ?EditaisPublicoService $editaisPublicoService = null,
        ?SessaoService $sessaoService = null,
    ) {
        $this->editaisPublicoService = $editaisPublicoService ?? service('editaisPublicoService');
        $this->sessaoService = $sessaoService ?? service('sessao');
    }

    /**
     * Exibe a listagem pública de editais/processos seletivos com filtros.
     */
    public function index()
    {
        $camada1 = '';
        $camada2 = 'pages/Editais';
        $pagina  = 'DadosEditais';

        if (!is_file(APPPATH . 'Views/Publica/' . $camada2 . '/' . $pagina . '_view.php')) {
            throw new PageNotFoundException("página não encontrada: " . $pagina);
        }

        $filtros = [
            'situacao'   => $this->request->getGet('situacao') ?: 'Vigentes',
            'secretaria' => $this->request->getGet('secretaria') ?: '',
            'cargo'      => $this->request->getGet('cargo') ?: 'Todos',
        ];

        $editais    = $this->editaisPublicoService->listarEditais($filtros);
        $secretarias = $this->editaisPublicoService->obterSecretariasFiltro();
        $cargos     = $this->editaisPublicoService->obterCargosFiltro();

        $layout = $this->sessaoService->estaLogado() ? 'Publica/layoutLogado' : 'Publica/layoutSimples';

        $parametros = [
            'camada1'     => $camada1,
            'camada2'     => $camada2,
            'pagina'      => $pagina,
            'titulo'      => 'Editais (Processos Seletivos)',
            'editais'     => $editais,
            'secretarias' => $secretarias,
            'cargos'      => $cargos,
            'filtros'     => $filtros,
            'dataAtual'   => date('d/m/Y'),
        ];

        return view($layout, $parametros);
    }
}
