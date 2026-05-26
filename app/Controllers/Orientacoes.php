<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use \App\Models\EditaisModel;
use \App\Models\CursosModel;
use \App\Models\AutenticadorModel;

class Orientacoes extends BaseController{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Orientacoes'){
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'titulo'        =>  ucfirst('Orientações'),
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view('layoutSimples',$parametros);
    }
}
        
