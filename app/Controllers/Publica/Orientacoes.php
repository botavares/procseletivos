<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;

class Orientacoes extends BaseController
{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Orientacoes')
    {
        $parametros = [
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'    => $page,
            'titulo'    => ucfirst('Orientações'),
            'dataAtual' => date('d/m/Y'),
        ];

        return view('Publica/layoutSimples', $parametros);
    }
}
