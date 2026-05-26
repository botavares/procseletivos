<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\AuxiliosModel;
use App\Models\DadosContratosModel;


class Auxilios extends BaseController{

    protected $auxiliosData;
    

    public function __construct(){
        $modelAuxilio = new AuxiliosModel();
        $this->auxiliosData = [
           'auxilios'         => $modelAuxilio->orderBy('ds_horas_diarias', 'asc')->findAll(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Auxilios') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
       
        $titulosTabela = array(
            "horas diárias","valor do Auxílio Bolsa","valor do Auxílio Transporte"
        );
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Gerenciar Auxílios",
            "dados"			=>	$this->auxiliosData['auxilios'],
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formAuxilios'){
        
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }


        //verificar se o usuario esta logado
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'acao'              =>  'create',
            "titulo"		    =>  'Cadastro de Auxílios',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formAuxilios'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $auxiliosModel = new AuxiliosModel();
        $dados = $auxiliosModel->where('pk_id_auxilio', $id)->first();
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'dados'     =>  $dados,
            'pagina'    =>  $page,
            'titulo'    =>  'Cadastro de Auxilios',
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\AuxiliosModel();
            $acao = $this->request->getPost('action');

            $arrayRegistro = array(
                'ds_horas_diarias' => $this->request->getPost('ds_horas_diarias'),
                'ds_valor_bolsa' => $this->request->getPost('ds_valor_bolsa'),
                'ds_valor_transporte' => $this->request->getPost('ds_valor_transporte'),
            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                    return redirect()->route('Auxilios')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                    foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Auxilios.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
            }
            if($acao ==="update"){
                $id = $this->request->getPost('pk_id_auxilio');
                if($model->update($id,$arrayRegistro)){
                    return redirect()->route('Auxilios')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Auxilios')->with('error',$model->errors());
                };
            }
        }
    }
    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe manifestos com esse setor
        $contratos = new DadosContratosModel();
        $constratos = $contratos->where('fk_id_auxilio', $id)->first();
        if($constratos){
            return redirect()->route('Auxilios')->with('mensagemError','Auxílio possui contratos vinculados, impossível excluir');
        }

        $model = new AuxiliosModel();
        if($model->delete($id)){
            return redirect()->route('Auxilios')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Auxilios')->with('mensagemError','Erro ao excluir registro');
        }


    }




}