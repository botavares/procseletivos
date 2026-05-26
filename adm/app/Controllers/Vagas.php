<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\SetoresModel;
use App\Models\SecretariasModel;
use App\Models\VagasModel;
use App\Models\CursosModel;
class Vagas extends BaseController{

    protected $setoresData;
    

    public function __construct(){
        $modelSetores = new SetoresModel();
        $modelSecretarias = new SecretariasModel();
        $modelVagas = new VagasModel();
        $modelCursos = new CursosModel();
        
        $this->vagasData = [
           'setores'   => $modelSetores->setoresSecretarias(),
           'secretarias' => $modelSecretarias->orderBy('ds_nome_secretaria', 'asc')->findAll(),
           'vagas'      => $modelVagas->vagas(),
           'cursos'     => $modelCursos->orderBy('ds_nome_curso', 'asc')->findAll(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Vagas') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
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
        
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
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
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelVagas = new VagasModel();
        
        $dados = $modelVagas->vagasSetorCurso($id);
        
      
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
            //Instanciar Model
            $model = new \App\Models\VagasModel();
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
                $duplicidade = $model
                            ->where('fk_id_setor', $this->request->getPost('pk_id_setor'))
                            ->where('fk_id_curso', $this->request->getPost('pk_id_curso'))
                            ->first();
                
                if(empty($duplicidade)){
                    if($model->insert($arrayRegistro)){
                        return redirect()->route('Vagas')->with('mensagemSuccess','Registro gravado com sucesso');
                    }else{

                        foreach ($model->errors() as $campo => $erro) {
                            $this->session->setFlashData($campo, $erro);
                        }
                        return redirect()->route('Vagas.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                    }
                }else{
                    return redirect()->route('Vagas.formularioCadastro')->with('mensagemError','Vagas para esse setor e curso já cadastradas');
                }
            }
            if($acao ==="update"){
                $id = $this->request->getPost('pk_id_vaga');
                if($model->update($id,$arrayRegistro)){
                    return redirect()->route('Vagas')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Vagas')->with('error',$model->errors());
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

    $modelVagas = new VagasModel();
    $setores = $modelVagas->setoresComVagas($idCurso);

    return $this->response->setJSON([
        'setores' => $setores,
        csrf_token() => csrf_hash() // novo token
    ]);
}



    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe manifestos com esse setor
        
        $model = new VagasModel();
        if($model->delete($id)){
            return redirect()->route('Vagas')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Vagas')->with('mensagemError','Erro ao excluir registro');
        }


    }




}