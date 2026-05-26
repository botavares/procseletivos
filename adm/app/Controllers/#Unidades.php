<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\UnidadesModel;
use App\Models\TelefonesModel;

class Unidades extends BaseController{

    protected $unidadesData;
    

    public function __construct(){
        $modelUnidades = new UnidadesModel();
        $modelTelefones = new TelefonesModel();
        
        
        $this->unidadesData = [
           'unidades'    => $modelUnidades->orderBy('ds_nome_unidade', 'asc')->findAll(),
           'telefones'  => $modelTelefones->findAll(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Unidades') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
       
        $titulosTabela = array(
            "Unidade","Secretaria","Telefone(s)"
        );
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Gerenciar Unidades",
            "unidades"			=>	$this->unidadesData['unidades'],
            "telefones"			=>	$this->unidadesData['telefones'],
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formUnidades'){
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
            "unidades"	    =>	$this->unidadesData['unidades'],
            'classes'		=>	$this->unidadesData['telefones'],
            'titulo'        =>  'Cadastro de unidades',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formUnidades'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelUnidades = new UnidadesModel();
        
        $dados = $modelUnidades->where('pk_id_unidade', $id)->first();
        
       
      
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'dados'     =>  $dados,
            "uniddes"	=>	$this->unidadesData['unidades'],
            'telefones'	=>	$this->unidadesData['telefones'],
            'titulo'    =>  'Cadastro de unidades',
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\UnidadesModel();
            $acao = $this->request->getPost('action');

            $arrayRegistro = array(
                'ds_nome_unidade' => $this->request->getPost('ds_nome_unidade'),
                'ds_rua_unidade' => $this->request->getPost('ds_rua_unidade'),
                'ds_numero_unidade' => $this->request->getPost('ds_numero_unidade'),
                'ds_cep_unidade' => $this->request->getPost('ds_cep_unidade'),
                'ds_bairro_unidade' => $this->request->getPost('ds_bairro_unidade'),
                'ds_secretaria' => $this->request->getPost('ds_secretaria'),
                'ds_hora_inicio' => $this->request->getPost('ds_hora_inicio'),
                'ds_hora_encerra' => $this->request->getPost('ds_hora_encerra'),
                'fk_id_setor'   => $this->request->getPost('fk_id_setor'),
            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                    return redirect()->route('Unidades')->with('success','Registro gravado com sucesso');
                }else{
                    foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Unidades.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
            }
            if($acao ==="update"){
                
                $id = $this->request->getPost('pk_id_unidade');

                if($model->update($id,$arrayRegistro)){
                    return redirect()->route('Unidades')->with('success','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Unidades')->with('error',$model->errors());
                };
              
               
            }

          
        }


    }
    public function deletar(){
        
            $id = $this->request->getPost('chavePrimaria');
        
        $modelUnidades = new UnidadesModel();
        if($modelUnidades->delete($id)){
            return redirect()->route('Unidades')->with('success','Registro excluído com sucesso');
        }else{
            return redirect()->route('Unidades')->with('error','Erro ao excluir registro');
        }


    }
}