<?php

namespace App\Controllers\Admin;

use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\Admin\CargosModel;
use App\Services\Admin\LogsService;
use App\Services\Admin\Cargos\CargoService;
use App\Services\Admin\Cargos\CargoGridService;



class Cargos extends BaseController{

    protected $cargosData;
    protected $cargosModel;
    protected $escolaridadesModel;
    protected $cargosEscolaridadesModel;
    protected $cargoGridService;

    public function __construct(){
        
        $this->cargosModel             = new \App\Models\Admin\CargosModel();
        $this->escolaridadesModel        = new \App\Models\Admin\EscolaridadesModel();
        $this->cargosEscolaridadesModel = new \App\Models\Admin\CargosEscolaridadesModel();
        $this->cargoGridService         = service('cargoGridService');
        
        $this->cargosData = [
           'cargos' => $this->cargosModel->orderBy('ds_nome_cargo', 'asc')->findAll(),
        ];
      
    }

    /*============================================================
     *LISTAGENS (PREENCHER O GRID INICIAL DAS MINHAS TELAS)
     =============================================================*/
    public function index($camada1 = '',$camada2 = 'pages', $page = 'Cargos') {
        return $this->listarParaGrid($camada1, $camada2, $page);
    }
    
    private function listarParaGrid(string $camada1, string $camada2, string $page){
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $grid = $this->cargoGridService->cargos();

        if (!is_array($grid) || !isset($grid['data'], $grid['columns'])) {
            throw new \RuntimeException('Grid retornado em formato inválido');
        }

        return view('Admin/layoutDash', [
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

    

    public function formularioCargosEscolaridade($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formCargosEscolaridade'){
        $this->validarSessao();

        $cargo = $this->cargosModel->find($id);
        
        if (!$cargo) {
            return redirect()
                ->route('Cargos')
                ->with('mensagemError', 'Cargo não encontrado');
        }

        $escolaridades = $this->escolaridadesModel->listarEscolaridadesOrdenadas();
        
        // Busca os dados da associação cargo + escolaridade (se existir)
        $cargoEscolaridade = $this->cargosEscolaridadesModel
                                ->where('fk_id_cargo', $id)
                                ->first();
        if($cargoEscolaridade){
            $action = 'update';
        }else{
            $action = 'create';
        }

        // Busca TODAS as escolaridades já associadas a esse cargo
        $escolaridadesDoCargo = $this->cargosEscolaridadesModel->listarEscolaridadesDoCargo($id);

        return $this->renderFormulario([
                                        'id'                    => $id,
                                        'acao'                  => $action,
                                        'camada1'               => 'pages',
                                        'camada2'               => 'cadastros',
                                        'page'                  => 'formCargosEscolaridade',
                                        'titulo'                => 'Associar Escolaridade ao Cargo: ' . esc($cargo->ds_nome_cargo),
                                        'cargo'                 => $cargo,
                                        'escolaridades'         => $escolaridades,
                                        'cargoEscolaridade'     => $cargoEscolaridade,
                                        'escolaridadesDoCargo'  => $escolaridadesDoCargo,
                                        'user'                  => session('nome'),
                                    ]);
    }


    private function renderFormulario(array $config){

        // criação de um config defaut
        $config = array_merge([
            'id'                   => null,
            'camada1'              => 'pages',
            'camada2'              => 'cadastros',
            'page'                 => 'formCargos',
            
        ],$config);

        $this->validarView($config['camada1'],$config['camada2'],$config['page']);
        $this->validarSessao();

        $dados = null;

        if ($config['acao'] === 'update' && $config['id']) {
            $dados = $this->cargosModel->find($config['id']);
        }

        // Dados padrão da view
        $viewData = [
            'camada1'               =>  $config['camada1'],
            'camada2'               =>  $config['camada2'],
            'pagina'                =>  $config['page'],
            'acao'                  =>  $config['acao'],
            "dados"                 =>  $dados,
            
            'user'				    =>	session('nome'),
        ];

        // Repassa quaisquer dados extras (ex: cargo, experiencias) para a view
        $padroes = ['id','acao','camada1','camada2','page','titulo'];
        foreach ($config as $chave => $valor) {
            if (!in_array($chave, $padroes, true)) {
                $viewData[$chave] = $valor;
            }
        }

        return view('Admin/layoutDash', $viewData);
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
        if (!is_file(APPPATH . "Views/Admin/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw new PageNotFoundException("Página não encontrada: {$page}");
        }
    }

    

    public function registrar(){
        try {
            log_message('debug', '[Cargos::registrar] Iniciando salvamento');
            
            $fkIdSecretaria = session('fk_id_secretaria');
            log_message('debug', '[Cargos::registrar] fk_id_secretaria=' . var_export($fkIdSecretaria, true));
            
            if (empty($fkIdSecretaria)) {
                throw new \Exception('Sua sessão não contém a secretaria vinculada. Faça logout e login novamente.');
            }

            $cargoData = [
                'pk_id_cargo'      => $this->request->getPost('pk_id_cargo'),
                'fk_id_secretaria' => $fkIdSecretaria,
                'ds_nome_cargo'    => $this->request->getPost('ds_nome_cargo'),
                'ds_carga_horaria' => $this->request->getPost('ds_carga_horaria'),
                'ds_nivel'         => $this->request->getPost('ds_nivel'),
            ];
            $acao = $this->request->getPost('action') ?? 'create';

            if (empty($cargoData['ds_nome_cargo'])) {
                throw new \Exception('O nome do cargo deve ser informado.');
            }
            if (empty($cargoData['ds_carga_horaria'])) {
                throw new \Exception('A carga horária deve ser informada.');
            }
            if (empty($cargoData['ds_nivel'])) {
                throw new \Exception('O nível de escolaridade deve ser informado.');
            }

            $service = service('cargoService');

            if ($acao === 'update') {
                $service->atualizar((int)$cargoData['pk_id_cargo'], $cargoData);
            } else {
                $id = $service->salvar($cargoData);
            }
            
            log_message('debug', '[Cargos::registrar] Salvamento OK, id=' . ($id ?? 'update'));

            return redirect()->route('Cargos')
                ->with('mensagemSuccess', 'Registro salvo com sucesso');

        } catch (\Exception $e) {
            log_message('error', '[Cargos::registrar] ERRO: ' . $e->getMessage());
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

            service('cargoService')->deletar($id);

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
