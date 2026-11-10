<?php
/*Quem usa:
Control: Contratos.php
*/

namespace App\Controllers\Admin;

use App\Services\ProcessosSeletivos\ProcessosSeletivosService;

use CodeIgniter\Controller;

use CodeIgniter\Exceptions\PageNotFoundException;

class Dashboard extends BaseController{
    protected $ProcessosSeletivosService;

    public function __construct(){
        $this->ProcessosSeletivosService = service('ProcessosSeletivosService');
    }
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Dashboard'){
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $idSecretaria = (int) session('fk_id_secretaria');

        $processosAtivos = $this->ProcessosSeletivosService->listarAtivosComSituacaoPorSecretaria($idSecretaria);
        $todosProcessos = $this->ProcessosSeletivosService->listarTodosPorSecretaria($idSecretaria);
        //$cargosComContagem = $this->ProcessosSeletivosService->getCargosComContagemCandidatosPorEditalAtivo();
        

        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'processosAtivos'   =>  $processosAtivos,
            'todosProcessos'    =>  $todosProcessos,
            //'cargosContagem'    =>  $cargosComContagem,
            'perfil'            =>  session('perfil'),
            'administrador'     =>  session('administrador'),
			'user'		        =>	session('nome'),
            'titulo'            =>  "Serviços Prefeitura Municipal de Divinópolis",
        ];

        echo view('Admin/layoutDash', $parametros);
    }
}