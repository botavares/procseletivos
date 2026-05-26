<?php
namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\DTOs\CandidatoDTO;
use App\Services\CandidatoService;
use App\Services\ClassificatorioService;
use App\Services\ProtocoloService;
use App\Services\PdfService;
use App\Services\GovBrService;


class Cadastros extends BaseController
{
    protected CandidatoService $candidatoService;
    protected ClassificatorioService $classificatorioService;
    protected ProtocoloService $protocoloService;
    protected PdfService $pdfService;
    protected GovBrService $govBrService;
    public function __construct()
    {
        $this->candidatoService = new CandidatoService();
        $this->classificatorioService = new ClassificatorioService();
        $this->protocoloService = new ProtocoloService();
        $this->pdfService = new PdfService();
        $this->govBrService = new GovBrService();
    }
    /**
     * Página inicial de cadastros
     */
    public function index($camada1 = '', $camada2 = 'pages', $page = 'OpcoesCadastro')
    {
        if (!is_file(APPPATH . "Views/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw PageNotFoundException::forPageNotFound();
        }
        if (!checklogged()) {
            $urlToGov = "https://app.prefeituradivinopolis.com.br/app/7ddde5c6897f39b7b139238d0dd94d7f?destino=Cadastros";
            return redirect()->to($urlToGov);
        }
        $dataSession = $_SESSION;
        
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
        $editais = (new \App\Models\EditaisCargosModel())->getEditaisAtivosCargos();
        
        

        $parametros = [
            'camada1' => $camada1,
            'camada2' => $camada2,
            'pagina' => $page,
            'status' => $candidato ? 'registrado' : 'naoregistrado',
            'editais' => $editais,
            'candidato' => $idCandidato ?? null,
            'params' => $dataSession,
            'protocolos' => $protocolos,
            'titulo' => ucfirst('Dados Pessoais e Acadêmicos'),
            'dataAtual' => date('d/m/Y'),
        ];
        return view('layoutLogado', $parametros);
    }
    /**
     * Formulário de dados pessoais
     */
    public function dadosCandidato($camada1 = '', $camada2 = 'pages', $page = 'FormularioPessoais')
    {
        if (!checklogged()) {
            return redirect()->to('Home');
        }
        $dataSession = $_SESSION;
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
        
        $editalModel = new \App\Models\EditaisModel();
        $dadosEdital = $editalModel->where('pk_id_edital', $edital)->first($edital);
        $dataInicialEdital = date('d/m/Y', strtotime($dadosEdital->ds_data_inicial));
        $dataFinalEdital = date('d/m/Y', strtotime($dadosEdital->ds_data_termino));
        //se hoje não estiver entre a data inicial e final do edital, redireciona para a página de opções de cadastro
        $hoje = date('Y-m-d');
        if ($hoje < $dadosEdital->ds_data_inicial || $hoje > $dadosEdital->ds_data_termino) {
            return redirect()->route('Cadastros')->with('mensagemError', "O período de cadastro para este edital é de {$dataInicialEdital} a {$dataFinalEdital}.");
        }

        if (!checklogged()) {
            return redirect()->to('Home');
        }
        $dataSession = $_SESSION;
        $dadosCargo = $this->classificatorioService->buscarDadosCargo($cargo);
        $requisitos = $this->classificatorioService->buscarRequisitos($cargo);
        $experiencias = $this->classificatorioService->buscarExperiencias($edital, $cargo);
        $cadastrados = $this->classificatorioService->buscarDadosCadastrados($id, $cargo, $edital);

        $escolaridadesIndexadas = [];

        if (!empty($cadastrados['escolaridades'])) {
            foreach ($cadastrados['escolaridades'] as $esc) {
                $escolaridadesIndexadas[$esc->fk_id_escolaridade] = $esc->ds_quantidade;
            }
        }

        $aperfeicoamentosIndexados = [];
        if (!empty($cadastrados['aperfeicoamentos'])) {
            foreach ($cadastrados['aperfeicoamentos'] as $ap) {
                $aperfeicoamentosIndexados[$ap->fk_id_curso] = $ap->ds_quantidade ?? 1;
            }
        }
        
        $parametros = [
            'camada1' => $camada1,
            'camada2' => $camada2,
            'pagina' => $page,
            'params' => $dataSession,
            'experienciaProfissional' => $experiencias,
            'experienciasSalvas' => $cadastrados['experienciasSalvas'],
            // Dados classificatórios completos
            'escolaridadesClassificatorias' => $requisitos['escolaridadesClassificatorias'],
            'aperfeicoamentoClassificatorios' => $requisitos['aperfeicoamentosClassificatorios'],
            // Arrays completos dos dados já salvos (para preencher inputs)
            'dadosEscolaridade' => $escolaridadesIndexadas,
            'dadosAperfeicoamento' => $aperfeicoamentosIndexados,
            'dadosExperiencia' => $cadastrados['experiencias'],
            // IDs para checkboxes
            'idsEscolaridadesCandidato' => $cadastrados['idsEscolaridades'],
            'idsAperfeicoamentosCandidato' => $cadastrados['idsAperfeicoamentos'],
            'idCandidato' => $id,
            'idCargo' => $cargo,
            'idEdital' => $edital,
            'titulo' => ucfirst('Registrar seus dados acadêmicos'),
            'dataAtual' => date('d/m/Y'),
            'saudacao' => $this->getSaudacao(),
            'cargos' => $dadosCargo,
        ];
        return view('layoutLogado', $parametros);
    }
    /**
     * Salva dados pessoais
     */
    public function registrarDadosPessosais(){
        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }
        $dto = CandidatoDTO::fromArray($this->request->getPost());
        $resultado = $this->candidatoService->salvar($dto, $this->request->getPost('acao'));
        if ($resultado['sucesso']) {
            $mensagem = $resultado['acao'] === 'create' 
                ? 'Registro gravado com sucesso' 
                : 'Registro alterado com sucesso';
            return redirect()->route('dadosClassificatorios',[
                23,
                16,
                $resultado['id']
            ])->with('mensagemSuccess', $mensagem);
        }
        
        return redirect()->route('dadosPessoais', [$dto->fkIdGov])
            ->withInput()
            ->with('errors', $resultado['erro'] ?? 'Erro ao salvar');
    }
    /**
     * Salva dados classificatórios
     */
    public function registrarDadosClassificatorios()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('Home');
        }
        $post = $this->request->getPost();
        $cargoId = $post['idCargo'];
        $editalId = $post['idEdital'];
        $candidatoId = $post['idCandidato'];
        // Processa classificatório
        $this->classificatorioService->processarFormulario($post, $candidatoId, $cargoId, $editalId);
        // Gera/Atualiza protocolo
        $candidato = $this->candidatoService->buscarPorId($candidatoId);
        $dadosCargo = $this->classificatorioService->buscarDadosCargo($cargoId);
        
        $protocoloDto = $this->protocoloService->buscarOuGerar(
            $candidatoId, 
            $cargoId, 
            $editalId, 
            $dadosCargo->fk_id_secretaria ?? null
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
        $modelCargos            = new \App\Models\CargosModel();
        $modelCandidatos        = new \App\Models\CandidatosModel();
        $modelProtocolos        = new \App\Models\ProtocolosModel();
        $modelEditais            = new \App\Models\EditaisModel();

        $dadosCandidatos    = $modelCandidatos->where('pk_id_cadastrado', $idCandidato)->first();
        $nomeCargo          = $modelCargos->where('pk_id_cargo', $idCargo)->select('ds_nome_cargo')->first();
        $dadosEdital        = $modelEditais->where('pk_id_edital', $idEdital)->first();
        $dadosProtocolos    = $modelProtocolos->where('fk_id_cadastrado', $idCandidato)
                                              -> where('fk_id_cargo', $idCargo)
                                              -> where('fk_id_edital', $idEdital)
                                              ->first();

        if(!$dadosProtocolos){
             return redirect()->route('home')->with('mensagemError', 'Protocolo não encontrado para este candidato, cargo e edital.');

        }else{
            // Formata o número do edital com a máscara 012026 para 01/2026
            $ano = substr($dadosEdital->ds_numero_edital, -4);            // últimos 4 dígitos → ano
            $numero = substr($dadosEdital->ds_numero_edital, 0, -4);      // o que sobra → número do edital
            // remove zeros à esquerda
            $numero = ltrim($numero, "0");
            $numeroEdital = "Edital " .$numero . '/' . $ano;

        }

		$def = $this->pdfService->formatarDeficiencia(
            $dadosCandidatos->fk_id_pne,
            $dadosCandidatos->ds_outra_pne
        );

        
        $dados = array(
            'brasao'            =>  imageToBase64(ROOTPATH . '/external/img/brasao.png'),
			'fundo'		        =>	imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'edital'            =>  $numeroEdital,
            'dadosPessoais'     =>  $dadosCandidatos,
            'protocolo'         =>  $dadosProtocolos->ds_protocolo,
            'nascimento'        =>  date('d/m/Y', strtotime($dadosCandidatos->ds_nascimento)),
            'nomeCargo'         =>  $nomeCargo->ds_nome_cargo,
            'deficiencia'       =>  $def,
            'dataCadastro'      =>  date('d/m/Y', strtotime($dadosCandidatos->ds_data_cadastro)),
            'horaCadastro'      =>  date('H:i:s', strtotime($dadosCandidatos->ds_hora_cadastro)),
            //'ip'                =>  $dadosCandidatos->pk_id_cadastrado,
        );
        $pdfContent = $this->pdfService->gerarComprovante($dados);

        if (!$pdfContent) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON(['error' => 'Erro ao gerar PDF']);
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="comprovante_candidato_'.$idCandidato.'.pdf"')
            ->setHeader('Content-coding', 'none')
            ->setBody($pdfContent);
    

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
    private function formatarNumeroEdital(string $numero): string
    {
        $ano = substr($numero, -4);
        $num = ltrim(substr($numero, 0, -4), "0");
        return "Edital {$num}/{$ano}";
    }
}