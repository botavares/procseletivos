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

class Tramites extends BaseController{
    use AtualizarAndamentoTrait;
    use RegistrarEventoTrait;
    protected $tramitesData;
    

    public function __construct(){
      
      
    }

    /*==============================================================================
        FUNÇÃO: gerarTramite;
        OBJETIVO: Direcionar o manifesto para o setor/secretaria pertinente
        PARAMETROS: - $idManifesto;
        CRIAÇÃO:17/12/2024
        MODIFICADO:
        RESUMO: Ao tramitar o manifesto, será gerado um documento com o protocolo e informações 
                da manifestação para ser encaminhado ao setor responsável
    ==============================================================================*/
    public function gerarTramite($idManifesto,$camada1 = 'pages',$camada2 = 'cadastros', $page = 'formGerarTramite'){
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
        'titulo'            =>  "TRAMITAR MANIFESTO ".$manifesto->ds_numero_manifestacao,
        );
        echo view('layoutDash', $dadosResposta);
    }

    /*==============================================================================
        FUNÇÃO: alterarTramite;
        OBJETIVO: Alterar tipo, setor ou assunto do manifesto para o setor/secretaria pertinente
        PARAMETROS: - $idManifesto;
        CRIAÇÃO:06/01/2024
        MODIFICADO:
        RESUMO: Ao tramitar o manifesto, pode ser que o cidadão por motivo de desconhecer
                os processos aponte um assunto, um setor ou até mesmo o tipo do manifesto
                por isso a necessidade do agente alterar.
    ==============================================================================*/
    public function alterarTramite($idManifesto,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formAlterarTramite'){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos             =   new \App\Models\ManifestacoesModel();
        $Andamentos             =   new \App\Models\ManifestosAndamentoModel();
        $Tramitados             =   new \App\Models\ManifestosTramitesModel();
        $Respostas              =   new \App\Models\RespostasContestacoesModel();

        $manifesto              =   $Manifestos->listarManifestosID($idManifesto);
        $verificarTramitado     =   $Tramitados->verificarTramitado($idManifesto);
        $verificarRespondido    =   $Respostas->verificarRespondido($idManifesto);
      

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
        'titulo'            =>  "ALTERAR TRAMITE DE MANIFESTO ".$manifesto->ds_numero_manifestacao,
        );
        echo view('layoutDash', $dadosResposta);
    }

    /*==============================================================================
        FUNÇÃO: registrarTramite;
        OBJETIVO: Registrar o trâmite da manifestação;
        PARAMETROS: - $idManifesto: ID da manifestação;
        CRIAÇÃO:17/12/2024
        MODIFICADO:
        RESUMO: Função irá registrar o trâmite da manifestação. Caso a manifestação
                já tenha uma resposta, não poderá ser tramitado. 
    ==============================================================================*/
    public function registrarTramite(){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos = new \App\Models\ManifestacoesModel();
        $tramites = new \App\Models\ManifestosTramitesModel();
        
        $listaManifesto = $Manifestos->listarManifestosID($this->request->getPost('fk_id_manifesto'));
        if($listaManifesto){
            $assunto = $listaManifesto->fk_id_assunto;
            $setor = $listaManifesto->fk_id_setor;
            $tipo = $listaManifesto->ds_tipo_manifesto;    
        }
        $protocolo = $listaManifesto->ds_protocolo;
        if($this->request->getPost('action') == 'create'){
            
            //verificar se existe tramite para esse manifesto;
            $tramite = $tramites
            ->where('fk_id_manifesto', $this->request->getPost('fk_id_manifesto'))
            ->orderBy('pk_id_tramite','DESC')
            ->first();


            $tramitar = 'TRUE';

            if($tramite){
                //verificar se manifesto já possui trâmite;
                $tramite = $tramites
                ->where('fk_id_manifesto', $this->request->getPost('fk_id_manifesto'))
                ->orderBy('pk_id_tramite', 'DESC')
                ->first();

                if($tramite){
                    $tramitar = 'FALSE';
                    
                }else{
                    $tramitar = 'TRUE';
                }
            }
            if($tramitar == 'TRUE'){
                $dadosTramite = array(
                    'fk_id_manifesto' => $this->request->getPost('fk_id_manifesto'),
                    'ds_texto_observacao' => $this->request->getPost('ds_texto_observacao'),
                    'ds_agente'  => session('id'),
                    'ds_data_tramite' => date('Y-m-d'),
                    'ds_hora_tramite' => date('H:i:s'),
                    'ds_data_alteracao' => '',
                    'ds_hora_alteracao' => '',
                    'ds_agente_alteracao' => '',
                );
                if($tramites->insert($dadosTramite)){
                    
                    $this->atualizarAndamento($tramites->insertID(),$dadosTramite, 'T');


                    $this->registrarEvento(session('id'), 'Tramitou manifesto', $this->request->getPost('fk_id_manifesto'),$protocolo);


                    //$this->sucesso($this->request->getPost('fk_id_manifesto'), 'T');
                    return redirect()->route('Tramites.sucesso',[$this->request->getPost('fk_id_manifesto'), 'T'])->with('mensagemSuccess','Manifesto tramitado com sucesso');
                    //$this->imprimirFormularioEncaminhamento($this->request->getPost('fk_id_manifesto'));
                    //return redirect()->route('Manifestacoes.gerenciarManifestos')->with('mensagemSuccess','Manifesto tramitado com sucesso');
                }else{
                //mostrar a mensagem de erro no insert
                return redirect()->route('Manifestacoes.gerenciarManifestos')->with('error',$tramites->errors());
                }
            }else{
             return redirect()->route('Manifestacoes.gerenciarManifestos')->with('error',$tramites->errors());
            }
        }
        if($this->request->getPost('action') == 'update'){
           
            
            $dadosManifesto = array(
                'ds_tipo_manifesto' => $this->request->getPost('ds_tipo_manifesto'),
                'fk_id_setor'  => $this->request->getPost('pk_id_setor'),
                'fk_id_assunto'  => $this->request->getPost('pk_id_assunto'),
            );
            
            if($Manifestos->update($this->request->getPost('fk_id_manifesto'),$dadosManifesto)){
                $dadosTramite = array(
                    'ds_texto_observacao' => $this->request->getPost('ds_texto_observacao'),
                    'ds_agente_alteracao'  => session('id'),
                    'ds_data_alteracao' => date('Y-m-d'),
                    'ds_hora_alteracao' => date('H:i:s'),
                    
                );
                $tramites->update($this->request->getPost('pk_id_tramite'),$dadosTramite);
                $frase = "Alterou o Trâmite: Tipo: ".$tipo.", Assunto: ".$assunto.", Setor: ".$setor.
                " Para Tipo:".$this->request->getPost('ds_tipo_manifesto').
                ", Assunto: ".$this->request->getPost('pk_id_assunto').
                ", Setor: ".$this->request->getPost('pk_id_setor');
                $this->registrarEvento(session('id'), $frase, $this->request->getPost('fk_id_manifesto'),$protocolo);
                
                return redirect()->route('gerenciarManifestos')->with('mensagemSuccess','Manifesto alterado com sucesso');
            }else{
                dd($dadosManifesto);
            }
        }
        
    }

    public function sucesso($idManifesto,$evento, $camada1 = '', $camada2 = 'pages', $page = 'SucessoTramite'){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        if($evento == 'T'){
            $mensagem   = 'Manifesto tramitado com sucesso';
            $titulo     = 'Tramite de manifesto';
        }
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'idManifesto'       =>  $idManifesto,
            'evento'            =>  $evento,
            'mensagem'          =>  $mensagem,
            'titulo'            =>  $titulo,
        
        ];

        echo view('layoutDash', $parametros);

    }

    public function imprimirFormularioEncaminhamento($idManifesto){
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $Manifestos     =   new \App\Models\ManifestacoesModel();
        $manifesto      =   $Manifestos->listarManifestosID($idManifesto);

        if($manifesto){
            $tramites       =   new \App\Models\ManifestosTramitesModel();
            $tramite        =   $tramites->listarManifestosId($idManifesto);

            $bairros        =   new \App\Models\BairrosModel();
            $bairro         =   $bairros->where('pk_id_bairro',$manifesto->fk_id_bairro)->first();
            if($bairro){
                $nomeBairro = $bairro->ds_nome_bairro;
            }else{
                $nomeBairro = "N/A";
            }

            $canais         =   new \App\Models\CanaisModel();
            $canal          =   $canais->where('pk_id_canal',$manifesto->ds_canal)->first();

            $setores        =   new \App\Models\SetoresModel();
            $setor          =   $setores->setorSecretaria($manifesto->fk_id_setor);

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // Permite carregar imagens externas

            $dompdf = new Dompdf($options);
            $dadosResposta = array(
            'brasao'        =>  imageToBase64(ROOTPATH . '/external/img/brasao.png'),
            'fundo'		    =>	imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'idManifesto'       =>  $manifesto->pk_id_manifesto,
            'protocolo'         =>  $manifesto->ds_protocolo,
            'data'              =>  $manifesto->ds_datacadastro,
            'manifestante'      =>  ($manifesto->ds_anonimo == 1) ? 'IDENTIFICADO' : 'ANÔNIMO',
            'nome'              =>  $manifesto->ds_nome,
            'bairro'            =>  $nomeBairro,
            'telefone'          =>  $manifesto->ds_telefone,
            'email'             =>  $manifesto->ds_email,
            'canal'             =>  $manifesto->ds_canal,
            'setor'             =>  $setor->ds_nome_setor,
            'assunto'           =>  $manifesto->ds_assunto,
            'secretaria'        =>  $setor->ds_nome_secretaria,
            'observacao'        =>  $tramite->ds_texto_observacao,
            'relato'            =>  $manifesto->ds_texto_manifestacao,
            'perfil'            =>  session('perfil'),
            'user'		        =>	session('nome'),
            'titulo'            =>  "FORMULÁRIO DE ENCAMINHAMENTO DA OUVIDORIA",
            );
            $nomeDocumento = "formularioencaminhamento";
            /*var_dump($dadosResposta);
            die();*/
            $this->registrarEvento(session('id'),"Imprimiu o Trâmite - Formulário de encaminhamento",$idManifesto,$manifesto->ds_protocolo);
            imprimir($dompdf, $nomeDocumento, $dadosResposta);
        }else{
            return redirect()->route('gerenciarManifestos')->with('mensagemError','Manifesto não encontrado');
        }
    }

}