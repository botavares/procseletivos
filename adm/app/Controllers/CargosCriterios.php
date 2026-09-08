<?php
namespace App\Controllers;
use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CargosModel;
use App\Models\CriteriosAdicionaisModel;
use App\Models\CargosCriteriosAdicionaisModel;
use App\Services\LogsService;
use App\Services\Cargos\CargosCriteriosFormService;
use App\Services\Cargos\CargosCriteriosService;


class CargosCriterios extends BaseController{

    protected $CriteriosData;
    

    public function __construct(){
        $modelCargos = new CargosModel();
        $modelCriterios = new CriteriosAdicionaisModel();

        $this->cargosData = [
           'cargos' => $modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll(),
           'Criterios' => $modelCriterios->orderBy('ds_nome_criterio', 'asc')->findAll(),
        ];
      
    }

    public function formularioCargosCriterio($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formCargosCriterios'){
        $this->validarSessao();

        $cargo = (new CargosModel())->find($id);
        
        if (!$cargo) {
            return redirect()
                ->route('Cargos')
                ->with('mensagemError', 'Cargo não encontrado');
        }

        $Criterios = (new CriteriosAdicionaisModel())->listarCriteriosOrdenados();
        
        // Form sempre inicia em branco para nova associação
        $action = 'create';
        $cargoCriterio = null;

        // Busca TODAS as Criterios já associadas a esse cargo
        $CriteriosDoCargo = (new CargosCriteriosAdicionaisModel())->listarCriteriosDoCargo($id);

        return $this->renderFormulario([
                                        'id'                    => $id,
                                        'acao'                  => $action,
                                        'camada1'               => 'pages',
                                        'camada2'               => 'cadastros',
                                        'page'                  => 'formCargosCriterios',
                                        'titulo'                => 'Associar Critérios Adicionais ao Cargo: ' . esc($cargo->ds_nome_cargo),
                                        'cargo'                 => $cargo,
                                        'Criterios'             => $Criterios,
                                        'cargoCriterio'         => $cargoCriterio,
                                        'CriteriosDoCargo'      => $CriteriosDoCargo,
                                        'user'                  => session('nome'),
                                    ]);
    }

    private function renderFormulario(array $config){

        // criação de um config defaut
        $config = array_merge([
            'id'                   => null,
            
            'camada1'              => 'pages',
            'camada2'              => 'cadastros',
            'page'                 => 'formCargosCriterios',
            
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

        // Repassa quaisquer dados extras (ex: cargo, Criterios) para a view
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

        $associacao = (new CargosCriteriosAdicionaisModel())->find($idAssociacao);

        if (!$associacao) {
            return $this->response->setStatusCode(404)->setJSON(['erro' => 'Associação não encontrada']);
        }

        return $this->response->setJSON($associacao);
    }

    /**
     * Registra a associação de um criterio ao cargo (create ou update).
     */
    public function registrarAssociacaoCargoCriterio(){
        try {
            $form   = new CargosCriteriosFormService($this->request);
            $dados  = $form->handle();

            $dto = $dados['cargosCriterios'];

            $service = new CargosCriteriosService();
            $service->salvar($dto);

            return redirect()
                ->route('CargosCriterios.formularioCargosCriterio', [$dto->fk_id_cargo])
                ->with('mensagemSuccess', 'Criterio associado ao cargo com sucesso');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);
        }
    }

    /**
     * Remove a associação de uma criterio ao cargo.
     */
    public function deletarAssociacaoCargoCriterio(){
        try {
            $id = (int) $this->request->getPost('pk_id_cargo_criterio');

            if ($id <= 0) {
                throw new \InvalidArgumentException('Associação inválida');
            }

            (new CargosCriteriosService())->deletar($id);

            return redirect()
                ->back()
                ->with('mensagemSuccess', 'Criterio retirado do cargo com sucesso');

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