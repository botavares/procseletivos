<?php
namespace App\Controllers;
use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CargosModel;
use App\Models\EscolaridadesModel;
use App\Models\CargosEscolaridadesModel;
use App\Services\LogsService;
use App\Services\Cargos\CargosEscolaridadesFormService;
use App\Services\Cargos\CargosEscolaridadesService;


class CargosEscolaridades extends BaseController{

    protected $escolaridadesData;
    

    public function __construct(){
        $modelCargos = new CargosModel();
        $modelEscolaridades = new EscolaridadesModel();

        $this->cargosData = [
           'cargos' => $modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll(),
           'escolaridades' => $modelEscolaridades->orderBy('ds_nome_escolaridade', 'asc')->findAll(),
        ];
      
    }

    public function formularioCargosEscolaridade($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formCargosEscolaridade'){
        $this->validarSessao();

        $cargo = (new CargosModel())->find($id);
        
        if (!$cargo) {
            return redirect()
                ->route('Cargos')
                ->with('mensagemError', 'Cargo não encontrado');
        }

        $escolaridades = (new EscolaridadesModel())->listarEscolaridadesOrdenadas();
        
        // Busca os dados da associação cargo + escolaridade (se existir)
        $cargoEscolaridade = (new CargosEscolaridadesModel())
                                ->where('fk_id_cargo', $id)
                                ->first();
        if($cargoEscolaridade){
            $action = 'update';
        }else{
            $action = 'create';
        }

        // Busca TODAS as escolaridades já associadas a esse cargo
        $escolaridadesDoCargo = (new CargosEscolaridadesModel())->listarEscolaridadesDoCargo($id);

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
            'page'                 => 'formCargosEscolaridade',
            
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

        // Repassa quaisquer dados extras (ex: cargo, escolaridades) para a view
        $padroes = ['id','acao','camada1','camada2','page','titulo'];
        foreach ($config as $chave => $valor) {
            if (!in_array($chave, $padroes, true)) {
                $viewData[$chave] = $valor;
            }
        }

        return view('layoutDash', $viewData);
    }

    /**
     * Registra a associação de uma escolaridade ao cargo (create ou update).
     */
    public function registrarAssociacaoCargoEscolaridade(){
        try {
            $form   = new CargosEscolaridadesFormService($this->request);
            $dados  = $form->handle();

            $dto = $dados['cargosEscolaridades'];

            $service = new CargosEscolaridadesService();
            $service->salvar($dto);

            return redirect()
                ->route('Cargos')
                ->with('mensagemSuccess', 'Escolaridade associada ao cargo com sucesso');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);
        }
    }

    /**
     * Remove a associação de uma escolaridade ao cargo.
     */
    public function deletarAssociacaoCargoEscolaridade(){
        try {
            $id = (int) $this->request->getPost('pk_id_cargos_escolaridade');

            if ($id <= 0) {
                throw new \InvalidArgumentException('Associação inválida');
            }

            (new CargosEscolaridadesService())->deletar($id);

            return redirect()
                ->back()
                ->with('mensagemSuccess', 'Escolaridade retirada do cargo com sucesso');

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