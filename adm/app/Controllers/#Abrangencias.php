<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\AbrangenciasModel;
use App\Models\CursosModel;

class Abrangencias extends BaseController{

    protected $abrangenciasData;
    

    public function __construct(){
        $modelAbrangencias = new AbrangenciasModel();
        
        $this->abrangenciasData = [
           'abrangencias'   => $modelAbrangencias->orderBy('ds_nome_abrangencia', 'asc')->findAll(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'abrangencias') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
       
        $titulosTabela = array(
            "Nome da abrangencia"
        );
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Gerenciar Abrangências",
            "abrangencias"		=>	$this->abrangenciasData['abrangencias'],
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formAbrangencias'){
        
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
            "abrangencias"  =>	$this->abrangenciasData['abrangencias'],
            'titulo'        =>  'Cadastro de Abrangências',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formAbrangencias'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelAbrangencias          = new AbrangenciasModel();
        $dados                      = $modelAbrangencias->getAbrangencia($id);

                                    
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'dados'         =>  $dados,
            'abrangencias'	=>	$this->abrangenciasData['abrangencias'],
            'titulo'    =>  'Cadastro de Abrangencias',
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\AbrangenciasModel();
            $acao = $this->request->getPost('action');

            $arrayRegistro = array(
                'ds_nome_abrangencia' => $this->request->getPost('ds_nome_abrangencia'),
                'ds_status' => $this->request->getPost('ds_status'),
            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                     return redirect()->route('Abrangencias')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                   foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Abrangencias.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
            }
            if($acao ==="update"){
                $idAbrangencia = $this->request->getPost('pk_id_abrangencia');
                if($model->update($idAbrangencia,$arrayRegistro)){
                    return redirect()->route('Abrangencias')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Abrangencias')->with('error',$model->errors());
                };
            }
            

          
        }
    }
    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe cursos com essa abrangencia
        $modelCursos = new CursosModel();
        $cursos = $modelCursos->where('fk_id_abrangencia', $id)->first();
        if($cursos){
            return redirect()->route('Abrangencias')->with('mensagemError','abrangência possui cursos vinculados, impossível excluir');
        }
        
        $modelAbrangencias = new AbrangenciasModel();
        if($modelAbrangencias->delete($id)){
            return redirect()->route('Abrangencias')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Abrangencias')->with('mensagemError','Erro ao excluir registro');
        }


    }




}