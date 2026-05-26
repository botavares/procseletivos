<?php
namespace App\Controllers;

use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\DadosContratosModel;
use App\Models\CandidatosModel;
use App\Models\AcademicosModel;
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


use App\Services\Contratos\ContratoFormService;
use App\Services\Contratos\ContratoService;
use App\Services\Contratos\ContratoGridService;
use App\Services\Contratos\ContratoComunicacaoFormService;
use App\Services\Contratos\ContratoComunicacaoService;
use App\Services\Contratos\ContratoDocumentoService;

use App\Services\EmailService;
use App\Services\LogsService;
use App\Services\ContagemDeTempoService;

use Dompdf\Dompdf;
use Dompdf\Options;

class Contratos extends BaseController{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Contratados'){
        return $this->renderGrid('ativos',$camada1,$camada2,$page);
    }
    public function contratosExpirando($camada1 = '', $camada2 = 'pages', $page = 'Expirando'){
        return $this->renderGrid('expirando',$camada1,$camada2,$page);
    }

    private function renderGrid(string $tipo, $camada1, $camada2, $page){
        $this->validarView($camada1, $camada2, $page);
        $this->validarSessao();

        $gridServiceContrato = new ContratoGridService();

        $grid = match ($tipo) {
            'ativos'     => $gridServiceContrato->ativos(),
            'expirando'  => $gridServiceContrato->expirando(32),
            default      => throw new \InvalidArgumentException('Tipo de grid inválido'),
        };
        return view('layoutDash', [
            'camada1'           => $camada1,
            'camada2'           => $camada2,
            'pagina'            => $page,
            'dados'             => $grid['dados'],
            'titulosTabela'     => $grid['columns'],
            'titulo'            => 'Gerenciar Contratos',
            'user'              => session('nome'),
            'diasParaEncerrar'  => 32,
            'contratosExpirando' => $grid['contratosExpirando'],
        ]);
    }

    public function formComunicar(int $idCandidato,int $setor,$camada1 = 'pages',$camada2 = 'cadastros',$page = 'formComunicar') {
        return $this->renderFormulario([
            'idCandidato' => $idCandidato,
            'setor'     => $setor,
            'acao'      => 'comunicar',
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'      => $page,
        ]);
    }

    public function formContratar(int $idCandidato,$camada1 = 'pages',$camada2 = 'cadastros',$page = 'formContratar') {
        return $this->renderFormulario([
            'idCandidato' => $idCandidato,
            'acao'      => 'contratar',
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'      => $page,
        ]);
    }
    public function formAlterarContrato(int $idContrato,$camada1 = 'pages',$camada2 = 'alteracoes',$page = 'formAlterarContratos') {
        return $this->renderFormulario([
            'idContrato' => $idContrato,
            'acao'      => 'alterar',
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'      => $page,
        ]);
    }

    public function formAditivoRescindir(int $idContrato,$camada1 = 'pages',$camada2 = 'alteracoes',$page = 'formAditivoRescindir') {
        return $this->renderFormulario([
            'idContrato' => $idContrato,
            'acao'      => 'aditivo_rescindir',
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'      => $page,
        ]);
    }
    


    private function renderFormulario(array $params){
        $this->validarSessao();
        $this->validarView($params['camada1'],$params['camada2'],$params['pagina']);
        
        /*Aqui eu estou isolando a ação de comunicar porque ela é a única que vem de um formService Diferente que nao seja o
        ContratoFormService. Se a ação não for comunicar eu uso o ContratoFormService como default.
        */
        $formService = match ($params['acao']){
            'comunicar' => new ContratoComunicacaoFormService($this->request),
            default => new ContratoFormService($this->request), 
        };

        //Daqui pra baixo o $formService assume o ContratoFormService ou o ContratoComunicacaoFormService dependendo da ação
        $dadosFormulario = match ($params['acao']){
            'contratar' =>$formService->prepararFormularioNovo($params['idCandidato']),

            'alterar'   =>$formService->prepararFormularioAlteracao($params['idContrato']),

            'aditivo_rescindir' => $formService->formAditivoRescindir($params['idContrato']),

            'comunicar' => $formService->prepararFormulario($params['idCandidato'],$params['setor']),

            default => throw new \InvalidArgumentException('Ação inválida'),
        };

        return view('layoutDash', array_merge($params, $dadosFormulario));
    }


    public function comunicar(){
        $formService = new ContratoComunicacaoFormService($this->request);

        $service = new ContratoComunicacaoService(new EmailService(),new LogsService(),new ContagemDeTempoService());

        $service->comunicarContratoExpirando((int) $this->request->getPost('pk_id_contrato'),$formService->dadosComunicacao());

        return redirect()
            ->route('Contratos.contratosExpirando')
            ->with('mensagemSuccess', 'Comunicação realizada com sucesso');
    }

    

    public function registrar(){
        try{
            $this->validarSessao();
            $formService = new ContratoFormService($this->request);
            $dados = $formService->dadosContrato();

            $service = new ContratoService(
                new DadosContratosModel(),
                new VagasModel(),
                new LogsService(),
                new DadosRescisaoModel(),
                new DadosAditivosModel(),
                new ConvocadosModel(),
                new VerificadorModel()
                );
            
            $idContrato = $service->contratar($dados);
            if(!$idContrato){
                throw new \RuntimeException('Falha ao criar contrato');
            }
            return redirect()->to("Contratos/imprimirContrato/{$idContrato}");
            return redirect()
            ->route('Contratos')
            ->with('mensagemSuccess', 'Contrato cadastrado com sucesso');
        }catch(\Exception $e){
            return redirect()
            ->route('Contratos')
            ->with('mensagemError', 'Falha ao criar contrato');
            //dd($e->getMessage(), $e->getFile(), $e->getLine() );
          /* return redirect()
                ->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);*/
        }
    }

    public function alterar(){
        try {
            $formService  = new ContratoFormService($this->request);
            $dados = $formService->dadosAlterarContrato();

            (new ContratoService(
                 new DadosContratosModel(),
                new VagasModel(),
                new LogsService(),
                new DadosRescisaoModel(),
                new DadosAditivosModel(),
                new ConvocadosModel(),
                new VerificadorModel()
                ))->alterar($dados);

            return redirect()
                ->route('Contratos')
                ->with('mensagemSuccess', 'Contrato alterado com sucesso');

        } catch (\Throwable $e) {
            //dd($e->getMessage(), $e->getFile(), $e->getLine() );
             return redirect()
            ->route('Contratos')
            ->with('mensagemError', ['erro' => $e->getMessage()]);
            
        }
    }

    public function aditivarRescindir()
    {
        try {
            $formService  = new ContratoFormService($this->request);
            $dados = $formService->dadosAditivoRescindir();

            if($dados['modo'] == 0){ //modo = 0 -> rescindir
                $rescindir = (new ContratoService(
                    new DadosContratosModel(),
                    new VagasModel(),
                    new LogsService(),
                    new DadosRescisaoModel(),
                    new DadosAditivosModel(),
                    new ConvocadosModel(),
                    new VerificadorModel()))->rescindir($dados);
                if($rescindir == true){
                    //imprimir rescisao

                    return redirect()->to("Contratos/imprimirRescisao/{$dados['idContrato']}");
                }else{
                    return redirect()->route('Contratos', [$dados['idContrato']])->with('mensagemError', 'Não foi gerado a rescisão');
                }
            }else{ //modo = 1 -> aditivar
                $aditivar = (new ContratoService(new DadosContratosModel(),
                    new VagasModel(),
                    new LogsService(),
                    new DadosRescisaoModel(),
                    new DadosAditivosModel(),
                    new ConvocadosModel(),
                    new VerificadorModel()))->aditivar($dados);
                if($aditivar == true){
                    //imprimir aditivo
                     return redirect()->to("Contratos/imprimirAditivo/{$dados['idContrato']}");
                    
                }else{
                    return redirect()->route('Contratos', [$dados['idContrato']])->with('mensagemError', 'Não foi gerado o aditivo');
                }
            }
            

            return redirect()
            ->route('Contratos')
            ->with('mensagemSuccess', 'Operação realizada com sucesso');

        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine() );
            /* return redirect()
            ->route('Contratos')
            ->with('mensagemError', ['erro' => $e->getMessage()]);*/
           
        }
    }

    /*=======================================================
    * IMPRESSÕES
    *=======================================================*/
    public function imprimirContrato(int $id){
        $service = new ContratoDocumentoService(
            new DadosContratosModel(),
            new AuxiliosModel(),
            new InstituicoesModel(),
            new SegurosModel(),
            new DadosPrefeituraModel(),
            new CandidatosModel(),
            new AcademicosModel(),
            new SetoresModel(),
            new DadosAditivosModel(),
            new DadosRescisaoModel(),
            new MotivosRescisaoModel()
        );

        $dados = $service->montarContrato($id);

        if (!$dados) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }


        $dompdf = new Dompdf();
        imprimir($dompdf, 'contrato', $dados);
}

    public function imprimirAditivo(int $id){
        $service = new ContratoDocumentoService(
            new DadosContratosModel(),
            new AuxiliosModel(),
            new InstituicoesModel(),
            new SegurosModel(),
            new DadosPrefeituraModel(),
            new CandidatosModel(),
            new AcademicosModel(),
            new SetoresModel(),
            new DadosAditivosModel(),
            new DadosRescisaoModel(),
            new MotivosRescisaoModel()
        );

        $dados = $service->imprimirAditivo($id);

        $dompdf = new Dompdf();
        imprimir($dompdf, 'aditivo', $dados);
    
        }

    public function imprimirRescisao(int $id){
        $service = new ContratoDocumentoService(
             new DadosContratosModel(),
            new AuxiliosModel(),
            new InstituicoesModel(),
            new SegurosModel(),
            new DadosPrefeituraModel(),
            new CandidatosModel(),
            new AcademicosModel(),
            new SetoresModel(),
            new DadosAditivosModel(),
            new DadosRescisaoModel(),
            new MotivosRescisaoModel()
        );

        $dados = $service->imprimirRescisao($id);

        $dompdf = new Dompdf();
        imprimir($dompdf, 'rescisao', $dados);
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
