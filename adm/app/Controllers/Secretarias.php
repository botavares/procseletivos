<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\SecretariasModel;
use App\Models\SetoresModel;

class Secretarias extends BaseController{

    protected $secretariaDatas;
    

    public function __construct(){
        $model = new SecretariasModel();

        
        $this->secretariasData = [
           'secretarias'=> $model->select()->orderBy('ds_nome_secretaria', 'asc')->findAll(),
           
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Secretarias') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
       
        $titulosTabela = array(
            "Nome da Secretaria","Sigla","Email","Telefone"
        );
  


        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Gerenciar Secretarias",
			"secretarias"		=>	$this->secretariasData['secretarias'],
			"titulosTabela"		=>	$titulosTabela,
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formSecretarias'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'acao'          =>  'create',
            "secretarias"   =>	$this->secretariasData['secretarias'],
            'titulo'        =>  'Cadastro de Secretarias',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formSecretarias'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelSecretarias = new SecretariasModel();
        $dados = $modelSecretarias->where('pk_id_secretaria', $id)->first();
      
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'dados'         =>  $dados,
            'secretarias'   =>	$this->secretariasData['secretarias'],
            'titulo'        =>  'Alterar Secretarias'
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\SecretariasModel();
            $acao = $this->request->getPost('action');

            $telefone =  preg_replace('/\D/', '', $this->request->getPost('ds_telefone_secretaria'));

            $arrayRegistro = array(
                'ds_nome_secretaria' => $this->request->getPost('ds_nome_secretaria'),
                'ds_sigla_secretaria' => mb_strtoupper($this->request->getPost('ds_sigla_secretaria'),'UTF-8'),
                'ds_secretario_diretor' => $this->request->getPost('ds_secretario_diretor'),
                'ds_email_secretaria' => $this->request->getPost('ds_email_secretaria'),
                'ds_telefone_secretaria' => $telefone,

            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                    return redirect()->route('Secretarias')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                    foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Secretarias.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
            }
            if($acao ==="update"){
                
                $id = $this->request->getPost('pk_id_secretaria');
                if($model->update($id,$arrayRegistro)){
                    return redirect()->route('Secretarias')->with('mensagemSuccess','Registro alterado com sucesso.');
                }else{
                    return redirect()->route('Secretarias')->with('mensagemError','O registro não pode ser alterado.');
                };
              
               
            }

          
        }


    }
    
    public function deletar(){
        //Verificar se existe setores denpendentes dessa secretaria
        $modelSetores = new SetoresModel();
        $id = $this->request->getPost('chavePrimaria');
        $setores = $modelSetores->where('fk_id_secretaria', $this->request->getPost('chavePrimaria'))->first();
        
        if($setores){
            return redirect()->route('Secretarias')->with('mensagemError','Secretaria possui setores cadastrados, impossível excluir');
            
        }else{}
        $modelSecretarias = new SecretariasModel();
        if($modelSecretarias->delete($id)){
            return redirect()->route('Secretarias')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Secretarias')->with('mensagemError','Erro ao excluir registro');
        }
    }
}