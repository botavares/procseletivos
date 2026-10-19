<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;
use App\Services\Base\SessaoService;
use App\Services\EditaisService;
use App\Services\ServicoService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Editais extends BaseController
{
    protected EditaisService $editaisService;
    protected ServicoService $servicoService;
    protected SessaoService $sessaoService;

    public function __construct()
    {
        $this->editaisService = service('editaisService');
        $this->servicoService = service('servicoService');
        $this->sessaoService = service('sessao');
    }

    public function index($camada1 = '', $camada2 = 'pages', $page = 'Editais')
    {
        if (!is_file(APPPATH . 'Views/Publica/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("página não encontrada: " . $page);
        }

        $editaisAtivos = $this->editaisService->listarEditaisAtivos();
        $editaisEncerrados = $this->editaisService->listarEditaisEncerrados();
        $cargos = $this->editaisService->listarTodosCargos();

        $cargosPorEdital = $this->editaisService->listarCargosPorEditais($editaisAtivos);
        $cargosPorEditalEncerrados = $this->editaisService->listarCargosPorEditais($editaisEncerrados);

        $layout = $this->sessaoService->estaLogado() ? 'Publica/layoutLogado' : 'Publica/layoutSimples';

        $parametros = [
            'camada1'                   => $camada1,
            'camada2'                   => $camada2,
            'pagina'                    => $page,
            'titulo'                    => ucfirst('Editais Ativos'),
            'editais'                   => $editaisAtivos,
            'editaisEncerrados'         => $editaisEncerrados,
            'cargos'                    => $cargos,
            'cargosPorEdital'           => $cargosPorEdital,
            'cargosPorEditalEncerrados' => $cargosPorEditalEncerrados,
            'dataAtual'                 => date('d/m/Y'),
        ];

        return view($layout, $parametros);
    }

    public function obterEdital($arquivo)
    {
        $path = ROOTPATH . 'writable/uploads/Editais/' . $arquivo;

        if (!is_file($path)) {
            throw new PageNotFoundException('Arquivo não encontrado.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $arquivo . '"')
            ->setBody(file_get_contents($path));
    }

    public function buscarServicosServidores()
    {
        $busca = $this->request->getGet('q');

        if ($busca === null) {
            return $this->response->setJSON([]);
        }

        $dataBusca = $this->servicoService->buscarServicosServidores((string) $busca);

        return $this->response->setJSON($dataBusca);
    }

    public function autenticarEdital($token)
    {
        $edital = $this->editaisService->autenticarEditalPorToken($token);

        if (!$edital) {
            throw new PageNotFoundException('Edital não encontrado ou token inválido.');
        }

        $parametros = [
            'titulo' => 'Autenticação de Edital',
            'edital' => $edital,
        ];

        return view('Publica/editais/autenticar_edital_view', $parametros);
    }
}
