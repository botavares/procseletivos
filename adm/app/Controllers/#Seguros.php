<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\SegurosModel;
use App\Models\EditaisCursosModel;

class Seguros extends BaseController{

    protected $cursosData;
    

    public function __construct(){
        $modelSeguros = new SegurosModel();
        $this->segurosData = [
           'seguros'         => $modelSeguros
                            ->orderBy('ds_status', 'desc')
                            ->orderBy('ds_seguradora', 'asc')
                            ->findAll(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Seguros') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
       
        $titulosTabela = array(
            "Nome da seguradora","Numero da Apólice","Status"
        );
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Dados sobre Seguro do Estagiário",
            "seguros"			=>	$this->segurosData['seguros'],
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formSeguradoras'){
        
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
            "seguros"			=>	$this->segurosData['seguros'],
			"titulo"		    =>  'Cadastro de Seguradora',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formSeguradoras'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $segurosModel = new SegurosModel();
        $dados = $segurosModel->SeguroPorId($id);
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'dados'     =>  $dados,
            'titulo'    =>  'Cadastro do Seguro do Estagiário',
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\SegurosModel();
            $acao = $this->request->getPost('action');

            //update no ds_status para 0 para todos os seguros anteriores
            $model->updateStatusAnteriores();
            

            $arrayRegistro = array(
                'ds_seguradora' => $this->request->getPost('ds_seguradora'),
                'ds_numero_seguro' => $this->request->getPost('ds_numero_seguro'),
                'ds_apolice' => $this->request->getPost('ds_apolice'),
                'ds_cnpj' => $this->request->getPost('ds_cnpj'),
                'ds_status' => 1,
            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                    return redirect()->route('Seguros')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                    foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Seguros.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
            }
            if($acao ==="update"){
                $idSeguro = $this->request->getPost('pk_id_seguro');
                if($model->update($idSeguro,$arrayRegistro)){
                    return redirect()->route('Seguros')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Seguros')->with('error',$model->errors());
                };
            }
        }
    }
    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe manifestos com esse setor
        $modelEditaisCursos = new EditaisCursosModel();
        $cursos = $modelEditaisCursos->where('fk_id_seguro', $id)->first();
        if($cursos){
            return redirect()->route('Cursos')->with('mensagemError','Esse curso está registrado em editais anteriores, impossível excluir');
        }
        
        $modelCursos = new CursosModel();
        if($modelCursos->delete($id)){
            return redirect()->route('Cursos')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Cursos')->with('mensagemError','Erro ao excluir registro');
        }


    }

    public function ativarDesativarSeguro(){
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $segurosModel = new SegurosModel();

        $idSeguro    = $this->request->getPost('idSeguro');
        $status      = $this->request->getPost('status');

        $dadosAtualizar = [
            'ds_status' => $status,
        ];

        $this->updateStatusAnteriores();

        $segurosModel->where('pk_id_seguro', $idSeguro)->set($dadosAtualizar)->update();

        return $this->response->setJSON([
            csrf_token() => csrf_hash(), // novo token
            'status' => 'success',
            'message' => 'Comparecimento atualizado com sucesso.']);
    }





}