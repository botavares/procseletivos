<?php
namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\DTOs\CandidatoDTO;
use App\Services\Base\SessaoService;
use App\Services\CandidatoService;
use App\Services\ClassificatorioService;
use App\Services\EditaisService;
use App\Services\ComprovanteService;
use App\Services\ProtocoloService;
use App\Services\GovBrService;


class Cadastros extends BaseController{
    protected CandidatoService $candidatoService;
    protected ClassificatorioService $classificatorioService;
    protected EditaisService $editaisService;
    protected ComprovanteService $comprovanteService;
    protected ProtocoloService $protocoloService;
    protected GovBrService $govBrService;
    protected SessaoService $sessaoService;
    
    public function __construct(){
        $this->candidatoService = service('candidatoService');
        $this->classificatorioService = service('classificatorioService');
        $this->editaisService = service('editaisService');
        $this->comprovanteService = service('comprovanteService');
        $this->protocoloService = service('protocoloService');
        $this->govBrService = service('govBrService');
        $this->sessaoService = service('sessao');
    }

    /**
     * Página inicial de cadastros
     */
    public function index($camada1 = '', $camada2 = 'pages', $page = 'OpcoesCadastro'){
        if (!is_file(APPPATH . "Views/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw PageNotFoundException::forPageNotFound();
        }

        $loginMode = env('LOGIN_MODE', 'govbr');
        if ($loginMode === 'local') {
            if (!checklogged()) {
                return $this->loginLocal();
            }
        }
        if (!checklogged()) {
            $urlToGov = "https://app.prefeituradivinopolis.com.br/app/7ddde5c6897f39b7b139238d0dd94d7f?destino=Cadastros";
            return redirect()->to($urlToGov);
        }
        $dataSession = $this->sessaoService->obterUsuario();
        
        $candidato = $this->candidatoService->buscarPorCpf($dataSession['cpf']);
        $idCandidato = $candidato->pk_id_cadastrado ?? null;    
        
        // Vincula gov_id se necessário
        if ($candidato && empty($candidato->fk_id_gov)) {
            $this->candidatoService->vincularGov($idCandidato, $dataSession['id']);
        }
        
        if($idCandidato != null){
            $protocolos = $this->protocoloService->buscarPorCandidato($idCandidato);    
        }else{
            $protocolos = [];
        }
        //Buscando todos cargos com editais Ativos
        $cargosAtivos = $this->editaisService->buscarEditaisAtivosCargos();

        /*Parametros para carga na página de Opções de Cadastro*/
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'status'        => $candidato ? 'registrado' : 'naoregistrado',
            'cargosAtivos'  => $cargosAtivos,
            'candidato'     => $idCandidato ?? null,
            'params'        => $dataSession,
            'protocolos'    => $protocolos,
            'titulo'        => ucfirst('Dados Pessoais e Acadêmicos'),
            'dataAtual'     => date('d/m/Y'),
        ];
        return view('layoutLogado', $parametros);
    }
    /**
     * Formulário de dados pessoais
     */
    public function dadosCandidato($camada1 = '', $camada2 = 'pages', $page = 'FormularioPessoais'){
        if (!checklogged()) {
            return redirect()->to('Home');
        }
        $dataSession = $this->sessaoService->obterUsuario();
        $candidato = $this->candidatoService->buscarPorCpf($dataSession['cpf']);
        
        $acao = $candidato ? 'update' : 'create';
        $id = $candidato ? $candidato->pk_id_cadastrado : null;
        $dados = $candidato ?? (object) [
            'pk_id_cadastrado' => null,
            'fk_id_gov' => $dataSession['id'],
            'ds_nome' => $dataSession['nome'],
            'ds_cpf' => $dataSession['cpf'],
            'ds_email' => $dataSession['email'],
        ];
        $deficiencias = $this->candidatoService->listarDeficiencias();
        $deficiencia = $this->candidatoService->buscarDeficiencia($candidato->fk_id_pne ?? null);
        $parametros = [
            'camada1' => $camada1,
            'camada2' => $camada2,
            'pagina' => $page,
            'params' => $dataSession,
            'acao' => $acao,
            'dados' => $dados,
            'idCandidato' => $id,
            'titulo' => ucfirst('Registrar seus dados pessoais'),
            'dataAtual' => date('d/m/Y'),
            'saudacao' => $this->getSaudacao(),
            'bairro' => $this->formatarBairro($candidato),
            'deficiencias' => $deficiencias,
            'deficiencia' => $deficiencia,
        ];
        return view('layoutLogado', $parametros);
    }
    /**
     * Formulário classificatório
     */
    public function dadosClassificatorios($edital, $cargo, $id, $camada1 = '', $camada2 = 'pages', $page = 'FormularioClassificatorio'){
        
        $editalAtivo = $this->editaisService->estaAtivo($edital);
        if($editalAtivo !== true){
            return redirect()->route('Cadastros')->with('mensagemError', $editalAtivo['mensagemError']);
        }
        

        if (!checklogged()) {
            return redirect()->to('Home');
        }
        $dataSession = $this->sessaoService->obterUsuario();
        $dadosCargo = $this->classificatorioService->buscarDadosCargo($cargo);
        $requisitos = $this->classificatorioService->buscarRequisitos($cargo);
        $cadastrados = $this->classificatorioService->buscarDadosCadastrados($id, $cargo, $edital);
        
        $parametros = [
            'camada1' => $camada1,
            'camada2' => $camada2,
            'pagina' => $page,
            'params' => $dataSession,
            
            
            // Dados classificatórios completos para construção do formulário 
            'experienciasClassificatorias'          => $requisitos['experienciasClassificatorias'],
            'escolaridadesClassificatorias'         => $requisitos['escolaridadesClassificatorias'],
            'aperfeicoamentoClassificatorios'       => $requisitos['aperfeicoamentosClassificatorios'],
            'criteriosAdicionaisClassificatorios'   => $requisitos['criteriosAdicionaisClassificatorios'],
            // Arrays completos dos dados já salvos (para preencher inputs quando carrega dados cadastrados do candidato)
            'dadosEscolaridadeIndexado'         => $cadastrados['escolaridadesIndexadas'],
            'dadosAperfeicoamentoIndexado'      => $cadastrados['aperfeicoamentosIndexados'],
            'dadosCriterioAdicionalIndexado'    => $cadastrados['criteriosAdicionaisIndexados'],
            'dadosCriterioAdicional'            => $cadastrados['criteriosAdicionais'],
            'dadosExperiencia'                  => $cadastrados['experiencias'],
            'experienciasSalvas'                => $cadastrados['experienciasSalvas'],
            // IDs para checkboxes
            'idsEscolaridadesCandidato'         => $cadastrados['idsEscolaridades'],
            'idsAperfeicoamentosCandidato'      => $cadastrados['idsAperfeicoamentos'],
            'idsCriteriosAdicionaisCandidato'   => $cadastrados['idsCriteriosAdicionais'],
            // Dados do cargo, edital e candidato
            'idCandidato'   => $id,
            'idCargo'       => $cargo,
            'idEdital'      => $edital,
            'titulo'        => ucfirst('Registrar seus dados acadêmicos e profissionais'),
            'dataAtual'     => date('d/m/Y'),
            'saudacao'      => $this->getSaudacao(),
            'cargos'        => $dadosCargo,
        ];
        return view('layoutLogado', $parametros);
    }
    /**
     * Salva dados pessoais
     */
    public function registrarDadosPessoais(){
        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }
        $dto = CandidatoDTO::fromArray($this->request->getPost());
        $resultado = $this->candidatoService->salvar($dto, $this->request->getPost('acao'), $this->request);
        if ($resultado['sucesso']) {
            $mensagem = $resultado['acao'] === 'create' 
                ? 'Registro gravado com sucesso' 
                : 'Registro alterado com sucesso';
            return redirect()->route('Cadastros')->with('mensagemSuccess', $mensagem);
        }
        
        return redirect()->route('dadosPessoais', [$dto->fkIdGov])
            ->withInput()
            ->with('errors', $resultado['erro'] ?? 'Erro ao salvar');
    }
    /**
     * Salva dados classificatórios
     */
    public function registrarDadosClassificatorios(){
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('Home');
        }
        $post = $this->request->getPost();
        
        $cargoId = $post['idCargo'] ?? null;
        $editalId = $post['idEdital'] ?? null;
        $candidatoId = $post['idCandidato'] ?? null;
        
        // Processa classificatório
        $this->classificatorioService->processarFormulario($post, $candidatoId, $cargoId, $editalId);
        
        // Gera/Atualiza protocolo 
        $protocoloDto = $this->protocoloService->buscarOuGerar(
            $candidatoId, 
            $cargoId, 
            $editalId
        );
        
        $this->protocoloService->salvar($protocoloDto);
        
        // Redireciona para tela de sucesso
        return redirect()->route('sucessoClassificatorio', [$candidatoId, $cargoId, $editalId])
            ->with('mensagemSuccess', 'Registro atualizado com sucesso!');
    }
    /**
     * Tela de sucesso após salvar dados classificatórios
     * Permite impressão do comprovante
     */
    public function sucessoClassificatorio($idCandidato, $idCargo, $idEdital, $camada1 = '', $camada2 = 'pages', $page = 'SucessoClassificatorio')
    {
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'idCandidato'   => $idCandidato,
            'idCargo'       => $idCargo,
            'idEdital'      => $idEdital,
            'titulo'        => ucfirst('Registro concluído!'),
            'dataAtual'     => date('d/m/Y'),
        ];
        
        return view('layoutLogado', $parametros);
    }
    /**
     * Gera PDF do comprovante
     */
    /**
     * Método alternativo que salva o PDF no servidor e redireciona
     * para evitar problemas de buffer no Firefox
     */
    public function gerarComprovante($idEdital, $idCargo, $idCandidato){
        
        $comprovante = $this->comprovanteService->gerarComprovanteCompleto($idEdital, $idCargo, $idCandidato);
        
        if ($comprovante->erro !== null) {
            return redirect()->route('home')->with('mensagemError', $comprovante->erro);
        }

        if (!$comprovante->conteudo) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON(['error' => 'Erro ao gerar PDF']);
        }

        return $this->response
            ->setHeader('Content-Type', $comprovante->tipoMime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $comprovante->nomeArquivo . '"')
            ->setHeader('Content-coding', 'none')
            ->setBody($comprovante->conteudo);

    }

    
    /**
     * Callback de autenticação Gov.BR
     */
    public function loginGovBr()
    {
        $user = $this->request->getVar('user');
        $destino = $this->request->getVar('destino') ?? 'Home';
        if (empty($user)) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['error' => 'Parâmetro user é obrigatório']);
        }
        $resultado = $this->govBrService->autenticar($user);
        if (!$resultado['sucesso']) {
            log_message('error', 'Gov.BR Auth Error: ' . $resultado['erro']);
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['error' => 'Falha na autenticação', 'details' => $resultado['erro']]);
        }
        $dataSession = $this->govBrService->prepararDadosSessao(
            $resultado['dados'], 
            $user
        );
        
        session()->set($dataSession);
        log_message('info', "Usuário {$dataSession['nome']} ({$dataSession['cpf']}) autenticado via Gov.BR");
        return redirect()->to($destino);
    }
    public function loginLocal(){
        $dataSession = [
            'su'        => '1234567',
            'id'        => '69',
            'email'     => 'breno.o.tavares@gmail.com',
            'nome'      => 'Breno Oliveira Tavares',
            'cpf'       => '03455783686',
            'logged_in' => true
        ];

        $this->sessaoService->definirUsuario($dataSession);

        $destino = "Cadastros";//$this->request->getVar('destino');
        if ($destino) {
            return redirect()->to(base_url($destino));
        }

        return redirect()->to(base_url());
    }
    /**
     * Logout
     */
    public function logOut()
    {
        $su = session()->get('su');
        session()->destroy();
        
        log_message('info', "Logout realizado para sessão: {$su}");
        
        return redirect()->route('home');
    }
    // Métodos auxiliares privados
    private function getSaudacao(): string
    {
        $hora = date('H');
        if ($hora >= 5 && $hora < 12) return 'Bom dia';
        if ($hora >= 12 && $hora < 18) return 'Boa tarde';
        return 'Boa noite';
    }
    private function formatarBairro(?object $candidato): object
    {
        return (object) [
            'ds_nome_bairro' => $candidato->ds_nome_bairro ?? '',
            'ds_uf' => $candidato->ds_uf ?? '',
            'ds_cidade' => $candidato->ds_cidade ?? '',
        ];
    }
    
}