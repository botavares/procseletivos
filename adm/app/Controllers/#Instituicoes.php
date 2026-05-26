<?php

namespace App\Controllers;
use FileSystemIterator;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use CodeIgniter\Exceptions\PageNotFoundException;

use App\Models\InstituicoesModel;
use App\Models\CursosModel;
use App\Models\DadosContratosModel;

use App\Modules\Breadcrumbs\Breadcrumbs;

use CodeIgniter\Controller;

class Instituicoes extends BaseController{

    protected $instituicoes;
    

    public function __construct(){
        $InstituicoesModel = new InstituicoesModel();
        
        
        $this->data = [
           'instituicoes'         => $InstituicoesModel->orderBy('ds_nome', 'asc')->findAll(),
        ];
      
    }
    public function index($camada1 = '',$camada2 = 'pages', $page = 'Instituicoes') {

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
         if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
         $titulosTabela = array(
            "Nome da Instituição","Cidade","Email","Telefone"
        );

        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'titulo'        =>  ucfirst('Instituições de Ensino'),
            'instituicoes'  =>  $this->data['instituicoes'],
            'titulosTabela' =>  $titulosTabela,
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view('layoutDash',$parametros);

    }
    public function formularioCadastro($camada1 = 'pages',$camada2 = 'cadastros', $page = 'formInstituicoes') {

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
         if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'titulo'    =>  'Cadastro de Instituição',
        ];
        echo view('layoutDash',$parametros);
    }
     public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formInstituicoes'){

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
         if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $instituicoesModel = new InstituicoesModel();
        $dados = $instituicoesModel->where('pk_id_instituicao', $id)->first();
        $parametros = [
            'camada1'   =>  $camada1,
            'camada2'   =>  $camada2,
            'pagina'    =>  $page,
            'dados'     =>  $dados,
            'titulo'    =>  'Alterar Instituição',
        ];
        echo view('layoutDash',$parametros);
     }
     public function registrar(){
        $instituicoesModel = new InstituicoesModel();
        
        $acao = $this->request->getPost('action');

        $arrayRegistro = array(
            'ds_nome'       => $this->request->getPost('ds_nome'),
            'ds_cnpj'       => preg_replace('/\D/', '', $this->request->getPost('ds_cnpj')),
            'ds_rua'        => $this->request->getPost('ds_rua'),
            'ds_numero'     => $this->request->getPost('ds_numero'),
            'ds_complemento'=> $this->request->getPost('ds_complemento'),
            'ds_bairro'     => $this->request->getPost('ds_bairro'),
            'ds_cep'        => preg_replace('/\D/', '', $this->request->getPost('ds_cep')),
            'ds_cidade'     => $this->request->getPost('ds_cidade'),
            'ds_estado'     => $this->request->getPost('ds_estado'),
            'ds_email'      => $this->request->getPost('ds_email'),
            'ds_telefone'   => preg_replace('/\D/', '', $this->request->getPost('ds_telefone')),
            'ds_numero_convenio' => preg_replace('/\D/', '', $this->request->getPost('ds_numero_convenio')),
        );
        if($acao == 'create'){
            if($instituicoesModel->insert($arrayRegistro)){
                return redirect()->route('Instituicoes')->with('mensagemSuccess','Instituição cadastrada com sucesso');
            }else{
                foreach ($instituicoesModel->errors() as $campo => $erro) {
                    $this->session->setFlashData($campo, $erro);
                }
                return redirect()->route('Instituicoes.formularioCadastro')->back()->withInput()->with('error',$instituicoesModel->errors());
            }
            
        }
        if($acao ==="update"){
                
                $id = $this->request->getPost('pk_id_instituicao');
                if($instituicoesModel->update($id,$arrayRegistro)){
                    return redirect()->route('Instituicoes')->with('mensagemSuccess','Registro alterado com sucesso.');
                }else{
                    var_dump($instituicoesModel->errors());
                    die();
                    return redirect()->route('Instituicoes')->with('mensagemError','O registro não pode ser alterado.');
                };
              
               
            }
       
     }
     public function deletar(){
        //Verificar se existe contatos denpendentes dessa InstituicoesModel
        $contratosModel = new DadosContratosModel();
        $id = $this->request->getPost('chavePrimaria');
        $contratos = $contratosModel->where('fk_id_instituicao', $this->request->getPost('chavePrimaria'))->first();
        
        if($contratos){
            return redirect()->route('Secretarias')->with('mensagemError','Instituição possui contratos cadastrados, impossível excluir');
            
        }
        $InstituicoesModel = new InstituicoesModel();
        if($InstituicoesModel->delete($id)){
            return redirect()->route('Insituicoes')->with('mensagemSuccess','Registro excluído com sucesso');
        }else{
            return redirect()->route('Insituicoes')->with('mensagemError','Erro ao excluir registro');
        }
    }
}