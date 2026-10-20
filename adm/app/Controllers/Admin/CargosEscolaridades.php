<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CargosModel;
use App\Models\EscolaridadesModel;
use App\Models\CargosEscolaridadesModel;
use App\Services\LogsService;
use App\Services\Cargos\CargosEscolaridadesService;


class CargosEscolaridades extends BaseController{

    protected $escolaridadesData;
    protected $cargosModel;
    protected $escolaridadesModel;
    protected $cargosEscolaridadesModel;

    public function __construct(){

        $this->cargosModel              = new \App\Models\CargosModel();
        $this->escolaridadesModel       = new \App\Models\EscolaridadesModel();
        $this->cargosEscolaridadesModel = new \App\Models\CargosEscolaridadesModel();

        $this->cargosData = [
           'cargos' => $this->cargosModel->orderBy('ds_nome_cargo', 'asc')->findAll(),
           'escolaridades' => $this->escolaridadesModel->orderBy('ds_nome_escolaridade', 'asc')->findAll(),
        ];
      
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
        
        // Form sempre inicia em branco para nova associação
        $action = 'create';
        $cargoEscolaridade = null;

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
            'page'                 => 'formCargosEscolaridade',
            
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
     * Retorna os dados de uma associação específica para preenchimento do formulário (AJAX).
     */
    public function buscarAssociacao($idAssociacao){
        $this->validarSessao();

        $associacao = $this->cargosEscolaridadesModel->find($idAssociacao);

        if (!$associacao) {
            return $this->response->setStatusCode(404)->setJSON(['erro' => 'Associação não encontrada']);
        }

        return $this->response->setJSON($associacao);
    }

    /**
     * Registra a associação de uma escolaridade ao cargo (create ou update).
     */
    public function registrarAssociacaoCargoEscolaridade(){
        try {
            $acao = $this->request->getPost('acao') ?? 'create';
            $data = [
                'pk_id_cargos_escolaridade' => $this->request->getPost('pk_id_cargos_escolaridade'),
                'fk_id_cargo'               => $this->request->getPost('fk_id_cargo'),
                'fk_id_escolaridade'         => $this->request->getPost('fk_id_escolaridade'),
                'ds_pontuacao_minima'      => $this->request->getPost('ds_pontuacao_minima'),
                'ds_pontuacao_maxima'      => $this->request->getPost('ds_pontuacao_maxima'),
                'ds_tipo_campo'             => $this->request->getPost('ds_tipo_campo'),
            ];

            if (empty($data['fk_id_cargo'])) {
                throw new \Exception('O cargo deve ser informado.');
            }
            if (empty($data['fk_id_escolaridade'])) {
                throw new \Exception('A escolaridade deve ser selecionada.');
            }
            if (empty($data['ds_pontuacao_minima'])) {
                throw new \Exception('A pontuação mínima deve ser informada.');
            }
            if (empty($data['ds_pontuacao_maxima'])) {
                throw new \Exception('A pontuação máxima deve ser informada.');
            }
            if (empty($data['ds_tipo_campo'])) {
                throw new \Exception('O tipo de campo deve ser informado.');
            }
            if ($data['ds_pontuacao_maxima'] < $data['ds_pontuacao_minima']) {
                throw new \Exception('A pontuação máxima deve ser maior ou igual à mínima.');
            }

            service('cargosEscolaridadesService')->salvar($data, $acao);

            return redirect()
                ->route('CargosEscolaridades.formularioCargosEscolaridade', [$data['fk_id_cargo']])
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

            service('cargosEscolaridadesService')->deletar($id);

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