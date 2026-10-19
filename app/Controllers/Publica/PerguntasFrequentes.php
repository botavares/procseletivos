<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;
use App\Services\PerguntaFrequenteService;
use App\Modules\Breadcrumbs\Breadcrumbs;
use CodeIgniter\Exceptions\PageNotFoundException;

class PerguntasFrequentes extends BaseController
{
    protected PerguntaFrequenteService $perguntaFrequenteService;
    protected array $breadcrumbs;

    public function __construct()
    {
        $breadcrumb = new Breadcrumbs();
        $this->breadcrumbs = [
            'breads' => $breadcrumb->buildAuto(),
        ];
        $this->perguntaFrequenteService = service('perguntaFrequenteService');
    }

    public function index($camada1 = '', $camada2 = 'pages')
    {
        $page = 'PerguntasFrequentes';

        if (!is_file(APPPATH . 'Views/Publica/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("página não encontrada: " . $page);
        }

        $layout = checklogged() ? 'Publica/layoutLogado' : 'Publica/layoutSimples';

        $titulosTabela = [
            'Pergunta',
        ];

        $perguntas = $this->perguntaFrequenteService->listarTodas();

        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'dados'         => $perguntas,
            'titulo'        => ucfirst('Perguntas Frequentes'),
            'titulosTabela' => $titulosTabela,
            'dataAtual'     => date('d/m/Y'),
        ];

        return view($layout, array_merge($parametros, $this->breadcrumbs));
    }

    public function buscarRespostas()
    {
        $busca = $this->request->getGet('q');

        if ($busca === null) {
            return $this->response->setJSON(null);
        }

        $dataBusca = $this->perguntaFrequenteService->buscarPorId((int) $busca);

        return $this->response->setJSON($dataBusca);
    }
}
