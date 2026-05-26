<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\ManifestacoesModel;
use App\Models\AssuntosModel;
use App\Models\UsuariosModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use App\Modules\Breadcrumbs\Breadcrumbs;

use FileSystemIterator;
use App\Models\DashModel;
use App\Models\CanaisModel;
use App\Models\SetoresModel;
use App\Models\TiposManifestosModel;

use App\Traits\AtualizarAndamentoTrait;
use App\Traits\RegistrarEventoTrait;

use CodeIgniter\Controller;

class Respostas extends BaseController{
    use AtualizarAndamentoTrait;
    use RegistrarEventoTrait;
    protected $respostasData;
    

    public function __construct(){
      
      
    }
    /*==============================================================================
        FUNÇÃO: gerarResposta;
        OBJETIVO: abre formulário de resposta da manifestação;
        PARAMETROS: - $idManifesto;$camada1;$camada2; $page;
        CRIAÇÃO:05/12/2024
        MODIFICADO:
        RESUMO: Função abre um formulário de resposta para a manifestação
    ==============================================================================*/
    public function gerarResposta($idManifesto,$camada1 = 'pages',$camada2 = 'cadastros', $page = 'formGerarResposta'){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos     =   new \App\Models\ManifestacoesModel();
        $manifesto      =   $Manifestos->listarManifestosID($idManifesto);

        $dadosResposta = array(
        'camada1'           =>  $camada1,
        'camada2'           =>  $camada2,
        'pagina'            =>  $page,
        'idManifesto'       =>  $manifesto->pk_id_manifesto,
        'dados'             =>  $manifesto,
        'perfil'            =>  session('perfil'),
        'user'		        =>	session('nome'),
        'titulo'            =>  "Responder Manifestação número ".$manifesto->ds_numero_manifestacao,
        );
        echo view('layoutDash', $dadosResposta);

    }

    /*==============================================================================
        FUNÇÃO: registrarResposta;
        OBJETIVO: Registrar a resposta da manifestação;
        PARAMETROS: - $idManifesto: ID da manifestação;
        CRIAÇÃO:05/12/2024
        MODIFICADO:
        RESUMO: Função irá registrar uma resposta para a manifestação. Caso a manifestação
                tenha uma resposta e o manifestante tenha contestado, a função irá gerar uma 
                nova resposta para a contestação. Se não houver uma nova contestação, a função
                não permite uma nova resposta.
    ==============================================================================*/
    public function registrarResposta(){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos = new \App\Models\ManifestacoesModel();
        $listarManifestos = $Manifestos->listarManifestosID($this->request->getPost('fk_id_manifesto'));
        $protocolo = $listarManifestos->ds_protocolo;

        $Respostas = new \App\Models\RespostasContestacoesModel();

        if($this->request->getPost('action') == 'create'){
            //verificar se existe resposta para esse manifesto;
            $resposta = $Respostas
            ->where('fk_id_manifesto', $this->request->getPost('fk_id_manifesto'))
            ->where('ds_resposta', 1)
            ->orderBy('pk_id_resposta','DESC')
            ->first();

            $responder = 'TRUE';
            $isResposta = 1;
            $tipoResposta = 2; // 2 = resposta inicial
            $frase = 'Respondeu uma Manifestação';

            if($resposta){
                //verificar se a última resposta desse manifesto foi uma contestação;
                $contestacao = $Respostas
                ->where('fk_id_manifesto', $this->request->getPost('fk_id_manifesto'))
                ->where('ds_contestacao', 1)
                ->orderBy('pk_id_resposta', 'DESC')
                ->first();

                if($contestacao && $contestacao->pk_id_resposta > $resposta->pk_id_resposta){
                    $responder = 'TRUE';
                    $tipoResposta = 4; // 4 = resposta de contestação
                    $frase = 'Respondeu uma Contestação';
                }else{
                    $responder = 'FALSE';}
            }
            if($this->request->getPost('ds_encerrado')){
                $andamento = "RE";
                $frase = $frase." e Encerrou o Manifesto.";
            }else{
                $andamento = "R";
            }
            if($responder == 'TRUE'){
                $dadosResposta = array(
                    'fk_id_manifesto'               => $this->request->getPost('fk_id_manifesto'),
                    'ds_resposta'                   => $isResposta,
                    'ds_contestacao'                => 0,
                    'ds_texto_resposta'             => $this->request->getPost('ds_texto_resposta'),
                    'ds_agente'                     => session('id'),
                    'ds_tipo_resposta'              =>  $tipoResposta, //2 = resposta; 3 = contestação(feita por manifestante pelo portal); 4 = resposta de contestação
                    'ds_data_resposta'              => date('Y-m-d'),
                    'ds_hora_resposta'              => date('H:i:s'),
                    'ds_data_alteracao_resposta'    => '',
                    'ds_hora_alteracao_resposta'    => '',
                    
                );
                if($Respostas->insert($dadosResposta)){
                    $this->atualizarAndamento($Respostas->insertID(),$dadosResposta,$andamento);

                    $this->registrarEvento(session('id'),$frase,$this->request->getPost('fk_id_manifesto'),$protocolo);

                    return redirect()->route('gerenciarManifestos')->with('mensagemSuccess','Manifestação respondida com sucesso');
                }else{
                //mostrar a mensagem de erro no insert
                return redirect()->route('gerenciarManifestos')->with('errors',$Respostas->errors());
                }
            }else{
                return redirect()->route('gerenciarManifestos')->with('mensagemError','Manifestação '.$this->request->getPost('fk_id_manifesto').' ja foi respondida ou sua última contestação já foi respondida.');
            }
        }
        if($this->request->getPost('action') == 'update'){
            $dadosResposta = array(
                'ds_texto_resposta' => $this->request->getPost('ds_texto_resposta'),
                'ds_agente_alteracao'  => session('id'),
                'ds_data_alteracao_resposta' => date('Y-m-d'),
                'ds_hora_alteracao_resposta' => date('H:i:s'),
            );
            if($Respostas->update($this->request->getPost('pk_id_resposta'), $dadosResposta)){

                $this->registrarEvento(session('id'),'Alterou a resposta da manifestação',$this->request->getPost('fk_id_manifesto'),$protocolo);

                return redirect()->route('gerenciarManifestos')->with('mensagemSuccess','Resposta da manifestação alterada.');
            }else{
                return redirect()->route('gerenciarManifestos')->with('mensagemError','Manifestação '.$this->request->getPost('fk_id_manifesto').' não pode ser alterada.');
            }
        }
    }

    /*==============================================================================
        FUNÇÃO: alterarResposta;
        OBJETIVO: abrir formulário que dá condição do agente para alterar a resposta
        PARAMETROS: - $idManifesto;
        CRIAÇÃO:06/01/2024
        MODIFICADO:
        RESUMO: Agente pode alterar a resposta do manifesto.
    ==============================================================================*/
    public function alterarResposta($idManifesto,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formAlterarResposta'){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos     =   new \App\Models\ManifestacoesModel();
        $manifesto      =   $Manifestos->listarManifestosID($idManifesto);
        $verificarTramitado     =   $Manifestos->verificarTramitado($idManifesto);
        $verificarRespondido    =   $Manifestos->verificarUltimaResposta($idManifesto);

        $tiposModel          =   new \App\Models\TiposManifestosModel();
        $tipos          =   $tiposModel->where('ds_status', 1)->findAll();

        $setoresModel   =   new \App\Models\SetoresModel();
        $setores        =   $setoresModel->where('ds_status', 1)->findAll();

        $assuntosModel  =   new \App\Models\AssuntosModel();
        $assuntos       =   $assuntosModel->listarsetoresAssuntos($manifesto->fk_id_setor);
  

        $dadosResposta = array(
        'camada1'           =>  $camada1,
        'camada2'           =>  $camada2,
        'pagina'            =>  $page,
        'tipos'             =>  $tipos,
        'setores'           =>  $setores,
        'assuntos'          =>  $assuntos,
        'idManifesto'       =>  $manifesto->pk_id_manifesto,
        'dados'             =>  $manifesto,
        'verificarTramitado'    =>  $verificarTramitado,
        'verificarRespondido'   =>  $verificarRespondido,
        'perfil'            =>  session('perfil'),
        'user'		        =>	session('nome'),
        'titulo'            =>  "ALTERAR RESPOSTA DE MANIFESTO ".$manifesto->ds_numero_manifestacao,
        );
        echo view('layoutDash', $dadosResposta);
    }

     /*==============================================================================
        FUNÇÃO: gerarRespostaContestacao;
        OBJETIVO: abre formulário de resposta à contestações feitas pelo cidadão;
        PARAMETROS: - $idManifesto;$camada1;$camada2; $page;
        CRIAÇÃO:20/01/2025
        MODIFICADO:
        RESUMO: Função abre um formulário de resposta para a manifestação
    ==============================================================================*/
    public function gerarRespostaContestacao($idManifesto,$camada1 = 'pages',$camada2 = 'cadastros', $page = 'formResponder'){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos     =   new \App\Models\ManifestacoesModel();
        $manifesto      =   $Manifestos->listarManifestosID($idManifesto);
        $respostas      =   new \App\Models\RespostasContestacoesModel();
        $ultimaResposta =   $respostas
                            ->where('fk_id_manifesto', $idManifesto)
                            ->where('ds_resposta', 1)
                            ->orderBy('ds_data_resposta', 'DESC')
                            ->orderBy('ds_hora_resposta', 'DESC')
                            ->first();
                            
        $ultimaContestacao  =   $respostas
                                ->where('fk_id_manifesto', $idManifesto)
                                ->where('ds_contestacao', 1)
                                ->orderBy('ds_data_resposta', 'DESC')
                                ->orderBy('ds_hora_resposta', 'DESC')
                                ->first();

        $dadosResposta = array(
        'camada1'           =>  $camada1,
        'camada2'           =>  $camada2,
        'pagina'            =>  $page,
        'idManifesto'       =>  $manifesto->pk_id_manifesto,
        'dados'             =>  $manifesto,
        'resposta'          =>  $ultimaResposta,
        'contestacao'       =>  $ultimaContestacao,
        'perfil'            =>  session('perfil'),
        'user'		        =>	session('nome'),
        'titulo'            =>  "Responder Contestação do Manifesto número ".$manifesto->ds_numero_manifestacao,
        );
        echo view('layoutDash', $dadosResposta);

    }

    /*==============================================================================
        FUNÇÃO: marcarRespondido;
        OBJETIVO: Alterar o andamento da manifestação como respondido;
        PARAMETROS: - $idManifesto;
        CRIAÇÃO:05/12/2024
        MODIFICADO:
        RESUMO: Função altera o andamento da manifestação para respondido de imediato pela equipe da Ouvidoria.
    ==============================================================================*/
    public function marcarRespondido($idManifesto){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos = new \App\Models\ManifestacoesModel();
        $manifesto = $Manifestos->where('pk_id_manifesto', $idManifesto)->first();
        $andamentoManifesto = new \App\Models\ManifestosAndamentoModel();
        $dadosAndamento = array(
            'fk_id_manifesto'   =>  $manifesto->pk_id_manifesto,
            'ds_data_analise'   =>  date('Y-m-d'),
            'ds_hora_analise'   =>  date('H:i:s'),
            'ds_observacao'     =>  'Manifestação respondida de imediato pela equipe da Ouvidoria.',
            'ds_status'         =>  1,
            'ds_tramitado'      =>  0,
            'ds_respondido'     =>  1,
            'ds_contestado'     =>  0,
            'ds_agente_ouvidoria'   =>  session('id'),
        );
       
        //adicionar à tabela tb_manifestacoes_andamentos
        if($andamentoManifesto->insert($dadosAndamento)){
            $manifesto = new \App\Models\ManifestacoesModel();
            //id recem gerado pelo insert em tb_manifestos_andamentos
            $idAndamento = $andamentoManifesto->insertID();
            //atualizar o campo ds_andamento_atual da tabela tb_manifestos com o novo id de andamento
            $manifesto->where('pk_id_manifesto',$idManifesto)->update(['ds_andamento_atual'=>$idAndamento]);
            return redirect()->route('Manifestacoes')->with('success','Manifestação respondida com sucesso');
        }else{
           //mostrar a mensagem de erro no insert
           return redirect()->route('Manifestacoes')->with('error',$andamentoManifesto->errors());
        }
    }

}