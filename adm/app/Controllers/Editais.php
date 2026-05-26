<?php

namespace App\Controllers;

use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CargosModel;
use App\Models\EditaisCargosModel;
use App\Models\EditaisAbrangenciasModel;
use App\Models\EditaisModel;
use App\Services\LogsService;
use App\Services\Editais\EditalFormService;
use App\Services\Editais\EditalUploadService;
use App\Services\Editais\EditalService;
use App\Services\Editais\EditalGridService;




class Editais extends BaseController{

    protected $editaisData;
    

    public function __construct(){
        $modelCargos = new CargosModel();
        
        $modelEditais = new EditaisModel();
        
        $this->editaisData = [
           'cargos'         => $modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll(),
           
          
        ];
      
    }

    /*============================================================
    *LISTAGENS (PREENCHER O GRID INICIAL DAS MINHAS TELAS)
    =============================================================*/
    public function index($camada1 = '',$camada2 = 'pages', $page = 'Editais') {
        return $this->listarParaGrid('ativos', $camada1, $camada2, $page);
    }
    public function encerrados($camada1 = '',$camada2 = 'pages', $page = 'Editais') {
        return $this->listarParaGrid('encerrados', $camada1, $camada2, $page);
    }

    private function listarParaGrid(string $tipo,string $camada1,string $camada2,string $page) {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $gridService = new EditalGridService();

        $grid = match ($tipo) {
            'ativos'     => $gridService->ativos(),
            'encerrados' => $gridService->encerrados(),
            default      => throw new \InvalidArgumentException('Tipo de grid inválido'),
        };

        return view('layoutDash', [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'tipo'          => $tipo,
            'titulo'        => 'Gerenciar Editais',
            'editais'       => $grid['data'],
            'titulosTabela' => $grid['columns'],
            'user'          => session('nome'),
        ]);
}

    /*============================================================
    * FORMULARIOS
    =============================================================*/
    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formEditais') {
        return $this->renderFormulario([
                                        'acao'     => 'create',
                                        'camada1'  => $camada1,
                                        'camada2'  => $camada2,
                                        'page'     => $page,
                    ]);
    }
    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formEditais'){
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
            'page'                 => 'formEditais',
            'titulo'               => '',
        ],$config);
        
        $this->validarView($config['camada1'],$config['camada2'],$config['page']);
        $this->validarSessao();

        $dados = null;
        $cargosEditais = [];
        

        if($config['acao'] === 'update' && $config['id']){ // Aqui eu consigo pegar os dados do edital se for atualização
            $dados = (new EditaisModel())->getEdital($config['id']);
            $cargosEditais = (new EditaisCargosModel())
                            ->where('fk_id_edital', $config['id'])
                            ->findColumn('fk_id_cargo');
           
        }

        return view('layoutDash', [
            'camada1'               =>  $config['camada1'],
            'camada2'               =>  $config['camada2'],
            'pagina'                =>  $config['page'],
            'acao'                  =>  $config['acao'],
            "dados"                 =>  $dados,
            "cargos"			    =>	$this->editaisData['cargos'],
            "cargosEditais"		    =>	$cargosEditais,
            
            "titulo"			    =>	$config['acao'] === 'create' ? 'Criar Edital' : 'Alterar Edital',
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
            $form   = new EditalFormService($this->request);
            $dados  = $form->handle();
            
            $acao   = $dados['acao'];
            
           
            if($acao === 'update'){
                 
                (new EditalService())->atualizar(
                    $dados['edital'],
                    $dados['relacoes']
                );
            }else{
                $id = (new EditalService())->salvar(
                $dados['edital'],
                $dados['relacoes']
            );

            }

            
            (new EditalUploadService())->upload(
                $this->request->getFile('ds_arquivos'),
                $dados['edital']['ds_numero_edital']
            );
            
            return redirect()->route('Editais')
                ->with('mensagemSuccess', 'Registro salvo com sucesso');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);
        }
    }


 


    public function getCargosByEdital(){
        $idEdital = $this->request->getPost('idEdital');

        if ($idEdital) {
            $modelEditais = new EditaisModel();
            $editais = $modelEditais->getCargosByEdital($idEdital);

            return $this->response->setJSON([
                'cargos' => $editais,
                csrf_token() => csrf_hash() // novo token
            ]);
        }

        return $this->response
            ->setStatusCode(400)
            ->setJSON(['erro' => 'Edital não informado']);
    }


    public function deletar(){
        try {
            $id = (int) $this->request->getPost('chavePrimaria');

            if ($id <= 0) {
                throw new \InvalidArgumentException('Edital inválido');
            }

            (new EditalService())->deletar($id);

            return redirect()
                ->route('Editais')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {

            return redirect()
                ->route('Editais')
                ->with('mensagemError', $e->getMessage());
        }
    }



    public function obterEdital(string $arquivo)
{
    $arquivo = basename($arquivo); // evita ../

    $path = dirname(ROOTPATH)
        . DIRECTORY_SEPARATOR . 'writable'
        . DIRECTORY_SEPARATOR . 'uploads'
        . DIRECTORY_SEPARATOR . 'Editais'
        . DIRECTORY_SEPARATOR . $arquivo;

    if (! is_file($path)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Arquivo não encontrado.');
    }
    return $this->response
    ->setHeader('Content-Type', 'application/pdf')
    ->setHeader('Content-Disposition', 'inline; filename="' . $arquivo . '"')
    ->setBody(file_get_contents($path));
    //return $this->response->download($path, null);
}

    




}