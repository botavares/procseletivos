<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\SetoresModel;
use App\Models\SecretariasModel;
use App\Models\VagasModel;
use App\Models\CursosModel;
class Vagas extends BaseController{

    protected $setoresData;
    protected $setoresModel;
    protected $secretariasModel;
    protected $vagasModel;
    protected $cursosModel;

    public function __construct(){
        parent::__construct();
        
        $this->setoresModel     = new \App\Models\SetoresModel();
        $this->secretariasModel = new \App\Models\SecretariasModel();
        $this->vagasModel       = new \App\Models\VagasModel();
        $this->cursosModel      = new \App\Models\CursosModel();
        
        $this->vagasData = [
           'setores'     => $this->setoresModel->setoresSecretarias(),
           'secretarias' => $this->secretariasModel->orderBy('ds_nome_secretaria', 'asc')->findAll(),
           'vagas'       => $this->vagasModel->vagas(),
           'cursos'      => $this->cursosModel->orderBy('ds_nome_curso', 'asc')->findAll(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Vagas') {
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
       
        $titulosTabela = array(
            "Nome do setor","Secretaria","Curso","Vagas",
        );
        
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Planilha de vagas",
            "vagas"			    =>	$this->vagasData['vagas'],
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formVagas'){
        
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }


        //verificar se o usuario esta logado
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'acao'          =>  'create',
            "setores"	    =>	$this->vagasData['setores'],
            'secretarias'   =>	$this->vagasData['secretarias'],
            'cursos'        =>	$this->vagasData['cursos'],
            'titulo'        =>  'Planilha de Vagas',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formVagas'){
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $dados = $this->vagasModel->vagasSetorCurso($id);
        
      
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'dados'     =>  $dados,
            "setores"	    =>	$this->vagasData['setores'],
            'secretarias'   =>	$this->vagasData['secretarias'],
            'cursos'        =>	$this->vagasData['cursos'],
            'titulo'    =>  'Cadastro de setores',
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            $acao = $this->request->getPost('action');

            $arrayRegistro = array(
                'fk_id_setor' => $this->request->getPost('pk_id_setor'),
                'fk_id_curso' => $this->request->getPost('pk_id_curso'),
                'ds_vagas' => $this->request->getPost('ds_vagas'),
                'ds_responsavel' => $this->request->getPost('ds_responsavel'),
                'ds_telefone' => $this->request->getPost('ds_telefone'),
                'ds_observacao' => $this->request->getPost('ds_observacao'),
            );

            if($acao ==="create"){
                $duplicidade = $this->vagasModel
                            ->where('fk_id_setor', $this->request->getPost('pk_id_setor'))
                            ->where('fk_id_curso', $this->request->getPost('pk_id_curso'))
                            ->first();
                
                if(empty($duplicidade)){
                    if($this->vagasModel->insert($arrayRegistro)){
                        return redirect()->route('Vagas')->with('mensagemSuccess','Registro gravado com sucesso');
                    }else{

                        foreach ($this->vagasModel->errors() as $campo => $erro) {
                            $this->session->setFlashData($campo, $erro);
                        }
                        return redirect()->route('Vagas.formularioCadastro')->back()->withInput()->with('error',$this->vagasModel->errors());
                    }
                }else{
                    return redirect()->route('Vagas.formularioCadastro')->with('mensagemError','Vagas para esse setor e curso já cadastradas');
                }
            }
            if($acao ==="update"){
                $id = $this->request->getPost('pk_id_vaga');
                if($this->vagasModel->update($id,$arrayRegistro)){
                    return redirect()->route('Vagas')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Vagas')->with('error',$this->vagasModel->errors());
                };
            }
        }
    }

    public function getSetoresByCurso(){
    $idCurso = $this->request->getPost('idCurso');

    if (!$idCurso) {
        return $this->response
            ->setStatusCode(400)
            ->setJSON(['erro' => 'curso não informado']);
    }

    $setores = $this->vagasModel->setoresComVagas($idCurso);

    return $this->response->setJSON([
        'setores' => $setores,
        csrf_token() => csrf_hash() // novo token
    ]);
}



    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe manifestos com esse setor
        
        if($this->vagasModel->delete($id)){
            return redirect()->route('Vagas')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Vagas')->with('mensagemError','Erro ao excluir registro');
        }


    }




}