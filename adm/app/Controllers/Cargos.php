<?php

namespace App\Controllers;

use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CargosModel;

use App\Models\EditaisCargosModel;
use App\Services\LogsService;
use App\Services\Cargos\CargoFormService;
use App\Services\Cargos\CargoUploadService;
use App\Services\Cargos\CargoService;
use App\Services\Cargos\CargoGridService;


class Cargos extends BaseController{

    protected $cargosData;
    

    public function __construct(){
        $modelCargos = new CargosModel();
        
        $this->cargosData = [
           'cargos'         => $modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll(),
           
        ];
      
    }

    /*============================================================
    *LISTAGENS (PREENCHER O GRID INICIAL DAS MINHAS TELAS)
    =============================================================*/
    public function index($camada1 = '',$camada2 = 'pages', $page = 'Cargos') {
        return $this->listarParaGrid( $camada1, $camada2, $page);
    }
    
    private function listarParaGrid(string $camada1, string $camada2, string $page){
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $gridService = new CargoGridService();
        $grid = $gridService->cargos();

        if (!is_array($grid) ||!isset($grid['data'], $grid['columns'])) {
            throw new \RuntimeException('Grid retornado em formato inválido');
        }

        return view('layoutDash', [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'tipo'          => 'cargos',
            'titulo'        => 'Gerenciar Cargos',
            'cargos'       => $grid['data'],
            'titulosTabela' => $grid['columns'],
            'user'          => session('nome'),
        ]);
}


    /*============================================================
    * FORMULARIOS
    =============================================================*/
    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formCargos') {
        return $this->renderFormulario([
                                        'acao'     => 'create',
                                        'camada1'  => $camada1,
                                        'camada2'  => $camada2,
                                        'page'     => $page,
                    ]);
    }
    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formCargos'){
        return $this->renderFormulario([
                                        'id'                   => $id,
                                        'acao'                 => 'update',
                                        'camada1'              => $camada1,
                                        'camada2'              => $camada2,
                                        'page'                 => $page,
                                    ]);
    }

    private function renderFormulario(array $config){

        // criação de um config defaut
        $config = array_merge([
            'id'                   => null,
            'acao'                 => 'create',
            'camada1'              => 'pages',
            'camada2'              => 'cadastros',
            'page'                 => 'formCargos',
            'titulo'               => '',
        ],$config);

        $this->validarView($config['camada1'],$config['camada2'],$config['page']);
        $this->validarSessao();

        $dados = null;
        $cargos = [];
        $abrangencias = [];

        if($config['acao'] === 'update' && $config['id']){ // Aqui eu consigo pegar os dados do cargo se for atualização
             $dados = (new CargosModel())->getCargo($config['id']);
        }

        return view('layoutDash', [
            'camada1'               =>  $config['camada1'],
            'camada2'               =>  $config['camada2'],
            'pagina'                =>  $config['page'],
            'acao'                  =>  $config['acao'],
            "dados"                 =>  $dados,
            "cargos"			    =>	$this->cargosData['cargos'],
            "titulo"			    =>	$config['acao'] === 'create' ? 'Criar Cargo' : 'Alterar Cargo',
            'user'				    =>	session('nome'),
        ]);
    }

     /* =====================================================
     * VALIDAÇÕES AUXILIARES
     * ===================================================== */

    private function validarSessao(): void{
        if (!checklogged()) {
            redirect()->route('home')->with('error', 'Sua sessão expirou')->send();
            exit;
        }
    }

    private function validarView(string $camada1, string $camada2, string $page): void{
        if (!is_file(APPPATH . "Views/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw new PageNotFoundException("Página não encontrada: {$page}");
        }
    }

    

    public function registrar(){
        try {
            $form   = new CargoFormService($this->request);
            $dados  = $form->handle();

            $id = (new CargoService())->salvar(
                $dados['cargo']                
            );
            return redirect()->route('Cargos')
                ->with('mensagemSuccess', 'Registro salvo com sucesso');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);
        }
    }


    public function deletar(){
        try {
            $id = (int) $this->request->getPost('chavePrimaria');

            if ($id <= 0) {
                throw new \InvalidArgumentException('Cargo inválido');
            }

            (new CargoService())->deletar($id);

            return redirect()
                ->route('Cargos')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {

            return redirect()
                ->route('Cargos')
                ->with('mensagemError', $e->getMessage());
        }
    }


    




}