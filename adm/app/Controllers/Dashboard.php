<?php
/*Quem usa:
Control: Contratos.php
*/

namespace App\Controllers;

use App\Models\EditaisModel;

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
        $editaisAtivos = $modelEditais->getEditaisAtivos();
        $cargosComContagem = $modelEditais->getCargosComContagemCandidatosPorEditalAtivo();
        

        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'editais'           =>  $editaisAtivos,
            'cargosContagem'    =>  $cargosComContagem,
            'perfil'            =>  session('perfil'),
            'administrador'     =>  session('administrador'),
			'user'		        =>	session('nome'),
            'titulo'            =>  "Serviços Prefeitura Municipal de Divinópolis",
        ];

        echo view('layoutDash', $parametros);
    }
}