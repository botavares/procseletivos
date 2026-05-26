<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\SetoresModel;
use App\Models\SecretariasModel;
use App\Models\ManifestacoesModel;
use App\Models\DadosContratoModel;
class Setores extends BaseController{

    protected $setoresData;
    

    public function __construct(){
        $modelSetores = new SetoresModel();
        $modelSecretarias = new SecretariasModel();
        
        $this->setoresData = [
           'setores'   => $modelSetores->setoresSecretarias(),
           'secretarias' => $modelSecretarias->orderBy('ds_nome_secretaria', 'asc')->findAll(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Setores') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
       
        $titulosTabela = array(
            "Nome do setor","Secretaria","Telefone"
        );
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Gerenciar Setores",
            "setores"			=>	$this->setoresData['setores'],
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formSetores'){
        
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
            "setores"	    =>	$this->setoresData['setores'],
            'secretarias'   =>	$this->setoresData['secretarias'],
            'titulo'        =>  'Cadastro de setores',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formSetores'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelSetores = new SetoresModel();
        
        $dados = $modelSetores->setorSecretaria($id);
        
      
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'dados'     =>  $dados,
            'setores'	=>	$this->setoresData['setores'],
            'secretarias'   =>	$this->setoresData['secretarias'],
            'titulo'    =>  'Cadastro de setores',
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\SetoresModel();
            $acao = $this->request->getPost('action');

            $arrayRegistro = array(
                'ds_nome_setor' => $this->request->getPost('ds_nome_setor'),
                'ds_status' => $this->request->getPost('ds_status'),
                'ds_telefone' => $this->request->getPost('ds_telefone'),
                'fk_id_secretaria' => $this->request->getPost('pk_id_secretaria'),
            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                    return redirect()->route('Setores')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                    foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Setores.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
            }
            if($acao ==="update"){
                $idSetor = $this->request->getPost('pk_id_setor');
                if($model->update($idSetor,$arrayRegistro)){
                    $setorAssunto = $this->request->getPost('ds_assuntoSetores');
                    return redirect()->route('Setores')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Setores')->with('error',$model->errors());
                };
            }
        }
    }
    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe manifestos com esse setor
        $dadosContrato = new DadosContratoModel();
        $constratos = $dadosContrato->where('fk_id_setor', $id)->first();
        if($constratos){
            return redirect()->route('Setores')->with('mensagemError','Setor possui contratos vinculados, impossível excluir');
        }
        
        $modelSetores = new SetoresModel();
        if($modelSetores->delete($id)){
            return redirect()->route('Setores')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Setores')->with('mensagemError','Erro ao excluir registro');
        }


    }




}