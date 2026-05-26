<?php

namespace App\Controllers;
use FileSystemIterator;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use CodeIgniter\Exceptions\PageNotFoundException;



use App\Models\EditaisModel;
use App\Models\CursosModel;
use App\Models\EditaisCursosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\DadosContratosModel;
use App\Models\UnidadesModel;
use App\Models\AcademicosModel;
use App\Models\CandidatosModel;
use App\Models\ConvocadosModel;

use App\Modules\Breadcrumbs\Breadcrumbs;

//traits
use App\Traits\AtualizarAndamentoTrait;
use App\Traits\RegistrarEventoTrait;

// services
use App\Services\LogsService;
use App\Services\EmailService;
use App\Services\ConvocacoesService;

use CodeIgniter\Controller;

class Convocados extends BaseController{

    use AtualizarAndamentoTrait;
    use RegistrarEventoTrait;
    protected $manifestosData;
    

    public function __construct(){
      
      
    }
    public function index($idEdital = null, $idCurso = null, $camada1 = '', $camada2 = 'pages', $page = 'Convocados'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $modelEditais       = new EditaisModel();
        $modelCursos        = new CursosModel();
        $modelConvocados    = new ConvocadosModel();
        //buscar todos convocados
        $convocados         = $modelConvocados->getTodosConvocados();
        //aqui a verificação retira aqueles que passaram mais de 48 horas sem se manifestar
        $verificacao        = $this->verificarInteresse($convocados);
        if($verificacao  == true){
            $convocadosVerificado = $modelConvocados->getCovocadosPorEditalCurso($idEdital, $idCurso);
        }
        
        $this->abrirPaginaConvocacao($idEdital, $idCurso, $convocadosVerificado, $camada1, $camada2, $page);
    }

    public function convocados($camada1 = '', $camada2 = 'pages', $page = 'Convocados'){
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $modelConvocados= new ConvocadosModel();
        $convocados     = $modelConvocados->getTodosConvocados();
        $verificacao    = $this->verificarInteresse($convocados);
        if($verificacao  == true){
            $convocadosVerificado = $modelConvocados->getTodosConvocados();
        }
        $idEdital       = null;
        $idCurso        = null;

        
        $this->abrirPaginaConvocacao($idEdital, $idCurso, $convocadosVerificado, $camada1, $camada2, $page);

    }


    public function abrirPaginaConvocacao($idEdital = null, $idCurso = null, $convocados, $camada1 = '', $camada2 = 'pages', $page = 'Convocados'){
         $titulosTabela = ["Período","Nome do Candidato","Nascimento","Telefone","Email","Curso","Data da Convocação"];
        
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'convocados'    => $convocados,
            'idEdital'      => $idEdital,
            'idCurso'       => $idCurso,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            "titulosTabela" => $titulosTabela,
            'titulo'        => "Gerenciar Convocados (aguardando contratação)",
        ];

        echo view('layoutDash', $parametros);
    }

    public function formularioConvocar($id,$camada1 = 'pages', $camada2 = 'cadastros', $page = 'formConvocar'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $academicosModel= new AcademicosModel();
        $candidatosModel = new CandidatosModel();
        $cursosModel    = new CursosModel();
        $modelEditais   = new EditaisModel();
        $editaisCandidatosModel = new EditaisCandidatosModel();

        $dadosAcademicos        = $academicosModel->where('pk_id_candidato', $id)->first();
        $dadosCandidato         = $candidatosModel->where('pk_id_candidato', $id)->first();
        $dadosCurso             = $cursosModel->where('pk_id_curso', $dadosAcademicos->fk_id_curso)->first();
        $dadosEditaisCandidatos = $editaisCandidatosModel->editaisPorIdCandidato($id);

        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'academicos'    => $dadosAcademicos,
            'dados'         => $dadosCandidato,
            'curso'         => $dadosCurso->pk_id_curso,
            'cursoNome'     => $dadosCurso->ds_nome_curso,
            'edital'        => $dadosEditaisCandidatos->fk_id_edital,
            'periodo'       => $dadosAcademicos->ds_periodo."º período",
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Convocar Candidatos",
        ];
        

        echo view('layoutDash', $parametros);
    }
    

    public function convocarCandidato(){
        $editaisCandidatosModel = new EditaisCandidatosModel();
        

        $curso      = $this->request->getPost('fk_id_curso');
        $candidato  = $this->request->getPost('fk_id_candidato');
        $edital     = $this->request->getPost('fk_id_edital');
        //veriricar se já existe uma convocação para esse candidato nesse edital
        $convocadosModel = new ConvocadosModel();
        $convocadosVerificados = $convocadosModel->where('fk_id_edital', $edital)
                        ->where('fk_id_candidato', $candidato)
                        ->where('ds_status', 3)
                        ->first();
        if($convocadosVerificados){
            //excluir a convocação anterior para criar uma nova
            $convocadosModel->where('pk_id_convocacao', $convocadosVerificados->pk_id_convocacao)->delete();
        }


        $dadosRegistro = array(
            "fk_id_edital"      =>  $edital,
            "fk_id_candidato"   =>  $candidato,
            "fk_id_curso"       =>  $curso,
            "ds_comparecimento" =>  0,
            "ds_status"         =>  1,
            "ds_interesse"      =>  0,
            "ds_data"           =>  date('Y-m-d'),
            "ds_hora"           =>  date('H:i:s'),
            "ds_mensagem"       =>  $this->request->getPost('ds_mensagem')
        );
         $convocacaoModel = new ConvocadosModel();
         //verificar se já existe uma convocação para esse candidato nesse edital
         $convocacaoExistente = $convocacaoModel->where('fk_id_edital', $edital)
                                                ->where('fk_id_candidato', $candidato)
                                                ->where('fk_id_curso', $curso)
                                                ->where('ds_status', 1)
                                                ->first();
            if($convocacaoExistente){
                return redirect()->route('Candidatos', [$edital, $curso])
                 ->with('mensagemError', 'Candidato já foi convocado para esse edital e curso');
            }

        if($convocacaoModel->insert($dadosRegistro)){
            $idConvocacao = $convocacaoModel->insertID();
            $servicesEmail = new EmailService(); 
            $servicesLogs = new LogsService();
            $dadosEmailCandidato = array(
                'email'		=>	$this->request->getPost('ds_email'),
                'nome'		=>	$this->request->getPost('ds_nome_candidato'),
                'assunto'	=>	'Convocação para o processo de estágio',
                'mensagem'	=>	$this->request->getPost('ds_mensagem'),
            );
        
            $servicesEmail->enviar($dadosEmailCandidato);


            $servicesLogs->inserirLog("Adicionou","Convocação do candidato ".$this->request->getPost('fk_id_candidato'));

            return redirect()->route('Candidatos', [$edital, $curso])
                 ->with('mensagemSuccess', 'Candidato covocado com sucesso');
                 
        }else{
            var_dump($contratosModel->errors());
        }

    }

    public function atualizarComparecimento(){
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $convocadosModel = new ConvocadosModel();

        $idCandidato    = $this->request->getPost('idCandidato');
        $comparecimento = $this->request->getPost('comparecimento');

        $dadosAtualizar = [
            'ds_comparecimento' => $comparecimento,
            'ds_interesse' => '1'
        ];

        $convocadosModel->where('fk_id_candidato', $idCandidato)->set($dadosAtualizar)->update();

        return $this->response->setJSON([
            csrf_token() => csrf_hash(), // novo token
            'status' => 'success',
            'message' => 'Comparecimento atualizado com sucesso.'
        ]);
    }
    public function verificarInteresse($convocados){
        $servicesLogs = new LogsService();
        //percorrer convocados para identificar se a data e hora da convocação já passaram 48 horas
        foreach($convocados as $convocado){
            $dataConvocacao = $convocado->ds_data;
            $horaConvocacao = $convocado->ds_hora;
            $dataHoraConvocacao = date_create($dataConvocacao . ' ' . $horaConvocacao);
            $dataHoraAtual = date_create(date('Y-m-d H:i:s'));
            $intervalo = date_diff($dataHoraConvocacao, $dataHoraAtual);
            $horas = ($intervalo->d * 24)+$intervalo->h;
            $minutos = $intervalo->i;
            $segundos = $intervalo->s;
            $totalHoras = $horas + ($minutos / 60) + ($segundos / 3600);
            
            if ($totalHoras > 48 && $convocado->ds_interesse == 0) {
                //service desconvocar
                $serviceDesconvocar = new ConvocacoesService();
                $desconvoca = $serviceDesconvocar->desconvocar($convocado->fk_id_candidato, $convocado->fk_id_edital, $convocado->fk_id_curso);
                //$desconvoca = $this->desconvocar($convocado->fk_id_candidato, $convocado->fk_id_edital, $convocado->fk_id_curso);
                if($desconvoca == true){
                    // registrar evento
                    $servicesLogs->inserirLog("Desconvocado","O candidato ".$convocado->fk_id_candidato." foi desconvocado automaticamente.");
                    //$this->registrarEvento('Desconvocação Automática', 'O candidato de ID '.$convocado->fk_id_candidato.' foi desconvocado automaticamente por não comparecer à convocação dentro do prazo de 48 horas.');
                }else{
                    // registrar evento de falha
                    $servicesLogs->inserirLog("Falha de Desconvocação","Falha ao desconvocar o candidato ".$convocado->fk_id_candidato.".");
                    
                }
            }
        }
        return true;
    }

        
}