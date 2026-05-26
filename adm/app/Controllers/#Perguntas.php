<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\PerguntasModel;

class Perguntas extends BaseController{



    public function index($camada1 = '',$camada2 = 'pages', $page = 'Perguntas') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelPerguntas = new PerguntasModel();
        $perguntas = $modelPerguntas->orderBy('ds_pergunta', 'asc')->findAll();
       
        $titulosTabela = array(
            "Pergunta","Resposta"
        );
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Perguntas Frequentes",
           
            "perguntas"			=>	$perguntas,
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formPerguntas'){
        
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
            'titulo'        =>  'Registrar Perguntas e Respostas',
        ];
        echo view('layoutDash', $parametros);

    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formPerguntas'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelPerguntas = new PerguntasModel();
        $dados = $modelPerguntas->where('pk_id_pergunta', $id)->first();
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'dados'     =>  $dados,
            'titulo'    =>  'Alterar Perguntas e Respostas',
            
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\PerguntasModel();
            $acao = $this->request->getPost('action');

            $arrayRegistro = array(
                'ds_pergunta' => $this->request->getPost('ds_pergunta'),
                'ds_resposta' => $this->request->getPost('ds_resposta'),
            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                    return redirect()->route('Perguntas')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                    foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Perguntas.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
            }
            if($acao ==="update"){
                $idPergunta = $this->request->getPost('pk_id_pergunta');
                if($model->update($idPergunta,$arrayRegistro)){
                    return redirect()->route('Perguntas')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Perguntas')->with('error',$model->errors());
                };
            }
        }
    }
    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe manifestos com esse setor
        $modelPerguntas = new PerguntasModel();
        $pergunta = $modelPerguntas->where('pk_id_pergunta', $id)->first();
        if($pergunta){
            if($modelPerguntas->delete($id)){
                return redirect()->route('Perguntas')->with('mensagemSuccess','Registro excluído com sucesso');
            }else{
                return redirect()->route('Perguntas')->with('mensagemError','Erro ao excluir registro');
            }
        }else{
            return redirect()->route('Perguntas')->with('mensagemError','Registro inexistente ou já excluido.');
        }
        
       
        


    }




}