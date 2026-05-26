<?php
/*Quem usa:
Control: Contratos.php
*/

namespace App\Controllers;

use App\Models\DashModel;
use App\Models\AbrangenciasModel;
use App\Models\CursosModel;
use App\Models\EstagiariosModel;
use App\Models\EditaisModel;
use App\Models\DadosContratosModel;
use App\Models\VagasModel;
use App\Models\LogsModel;
use App\Models\DadosRescisaoModel;
use App\Models\DadosAditivosModel;
use App\Models\ConvocadosModel;
use App\Models\VerificadorModel;
use App\Models\EditaisCandidatosModel;

use App\Services\LogsService;
use App\Services\Contratos\ContratoService;

use CodeIgniter\Controller;

use CodeIgniter\Exceptions\PageNotFoundException;

class Dashboard extends BaseController{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Dashboard'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
          /*  $contratoService = new ContratoService(
                new DadosContratosModel(),
                new VagasModel(),
                new LogsService(),
                new DadosRescisaoModel(),
                new DadosAditivosModel(),
                new ConvocadosModel(),
                new VerificadorModel()
            );
            // Dispara a verificação automática
            //////$contratoService->verificarContratosVencidos();

        ////$dadosContratos = new DadosContratosModel();
        ////$contratosExpirando = $dadosContratos->getContratosExpirando(30);
        /*if(!$contratosExpirando){
            $contratosExpirando = [];
        }*/
    
        $modelEditais = new EditaisModel();
        $editaisAtivos = $modelEditais->where('ds_status','1')->findAll();
        

        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'editais'           =>  $editaisAtivos,
            'perfil'            =>  session('perfil'),
            'administrador'     =>  session('administrador'),
			'user'		        =>	session('nome'),
            //'contratosExpirando'=>  $contratosExpirando,
            'titulo'            =>  "Serviços Prefeitura Municipal de Divinópolis",
        
        ];

        echo view('layoutDash', $parametros);
    }
}