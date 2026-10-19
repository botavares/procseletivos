<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\SecretariasModel;
use App\Models\SetoresModel;

class Secretarias extends BaseController{

    protected $secretariasData;
    protected $secretariasModel;
    protected $setoresModel;

    public function __construct(){
        parent::__construct();
        
        $this->secretariasModel = new \App\Models\SecretariasModel();
        $this->setoresModel     = new \App\Models\SetoresModel();
        
        $this->secretariasData = [
           'secretarias'=> $this->secretariasModel->select()->orderBy('ds_nome_secretaria', 'asc')->findAll(),
           
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Secretarias') {
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
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
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
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
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $dados = $this->secretariasModel->where('pk_id_secretaria', $id)->first();
      
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
                if($this->secretariasModel->insert($arrayRegistro)){
                    return redirect()->route('Secretarias')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                    foreach ($this->secretariasModel->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Secretarias.formularioCadastro')->back()->withInput()->with('error',$this->secretariasModel->errors());
                }
            }
            if($acao ==="update"){
                
                $id = $this->request->getPost('pk_id_secretaria');
                if($this->secretariasModel->update($id,$arrayRegistro)){
                    return redirect()->route('Secretarias')->with('mensagemSuccess','Registro alterado com sucesso.');
                }else{
                    return redirect()->route('Secretarias')->with('mensagemError','O registro não pode ser alterado.');
                };
              
               
            }

          
        }


    }
    
    public function deletar(){
        //Verificar se existe setores denpendentes dessa secretaria
        $id = $this->request->getPost('chavePrimaria');
        $setores = $this->setoresModel->where('fk_id_secretaria', $this->request->getPost('chavePrimaria'))->first();
        
        if($setores){
            return redirect()->route('Secretarias')->with('mensagemError','Secretaria possui setores cadastrados, impossível excluir');
            
        }else{}
        if($this->secretariasModel->delete($id)){
            return redirect()->route('Secretarias')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Secretarias')->with('mensagemError','Erro ao excluir registro');
        }
    }
}