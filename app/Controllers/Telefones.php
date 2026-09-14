<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
class Telefones extends BaseController
{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Telefones'){

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        
        $setoresModel    =   new \App\Models\SetoresModel();

        $setores = $setoresModel->orderBy('pk_id_setor', 'asc')->findAll();

        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'titulo'        =>  ucfirst('Governo Digital'),
            'telefones'     =>  [],
            'unidades'      =>  [],
            'setores'       =>  $setores,
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view('layoutSimples',$parametros);
    }

    public function buscarTelefones(){
        return json_encode([]);
    }
}
