<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Capa'){

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        
        


        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'titulo'    =>  ucfirst('Serviços Divinópolis '.date('Y')),
            'dataAtual' =>  date('d/m/Y'),
        ];
        echo view('layoutSimples',$parametros);
    }
}
