<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;

use App\Services\Relatorios\RelatoriosService;
use App\Services\Relatorios\RelatoriosFormService;

class Relatorios extends BaseController{

    /*==================================================================================
    * RENDERIZAÇÃO DE FORMULÁRIOS
    *==================================================================================*/
    private function renderFormulario(array $params){
        $this->validarSessao();
        $this->validarView($params['camada1'],$params['camada2'],$params['pagina']);
        
        $formService = new RelatoriosFormService($this->request);

        $dadosFormulario = match ($params['acao']){
            'candidatosPorCurso' =>$formService->candidatosPorCurso(),
            'candidatosPorAbrangencia' =>$formService->candidatosPorAbrangencia(),
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
        $dados = service('relatoriosService')->relatorioCandidatosPorCurso($idCurso ?? $this->request->getPost('pk_id_curso'));
        service('pdfExportService')->gerarPdf('CandidatosPorCurso', $dados, 'CandidatosPorCurso');
    }
    /*==================================================================================
    * RELATORIOS DE CANDIDATOS POR ABRANGENCIA
    *==================================================================================*/
    public function relatorioCandidatosPorAbrangencia(int $idAbrangencia = null){
        $dados = service('relatoriosService')->relatorioCandidatosPorAbrangencia($idAbrangencia ?? $this->request->getPost('pk_id_abrangencia'));
        service('pdfExportService')->gerarPdf('CandidatosPorAbrangencia', $dados, 'CandidatosPorAbrangencia');
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