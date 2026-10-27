<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Capa'){

        $viewPath = APPPATH . 'Views/Admin/' . ($camada1 ? $camada1 . '/' : '') . $camada2 . '/' . $page . '_view.php';
        if (! is_file($viewPath)) {
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
        echo view('Admin/layoutLogin',$parametros);
    }
}
