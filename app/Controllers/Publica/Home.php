<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;
use App\Services\Base\SessaoService;
use App\Services\HomeService;
use App\Services\EditaisService;
use App\Services\ProcessosSeletivos\ProcessosSeletivosService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    protected HomeService $homeService;
    protected SessaoService $sessaoService;
    protected EditaisService $editaisService;
    protected ProcessosSeletivosService $processosSeletivosService;

    public function __construct()
    {
        $this->homeService = service('homeService');
        $this->sessaoService = service('sessao');
        $this->editaisService = service('editaisService');
        $this->processosSeletivosService = service('processosSeletivosService');
    }

    public function index($camada1 = '', $camada2 = 'pages', $page = 'Capa')
    {
        if (!is_file(APPPATH . 'Views/Publica/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("página não encontrada: " . $page);
        }

        $dadosHome = $this->homeService->obterDadosHome();
        $processosSeletivos = $this->processosSeletivosService->listarAtivosComSituacao();

        $parametros = [
            'camada1'            => $camada1,
            'camada2'            => $camada2,
            'params'             => $dadosHome['dataSession'],
            'processosSeletivos' => $processosSeletivos,
            'pagina'             => $page,
            'edital'             => $dadosHome['edital'],
            'status'             => $dadosHome['status'],
            "labelCaption"       => $dadosHome['labelCaption'],
            'dataAtual'          => date('d/m/Y'),
        ];

        return view($dadosHome['layout'], $parametros);
    }

    public function acessarGovBr()
    {
        if (!$this->sessaoService->estaLogado()) {
            $urlToGov = "https://app.prefeituradivinopolis.com.br/app/7ddde5c6897f39b7b139238d0dd94d7f?destino=Home";
            return redirect()->to($urlToGov);
        }

        return redirect()->to('Home');
    }
}
