<?php
/*Quem usa:
Control: Contratos.php
*/

namespace App\Controllers\Admin;

use App\Models\Admin\EditaisModel;

use CodeIgniter\Controller;

use CodeIgniter\Exceptions\PageNotFoundException;

class Dashboard extends BaseController{
    protected $editaisModel;

    public function __construct(){
        $this->editaisModel = new \App\Models\Admin\EditaisModel();
    }
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Dashboard'){
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $editaisAtivos = $this->editaisModel->getEditaisAtivos();
        $todosEditais = $this->editaisModel->getTodosEditais();
        $cargosComContagem = $this->editaisModel->getCargosComContagemCandidatosPorEditalAtivo();
        

        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'editais'           =>  $editaisAtivos,
            'todosEditais'      =>  $todosEditais,
            'cargosContagem'    =>  $cargosComContagem,
            'perfil'            =>  session('perfil'),
            'administrador'     =>  session('administrador'),
			'user'		        =>	session('nome'),
            'titulo'            =>  "Serviços Prefeitura Municipal de Divinópolis",
        ];

        echo view('Admin/layoutDash', $parametros);
    }
}