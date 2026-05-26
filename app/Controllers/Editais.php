<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use \App\Models\EditaisModel;
use \App\Models\ArquivoModel;
use \App\Models\CargosModel;
use \App\Models\AutenticadorModel;

class Editais extends BaseController
{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Editais'){

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        //Destaques
        $editais    =   new EditaisModel();
        $editaisAtivos = $editais->where('ds_status','1')->orderBy('ds_data_inicial','DESC')->findAll();
        $editaisEncerrados = $editais->where('ds_status','0')->orderBy('ds_data_inicial','DESC')->findAll();

        $modelCargos  =   new CargosModel();
        $cargos = $modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll();

        
        if (checklogged()) {
            $layout = 'layoutLogado';
        }else{
            $layout = 'layoutSimples';
        }

        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'titulo'        =>  ucfirst('Editais Ativos'),
            'editais'       =>  $editaisAtivos,
            'cargos'        =>  $cargos,
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view($layout,$parametros);
    }

    
    
    public function obterEdital($arquivo)
    {
        $path = ROOTPATH . 'writable/uploads/Editais/' .  $arquivo;

        /*$path = dirname(ROOTPATH)
        . DIRECTORY_SEPARATOR . 'writable'
        . DIRECTORY_SEPARATOR . 'uploads'
        . DIRECTORY_SEPARATOR . 'Editais'
        . DIRECTORY_SEPARATOR . $arquivo;
*/
    
        if (!is_file($path)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Arquivo não encontrado.');
        }
    
        //return $this->response->download($path, null);

        return $this->response
    ->setHeader('Content-Type', 'application/pdf')
    ->setHeader('Content-Disposition', 'inline; filename="' . $arquivo . '"')
    ->setBody(file_get_contents($path));
    }


	
	public function buscarServicosServidores(){
        if(isset($_GET['q'])){
            $busca = $_GET['q'];
            $servicosModel = new \App\Models\ServicosModel();
            $dataBusca = $servicosModel->listarServicosServidores($busca);
            echo json_encode($dataBusca);
        }
    }

    public function autenticarEdital($token)
    {
        $editaisModel = new EditaisModel();
        $edital = $editaisModel->where('ds_token_autenticacao', $token)->first();

        if (!$edital) {
            throw new PageNotFoundException('Edital não encontrado ou token inválido.');
        }

        $parametros = [
            'titulo' => 'Autenticação de Edital',
            'edital' => $edital,
        ];

        echo view('editais/autenticar_edital_view', $parametros);
    }

	
}
