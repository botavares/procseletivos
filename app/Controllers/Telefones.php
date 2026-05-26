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
        
        $telefonesModel    =   new \App\Models\TelefonesModel();
        $unidadesModel    =   new \App\Models\UnidadesModel();
		$setoresModel    =   new \App\Models\SetoresModel();

        //telfones com Unidades
        $telefones = $telefonesModel->orderBy('fk_id_unidade', 'asc')->findAll();
        $unidades = $unidadesModel->orderby('ds_nome_unidade', 'asc')->findAll();
		$setores = $setoresModel->orderby('pk_id_setor', 'asc')->findAll();

        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'titulo'        =>  ucfirst('Governo Digital'),
            'telefones'     =>  $telefones,
            'unidades'      =>  $unidades,
			'setores'		=>	$setores,
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view('layoutSimples',$parametros);
    }

    public function buscarTelefones(){
        if(isset($_GET['q'])){
            $arrayBusca = [];
            $busca = $_GET['q'];
            $telefonesModel = new \App\Models\TelefonesModel();
            $unidadesModel = new \App\Models\UnidadesModel();
            
            $unidadeBusca  = $unidadesModel->buscarUnidades($busca);

            foreach($unidadeBusca as &$d){
                $d->telefones = $telefonesModel->where('fk_id_unidade', $d->pk_id_unidade)->findAll();
            }
            
            return json_encode($unidadeBusca);
        }

        
    }
}
