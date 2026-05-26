<?php

namespace App\Controllers;
use DateTime;

use FileSystemIterator;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use CodeIgniter\Exceptions\PageNotFoundException;

use App\Models\DadosContratosModel;
use App\Models\FeriasModel;
use App\Models\AcademicosModel;
use App\Models\CandidatosModel;
use App\Models\CursosModel;
use App\Models\SetoresModel;
use App\Models\InstituicoesModel;
use App\Services\ContagemDeTempoService;
class Ferias extends BaseController{

    protected $feriasData;
    

    public function __construct(){
        $modelFerias = new FeriasModel();
        
        $this->feriasData = [
           'ferias'         => $modelFerias->getFerias(),
        ];
      
    }

    public function index($camada1 = '',$camada2 = 'pages', $page = 'Ferias') {
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

       
        $titulosTabela = array(
            "Nome do Estagiário","Ano referente","Data de Início", "Data de Fim","Dias de Recesso","Status"
        );
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            "titulo"            =>  "Gerenciar Recessos",
            "ferias"			=>	$this->feriasData['ferias'],
			"titulosTabela"		=>	$titulosTabela,
			'user'				=>	session('nome'),
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioCadastro($id=null,$camada1 = 'pages',$camada2 = 'cadastros', $page = 'formFerias'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        //verificar se o usuario esta logado
        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $contarTempo = new ContagemDeTempoService();
        
     
        $modelAcademicos    = new AcademicosModel();
        $candidatosModel    = new CandidatosModel();
        $contratosModel     = new DadosContratosModel();
        $cursosModel        = new CursosModel();
        $setoresModel       = new SetoresModel();

        $dadosAcademicos    = $modelAcademicos->where('pk_id_candidato', $id)->first();
        $dadosCursos        = $cursosModel->where('pk_id_curso', $dadosAcademicos->fk_id_curso)->first();
        
        $dadosCandidato     = $candidatosModel->where('pk_id_candidato', $id)->first();
        $dadosContratos     = $contratosModel
                            ->where('fk_id_candidato', $id)
                            ->where('ds_status', '1')
                            ->first();
        
        $dadosSetor         = $setoresModel->where('pk_id_setor', $dadosContratos->fk_id_setor)->first();
        
        $totalDiasFerias = $contarTempo->contagemDeTempoContrato($id, $dadosContratos->fk_id_setor, $dadosContratos->pk_id_contrato); 
        
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'acao'              =>  'create',
            'curso'             =>  $dadosCursos->ds_nome_curso,
            'setor'             =>  $dadosSetor->ds_nome_setor,
            'totalDiasFerias'   =>  $totalDiasFerias['feriasTiradas'],
            'totalDiasRestantes'=>  (int)$totalDiasFerias['diasFerias'],
            'dadosCandidato'    =>  $dadosCandidato,
            'dadosContratos'    =>  $dadosContratos,
			"titulo"		    =>  'Registro de Recessos',
        ];
        echo view('layoutDash', $parametros);
    }

    public function formularioAlteracao($id,$camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formFerias'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if(!checklogged()){
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        
        $academicosModel    = new AcademicosModel();
        $candidatosModel    = new CandidatosModel();
        $contratosModel     = new DadosContratosModel();
        $cursosModel        = new CursosModel();
        $setoresModel       = new SetoresModel();
        $feriasModel        = new FeriasModel();

        $dadosFerias        = $feriasModel->getFeriasId($id);

        $dadosAcademicos    = $academicosModel->where('pk_id_candidato', $dadosFerias->fk_id_estagiario)->first();
        $dadosCursos        = $cursosModel->where('pk_id_curso', $dadosAcademicos->fk_id_curso)->first();
        
        $dadosCandidato     = $candidatosModel->where('pk_id_candidato', $dadosFerias->fk_id_estagiario)->first();
        $dadosContratos     = $contratosModel->where('fk_id_candidato', $dadosFerias->fk_id_estagiario)->first();
        $dadosSetor         = $setoresModel->where('pk_id_setor', $dadosContratos->fk_id_setor)->first();
        
        $contarTempo = new ContagemDeTempoService();

        $totalDiasFerias = $contarTempo->contagemDeTempo($dadosFerias->fk_id_estagiario, $dadosContratos->fk_id_setor);

        
        $parametros = [
            'camada1'           =>  $camada1,
            'camada2'           =>  $camada2,
            'pagina'            =>  $page,
            'curso'             =>  $dadosCursos->ds_nome_curso,
            'setor'             =>  $dadosSetor->ds_nome_setor,
            'totalDiasFerias'   =>  $totalDiasFerias['feriasTiradas'],
            'totalDiasRestantes'=>  $totalDiasFerias['diasFerias']-$totalDiasFerias['feriasTiradas'],
            'dadosCandidato'    =>  $dadosCandidato,
            'dadosContratos'    =>  $dadosContratos,
            'dadosFerias'       =>  $dadosFerias,
			"titulo"		    =>  'Alterar Registro de Recessos',
        ];
        echo view('layoutDash',$parametros);
    }

    public function registrar(){

        if($this->request->getMethod() === 'post'){
            //Instanciar Model
            $model = new \App\Models\FeriasModel();
            $acao = $this->request->getPost('action');

            list($ano, $mes, $dia) = explode('/', $this->request->getPost('ds_data_inicio'));
            $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
            $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
            $dataInicio = $ano . '-' . $mes . '-' . $dia;

            list($ano, $mes, $dia) = explode('/', $this->request->getPost('ds_data_final'));
            $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
            $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
            $dataFinal = $ano . '-' . $mes . '-' . $dia;

            $arrayRegistro = array(
                'ds_ano_referente' => $this->request->getPost('ds_ano_referente'),
                'fk_id_contrato' => $this->request->getPost('pk_id_contrato'),
                'fk_id_estagiario' => $this->request->getPost('pk_id_candidato'),
                'ds_dias_ferias' => $this->request->getPost('ds_dias_ferias'),
                'ds_data_inicio' => date('Y-m-d', strtotime($dataInicio)),
                'ds_data_final' => date('Y-m-d', strtotime($dataFinal)),
                'ds_status' => $this->request->getPost('ds_status'),
            );

            if($acao ==="create"){
                if($model->insert($arrayRegistro)){
                    $this->imprimirDeclaracaoDeFerias($this->request->getPost('pk_id_contrato'), $this->request->getPost('pk_id_candidato'));
                    return redirect()->route('Ferias')->with('mensagemSuccess','Registro gravado com sucesso');
                }else{
                    foreach ($model->errors() as $campo => $erro) {
                        $this->session->setFlashData($campo, $erro);
                    }
                    return redirect()->route('Ferias.formularioCadastro')->back()->withInput()->with('error',$model->errors());
                }
                
            }

            if($acao ==="update"){
                $id = $this->request->getPost('pk_id_ferias');
                if($model->update($id,$arrayRegistro)){
                    return redirect()->route('Ferais.formularioAlteracao')->with('mensagemSuccess','Registro alterado com sucesso');
                }else{
                    return redirect()->route('Ferias.formularioAlteracao')->with('error',$model->errors());
                };
            }
        }
    }

    private function feriasUsufruidas($idCandidato,$idContrato){
        
        
        $feriasModel        = new FeriasModel();
        $dadosFerias        = $feriasModel
                            ->where('fk_id_estagiario', $idCandidato)
                            ->where('fk_id_contrato', $idContrato)
                            ->where('ds_status', 1)
                            ->findAll();

        if($dadosFerias != null){
            //somar quantos dias de ferias o candidato tem
            $totalDiasFerias = 0;
            foreach($dadosFerias as $valueFerias){
                $final = new DateTime($valueFerias->ds_data_final);
                $inicial = new DateTime($valueFerias->ds_data_inicio);
                $total = $final->diff($inicial);
                $totalDiasFerias += $total->days;
            }
        }else{
            $totalDiasFerias = 0;
        }

        return $totalDiasFerias;
        
    }
    public function deletar(){
        
        $id = $this->request->getPost('chavePrimaria');
        //verificar se existe manifestos com esse setor
        $modelFerias = new FeriasModel();
        $ferias = $modelFerias->where('ds_status', 1)
                    ->where('pk_id_ferias', $id)
                    ->first();
        if($ferias){
            return redirect()->route('Ferias')->with('mensagemError','O recesso escolhido já foi usufruida, impossível excluir');
        }else{
            if($modelFerias->delete($id)){
                return redirect()->route('Ferias')->with('mensagemSuccess','Registro excluído com sucesso');
            }else{
                return redirect()->route('Ferias')->with('mensagemError','Erro ao excluir registro');
            }
        }
    }

    public function imprimirDeclaracaoDeFerias($contrato){
        $dompdf                 =   new Dompdf();
        $candidatosModel		=	new \App\Models\CandidatosModel();
        $contratosModel		    =	new \App\Models\DadosContratosModel();
        $feriasModel            =   new \App\Models\FeriasModel();
        $motivosModel           =   new \App\Models\MotivosRescisaoModel();
        $dadosPrefeituraModel   =   new \App\Models\DadosPrefeituraModel();

        $dadosContrato          =   $contratosModel->getDadoContrato($contrato);
        $dadosCandidato         =   $candidatosModel->where('pk_id_candidato', $dadosContrato->fk_id_candidato)->first();
        $dadosPrefeitura        =   $dadosPrefeituraModel->first();
        
        $dadosFerias            =   $feriasModel->where('fk_id_contrato', $contrato)
                                                ->where('fk_id_estagiario', $dadosContrato->fk_id_candidato)
                                                ->first();

        list($anoi, $mesi, $diai) = explode('-', $dadosFerias->ds_data_inicio);
            $mesi = str_pad($mesi, 2, '0', STR_PAD_LEFT);
            $diai = str_pad($diai, 2, '0', STR_PAD_LEFT);
            $dataInicio = $diai . '/' . $mesi . '/' . $anoi;

        list($anof, $mesf, $diaf) = explode('-', $dadosFerias->ds_data_final);
            $mesf = str_pad($mesf, 2, '0', STR_PAD_LEFT);
            $diaf = str_pad($diaf, 2, '0', STR_PAD_LEFT);
            $dataFinal = $diaf . '/' . $mesf . '/' . $anof;
        
        //diferenca entre as datas
        $data1 = new DateTime($dadosFerias->ds_data_inicio);
        $data2 = new DateTime($dadosFerias->ds_data_final);
        $interval = $data1->diff($data2);
        $interval = $interval->format('%a');
        
        $mesData = (int) date('m');
        $nomeMes = mesPorExtenso($mesData);
            
        
        $dados = [
            // === Identificação visual ===
            'brasao'                   => imageToBase64(ROOTPATH . '/external/img/logo.png'),
            'fundo'                    => imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'titulo_documento'         => 'DECLARAÇÃO DE RECESSO',
            'nome_estagiario'          => $dadosCandidato->ds_nome,
            'dias_gozados'             => $interval,
            'numero_termo'             => $dadosContrato->ds_numero_termo."/".$dadosContrato->ds_ano_termo,
            'nomeMes'                  => $nomeMes,
            'local_assinatura'         => 'Divinópolis',
            'data_assinatura'          => date('d'). ' de '. mesPorExtenso($mesData). ' de '.date('Y'),
            'assinatura_estagiario'    => '                          ',
            'assinatura_secretario'    => '                          ',
            'nome_secretario'          => $dadosPrefeitura->ds_diretor_rh,

        ];

       imprimir($dompdf,'ferias',$dados);

    }


}