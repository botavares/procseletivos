<?php
namespace App\Controllers;

use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\DadosContratosModel;
use App\Models\CandidatosModel;
use App\Models\AcademicosModel;
use App\Models\CursosModel;
use App\Models\AbrangenciasModel;
use App\Models\SetoresModel;
use App\Models\VagasModel;
use App\Models\DadosRescisaoModel;
use App\Models\DadosAditivosModel;
use App\Models\ConvocadosModel;
use App\Models\AuxiliosModel;
use App\Models\InstituicoesModel;
use App\Models\SegurosModel;
use App\Models\DadosPrefeituraModel;
use App\Models\VerificadorModel;
use App\Models\MotivosRescisaoModel;

use App\Services\Relatorios\RelatoriosService;
use App\Services\Relatorios\RelatoriosFormService;

use App\Services\EmailService;
use App\Services\LogsService;
use App\Services\ContagemDeTempoService;

use Dompdf\Dompdf;
use Dompdf\Options;

class Relatorios extends BaseController{

    /*==================================================================================
    * RENDERIZAÇÃO DE FORMULÁRIOS
    *==================================================================================*/
    private function renderFormulario(array $params){
        $this->validarSessao();
        $this->validarView($params['camada1'],$params['camada2'],$params['pagina']);
        
        /*Aqui eu estou isolando a ação de imprimir porque ela é a única que vem de um formService Diferente que nao seja o
        RelatoriosFormService. Se a ação não for imprimir eu uso o RelatoriosFormService como default.
        */
        $formService = match ($params['acao']){
            'planilha' => new PlanilhaFormService($this->request),
            default => new RelatoriosFormService($this->request), 
        };

        //Daqui pra baixo o $formService assume o RelatoriosFormService ou o PlanilhaFormService dependendo da ação
        $dadosFormulario = match ($params['acao']){
            'candidatosPorCurso' =>$formService->candidatosPorCurso(),
            'candidatosPorAbrangencia' =>$formService->candidatosPorAbrangencia(),
            'contratosPorSetor' =>$formService->contratosPorSetor(),
            
            default => throw new \InvalidArgumentException('Ação inválida'),
        };

        return view('layoutDash', array_merge($params, $dadosFormulario));
    }
    /*==================================================================================
    * FORMULÁRIO DE CANDIDATOS POR CURSO
    *==================================================================================*/
    public function formCandidatosPorCurso($camada1 = 'pages',$camada2 = 'relatorios',$page = 'formCandidatosPorCurso') {
        return $this->renderFormulario([
            'acao'      => 'candidatosPorCurso',
            'urlVoltar' => ($this->request->getGet('voltar')) ?: site_url('Dashboard'),
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'    => $page,
        ]);
    }
    /*==================================================================================
    * FORMULÁRIO DE CANDIDATOS POR ABRANGENCIA
    *==================================================================================*/
    public function formCandidatosPorAbrangencia($camada1 = 'pages',$camada2 = 'relatorios',$page = 'formCandidatosPorAbrangencia') {
        return $this->renderFormulario([
            'acao'      => 'candidatosPorAbrangencia',
            'urlVoltar' => ($this->request->getGet('voltar')) ?: site_url('Dashboard'),
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'    => $page,
        ]);
    }
    /*==================================================================================
    * RELATORIOS DE CANDIDATOS POR CURSO
    *==================================================================================*/
    public function relatorioCandidatosPorCurso(int $idCurso = null){
        $service = new RelatoriosService(
            new CandidatosModel(),
            new AcademicosModel(),
            new CursosModel(),
            new AbrangenciasModel()
        );
        $dados =  $service->relatorioCandidatosPorCurso($idCurso ?? $this->request->getPost('pk_id_curso'));
        $dompdf = new Dompdf();
        imprimir($dompdf, 'CandidatosPorCurso', $dados);
    }
    /*==================================================================================
    * RELATORIOS DE CANDIDATOS POR ABRANGENCIA
    *==================================================================================*/
    public function relatorioCandidatosPorAbrangencia(int $idAbrangencia = null){
        $service = new RelatoriosService(
            new CandidatosModel(),
            new AcademicosModel(),
            new CursosModel(),
            new AbrangenciasModel()
        );
        $dados =  $service->relatorioCandidatosPorAbrangencia($idAbrangencia ?? $this->request->getPost('pk_id_abrangencia'));
        $dompdf = new Dompdf();
        imprimir($dompdf, 'CandidatosPorAbrangencia', $dados);
    }

    
  /* =====================================================
     * VALIDAÇÕES AUXILIARES
     * ===================================================== */

    private function validarSessao(): void{
        if (!checklogged()) {
            redirect()->route('home')->with('error', 'Sua sessão expirou')->send();
            exit;
        }
    }

    private function validarView(string $camada1, string $camada2, string $page): void{
        if (!is_file(APPPATH . "Views/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw new PageNotFoundException("Página não encontrada: {$page}");
        }
    }
}