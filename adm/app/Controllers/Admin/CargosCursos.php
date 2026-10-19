<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CargosModel;
use App\Models\CursosModel;
use App\Models\CargosCursosModel;
use App\Services\LogsService;
use App\Services\Cargos\CargosCursosFormService;
use App\Services\Cargos\CargosCursosService;


class CargosCursos extends BaseController{

    protected $cargosData;
    protected $cargosModel;
    protected $cursosModel;
    protected $cargosCursosModel;

    public function __construct(){
        parent::__construct();
        
        $this->cargosModel      = new \App\Models\CargosModel();
        $this->cursosModel       = new \App\Models\CursosModel();
        $this->cargosCursosModel = new \App\Models\CargosCursosModel();
        
        $this->cargosData = [
           'cargos' => $this->cargosModel->orderBy('ds_nome_cargo', 'asc')->findAll(),
        ];
      
    }

    public function formularioCargosCurso($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formCargosCursos'){
        $this->validarSessao();

        $cargo = $this->cargosModel->find($id);
        
        if (!$cargo) {
            return redirect()
                ->route('Cargos')
                ->with('mensagemError', 'Cargo não encontrado');
        }

        $cursos = $this->cursosModel->listarCursosOrdenados();
        
        // Form sempre inicia em branco para nova associação
        $action = 'create';
        $cargoCurso = null;

        // Busca TODAS os cursos já associados a esse cargo
        $cursosDoCargo = $this->cargosCursosModel->listarCursosDoCargo($id);

        return $this->renderFormulario([
                                        'id'                    => $id,
                                        'acao'                  => $action,
                                        'camada1'               => 'pages',
                                        'camada2'               => 'cadastros',
                                        'page'                  => 'formCargosCursos',
                                        'titulo'                => 'Associar Curso ao Cargo: ' . esc($cargo->ds_nome_cargo),
                                        'cargo'                 => $cargo,
                                        'cursos'                => $cursos,
                                        'cargoCurso'            => $cargoCurso,
                                        'cursosDoCargo'         => $cursosDoCargo,
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
            "titulo"			    =>	$config['titulo'],
            'user'				    =>	session('nome'),
        ];

        // Repassa quaisquer dados extras (ex: cargo, cursos) para a view
        $padroes = ['id','acao','camada1','camada2','page','titulo'];
        foreach ($config as $chave => $valor) {
            if (!in_array($chave, $padroes, true)) {
                $viewData[$chave] = $valor;
            }
        }

        return view('layoutDash', $viewData);
    }

    /**
     * Retorna os dados de uma associação específica para preenchimento do formulário (AJAX).
     */
    public function buscarAssociacao($idAssociacao){
        $this->validarSessao();

        $associacao = $this->cargosCursosModel->find($idAssociacao);

        if (!$associacao) {
            return $this->response->setStatusCode(404)->setJSON(['erro' => 'Associação não encontrada']);
        }

        return $this->response->setJSON($associacao);
    }

    /**
     * Registra a associação de um curso ao cargo (create ou update).
     */
    public function registrarAssociacaoCargoCursos(){
        try {
            $form   = new CargosCursosFormService($this->request);
            $dados  = $form->handle();

            $dto = $dados['cargosCursos'];

            service('cargosCursosService')->salvar($dto);

            return redirect()
                ->route('CargosCursos.formularioCargosCurso', [$dto->fk_id_cargo])
                ->with('mensagemSuccess', 'Curso associado ao cargo com sucesso');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);
        }
    }

    /**
     * Remove a associação de uma experiência ao cargo.
     */
    public function deletarAssociacaoCargoCursos(){
        try {
            $id = (int) $this->request->getPost('pk_id_cargo_aperfeicoamento');

            if ($id <= 0) {
                throw new \InvalidArgumentException('Associação inválida');
            }

            service('cargosCursosService')->deletar($id);

            return redirect()
                ->back()
                ->with('mensagemSuccess', 'Curso retirado do cargo com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('mensagemError', $e->getMessage());
        }
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

}