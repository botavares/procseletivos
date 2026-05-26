<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use chillerlan\QRCode\{QRCode, QROptions};
use App\Modules\Breadcrumbs\Breadcrumbs;



    class PerguntasFrequentes extends BaseController{
        protected $breadcrumbs;
       
    public function __construct(){
        $this->breadcrumbs = new Breadcrumbs();
        $this->breadcrumbs = [
            'breads' => $this->breadcrumbs->buildAuto(),
        ];
       
    }
    public function index($camada1 = '', $camada2 = 'pages'){
        $PerguntasFrequentes = new \App\Models\PerguntasFrequentesModel();
        $page = 'PerguntasFrequentes';
        if(!checklogged()){
            $layout = 'layoutSimples';
        }else{
            $layout = 'layoutLogado';
        }
            
        $titulosTabela = array(
            'Pergunta'
        );

        $perguntas = $PerguntasFrequentes->findAll();
           
        
            $parametros = [
                'camada1'       =>  $camada1,
                'camada2'       =>  $camada2,
                'pagina'        =>  $page,
                'dados'         =>  $perguntas,
                'titulo'        =>  ucfirst('Perguntas Frequentes'),
                'titulosTabela' =>  $titulosTabela,
                'dataAtual'     =>  date('d/m/Y'),
            ];
            echo view($layout,$parametros);
        }

        public function buscarRespostas(){
            if(isset($_GET['q'])){
                $busca = $_GET['q'];
                $perguntasModel = new \App\Models\PerguntasFrequentesModel();
                $dataBusca = $perguntasModel->where('pk_id_pergunta', $busca)->first();
                return  $this->response->setJSON($dataBusca);
            }
        }

    }

    