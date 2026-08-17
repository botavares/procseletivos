<?php

namespace App\Controllers;

use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CargosModel;
use App\Models\ExperienciasModel;
use App\Models\CargosExperienciasModel;
use App\Services\LogsService;
use App\Services\Cargos\CargoFormService;
use App\Services\Cargos\CargoService;
use App\Services\Cargos\CargoGridService;
use App\Services\Cargos\CargosExperienciasFormService;
use App\Services\Cargos\CargosExperienciasService;


class Cargos extends BaseController
{

    protected $cargosData;
    

    public function __construct(){
        $modelCargos = new CargosModel();
        
        $this->cargosData = [
           'cargos' => $modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll(),
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

        $gridService = new CargoGridService();
        $grid = $gridService->cargos();

        if (!is_array($grid) || !isset($grid['data'], $grid['columns'])) {
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

    public function formularioCargosExperiencia($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formCargosExperiencia'){
        $this->validarSessao();

        $cargo = (new CargosModel())->find($id);
        
        if (!$cargo) {
            return redirect()
                ->route('Cargos')
                ->with('mensagemError', 'Cargo não encontrado');
        }

        $experiencias = (new ExperienciasModel())->listarExperienciasOrdenadas();
        
        // Busca os dados da associação cargo + experiência (se existir)
        $cargoExperiencia = (new CargosExperienciasModel())
                                ->where('fk_id_cargo', $id)
                                ->first();
        if($cargoExperiencia){
            $action = 'update';
        }else{
            $action = 'create';
        }

        // Busca TODAS as experiências já associadas a esse cargo
        $experienciasDoCargo = (new CargosExperienciasModel())->listarExperienciasDoCargo($id);

        return $this->renderFormulario([
                                        'id'                    => $id,
                                        'acao'                  => $action,
                                        'camada1'               => 'pages',
                                        'camada2'               => 'cadastros',
                                        'page'                  => 'formCargosExperiencia',
                                        'titulo'                => 'Associar Experiência ao Cargo: ' . esc($cargo->ds_nome_cargo),
                                        'cargo'                 => $cargo,
                                        'experiencias'          => $experiencias,
                                        'cargoExperiencia'      => $cargoExperiencia,
                                        'experienciasDoCargo'   => $experienciasDoCargo,
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
            $dados = (new CargosModel())->find($config['id']);
        }

        // Dados padrão da view
        $viewData = [
            'camada1'               =>  $config['camada1'],
            'camada2'               =>  $config['camada2'],
            'pagina'                =>  $config['page'],
            'acao'                  =>  $config['acao'],
            "dados"                 =>  $dados,
            "titulo"			    =>	$config['titulo'],
            'user'				    =>	session('nome'),
        ];

        // Repassa quaisquer dados extras (ex: cargo, experiencias) para a view
        $padroes = ['id','acao','camada1','camada2','page','titulo'];
        foreach ($config as $chave => $valor) {
            if (!in_array($chave, $padroes, true)) {
                $viewData[$chave] = $valor;
            }
        }

        return view('layoutDash', $viewData);
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

            $acao = $dados['acao'];
            $cargoDto = $dados['cargo'];

            $service = new CargoService();

            if ($acao === 'update') {
                $service->atualizar($cargoDto);
            } else {
                $id = $service->salvar($cargoDto);
            }

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

    /**
     * Registra a associação de uma experiência ao cargo (create ou update).
     */
    public function registrarExperiencia(){
        try {
            $form   = new CargosExperienciasFormService($this->request);
            $dados  = $form->handle();

            $dto = $dados['cargosExperiencias'];

            $service = new CargosExperienciasService();
            $service->salvar($dto);

            return redirect()
                ->route('Cargos')
                ->with('mensagemSuccess', 'Experiência associada ao cargo com sucesso');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);
        }
    }

    /**
     * Remove a associação de uma experiência ao cargo.
     */
    public function deletarExperiencia(){
        try {
            $id = (int) $this->request->getPost('pk_id_cargos_experiencias');

            if ($id <= 0) {
                throw new \InvalidArgumentException('Associação inválida');
            }

            (new CargosExperienciasService())->deletar($id);

            return redirect()
                ->back()
                ->with('mensagemSuccess', 'Experiência retirada do cargo com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('mensagemError', $e->getMessage());
        }
    }
}
