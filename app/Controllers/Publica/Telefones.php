<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;
use App\Services\SetorService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Telefones extends BaseController
{
    protected SetorService $setorService;

    public function __construct()
    {
        $this->setorService = service('setorService');
    }

    public function index($camada1 = '', $camada2 = 'pages', $page = 'Telefones')
    {
        if (!is_file(APPPATH . 'Views/Publica/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("página não encontrada: " . $page);
        }

        $setores = $this->setorService->listarTodos();

        $parametros = [
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'    => $page,
            'titulo'    => ucfirst('Governo Digital'),
            'telefones' => [],
            'unidades'  => [],
            'setores'   => $setores,
            'dataAtual' => date('d/m/Y'),
        ];

        return view('Publica/layoutSimples', $parametros);
    }

    public function buscarTelefones()
    {
        return $this->response->setJSON([]);
    }
}
