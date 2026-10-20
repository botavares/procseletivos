<?php
namespace App\Controllers\Publica;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\DTOs\CandidatoDTO;
use App\Exceptions\Domain\EditalForaDoPrazoException;
use App\Services\Base\SessaoService;
use App\Services\CandidatoService;
use App\Services\ClassificatorioService;
use App\Services\EditaisService;
use App\Services\ComprovanteService;
use App\Services\ProtocoloService;
use App\Services\GovBrService;
use App\Services\CandidatoRecursoService;

class Cadastros extends BaseController
{
    protected CandidatoService        $candidatoService;
    protected ClassificatorioService  $classificatorioService;
    protected EditaisService          $editaisService;
    protected ComprovanteService      $comprovanteService;
    protected ProtocoloService        $protocoloService;
    protected GovBrService            $govBrService;
    protected SessaoService           $sessaoService;
    protected CandidatoRecursoService $candidatoRecursoService;

    public function __construct()
    {
        $this->candidatoService        = service('candidatoService');
        $this->classificatorioService  = service('classificatorioService');
        $this->editaisService          = service('editaisService');
        $this->comprovanteService       = service('comprovanteService');
        $this->protocoloService        = service('protocoloService');
        $this->govBrService            = service('govBrService');
        $this->sessaoService            = service('sessao');
        $this->candidatoRecursoService  = service('candidatoRecursoService');
    }

    /* ---------- Métodos auxiliares privados ---------- */

    private function saudacao(): string
    {
        $hora = (int) date('H');
        return match (true) {
            $hora >= 5 && $hora < 12 => 'Bom dia',
            $hora >= 12 && $hora < 18 => 'Boa tarde',
            default                    => 'Boa noite',
        };
    }

    private function bairroFormatado(?object $c): object
    {
        return (object) [
            'ds_nome_bairro' => $c?->ds_nome_bairro ?? '',
            'ds_uf'          => $c?->ds_uf ?? '',
            'ds_cidade'      => $c?->ds_cidade ?? '',
        ];
    }

    /* ---------- Ações ---------- */

    public function index($camada1 = '', $camada2 = 'pages', $page = 'OpcoesCadastro')
    {
        if (!is_file(APPPATH . "Views/Publica/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (!checklogged()) {
            return redirect()->to(
                'https://app.prefeituradivinopolis.com.br/app/7ddde5c6897f39b7b139238d0dd94d7f?destino=Cadastros'
            );
        }

        $dataSession = $this->sessaoService->obterUsuario();
        $candidato   = $this->candidatoService->buscarPorCpf($dataSession['cpf']);
        $idCandidato = $candidato->pk_id_cadastrado ?? null;

        if ($candidato && empty($candidato->fk_id_gov)) {
            $this->candidatoService->vincularGov($idCandidato, $dataSession['id']);
        }

        if ($idCandidato !== null) {
            $protocolos = $this->protocoloService->buscarPorCandidato($idCandidato);
            $recursos   = $this->candidatoRecursoService->buscarResumoRecursosPorCandidato($idCandidato);
        } else {
            $protocolos = [];
            $recursos   = [];
        }

        return view('Publica/layoutLogado', [
            'camada1'      => $camada1,
            'camada2'      => $camada2,
            'pagina'       => $page,
            'status'       => $candidato ? 'registrado' : 'naoregistrado',
            'cargosAtivos' => $this->editaisService->buscarEditaisAtivosCargos(),
            'candidato'    => $idCandidato,
            'params'       => $dataSession,
            'protocolos'   => $protocolos,
            'recursos'     => $recursos,
            'titulo'       => ucfirst('Dados Pessoais e Acadêmicos'),
            'dataAtual'    => date('d/m/Y'),
        ]);
    }

    public function dadosCandidato($camada1 = '', $camada2 = 'pages', $page = 'FormularioPessoais')
    {
        if (!checklogged()) {
            return redirect()->to('Home');
        }

        $dataSession = $this->sessaoService->obterUsuario();
        $candidato   = $this->candidatoService->buscarPorCpf($dataSession['cpf']);
        $acao        = $candidato ? 'update' : 'create';
        $id          = $candidato ? $candidato->pk_id_cadastrado : null;

        $dados = $candidato ?? (object) [
            'pk_id_cadastrado' => null,
            'fk_id_gov'        => $dataSession['id'],
            'ds_nome'          => $dataSession['nome'],
            'ds_cpf'           => $dataSession['cpf'],
            'ds_email'         => $dataSession['email'],
        ];

        return view('Publica/layoutLogado', [
            'camada1'      => $camada1,
            'camada2'      => $camada2,
            'pagina'       => $page,
            'params'       => $dataSession,
            'acao'         => $acao,
            'dados'        => $dados,
            'idCandidato'  => $id,
            'titulo'       => ucfirst('Registrar seus dados pessoais'),
            'dataAtual'    => date('d/m/Y'),
            'saudacao'     => $this->saudacao(),
            'bairro'       => $this->bairroFormatado($candidato),
            'deficiencias' => $this->candidatoService->listarDeficiencias(),
            'deficiencia'  => $this->candidatoService->buscarDeficiencia($candidato?->fk_id_pne),
        ]);
    }

    public function dadosClassificatorios($edital, $cargo, $id, $camada1 = '', $camada2 = 'pages', $page = 'FormularioClassificatorio')
    {
        try {
            $this->editaisService->estaAtivo($edital);
        } catch (EditalForaDoPrazoException $e) {
            return redirect()->route('Cadastros')->with('mensagemError', $e->getMessage());
        }

        if (!checklogged()) {
            return redirect()->to('Home');
        }

        $dataSession = $this->sessaoService->obterUsuario();
        $requisitos  = $this->classificatorioService->buscarRequisitos($cargo);
        $cadastrados = $this->classificatorioService->buscarDadosCadastrados($id, $cargo, $edital);

        return view('Publica/layoutLogado', [
            'camada1'                              => $camada1,
            'camada2'                              => $camada2,
            'pagina'                               => $page,
            'params'                               => $dataSession,
            'experienciasClassificatorias'         => $requisitos['experienciasClassificatorias'],
            'escolaridadesClassificatorias'        => $requisitos['escolaridadesClassificatorias'],
            'aperfeicoamentoClassificatorios'      => $requisitos['aperfeicoamentosClassificatorios'],
            'criteriosAdicionaisClassificatorios'  => $requisitos['criteriosAdicionaisClassificatorios'],
            'dadosEscolaridadeIndexado'            => $cadastrados['escolaridadesIndexadas'],
            'dadosAperfeicoamentoIndexado'         => $cadastrados['aperfeicoamentosIndexados'],
            'dadosCriterioAdicionalIndexado'       => $cadastrados['criteriosAdicionaisIndexados'],
            'dadosCriterioAdicional'                => $cadastrados['criteriosAdicionais'],
            'dadosExperiencia'                     => $cadastrados['experiencias'],
            'experienciasSalvas'                   => $cadastrados['experienciasSalvas'],
            'idsEscolaridadesCandidato'            => $cadastrados['idsEscolaridades'],
            'idsAperfeicoamentosCandidato'         => $cadastrados['idsAperfeicoamentos'],
            'idsCriteriosAdicionaisCandidato'      => $cadastrados['idsCriteriosAdicionais'],
            'idCandidato'                            => $id,
            'idCargo'                              => $cargo,
            'idEdital'                             => $edital,
            'titulo'                               => ucfirst('Registrar seus dados acadêmicos e profissionais'),
            'dataAtual'                            => date('d/m/Y'),
            'saudacao'                             => $this->saudacao(),
            'cargos'                               => $this->classificatorioService->buscarDadosCargo($cargo),
        ]);
    }

    public function registrarDadosPessoais()
    {
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

        return redirect()->route('dadosCandidato')
            ->withInput()
            ->with('errors', $resultado['erro'] ?? 'Erro ao salvar');
    }

    public function registrarDadosClassificatorios()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('Home');
        }

        $post        = $this->request->getPost();
        $cargoId     = $post['idCargo'] ?? null;
        $editalId    = $post['idEdital'] ?? null;
        $candidatoId = $post['idCandidato'] ?? null;

        $this->classificatorioService->processarFormulario($post, $candidatoId, $cargoId, $editalId);

        $protocoloDto = $this->protocoloService->buscarOuGerar($candidatoId, $cargoId, $editalId);
        $this->protocoloService->salvar($protocoloDto);

        return redirect()->route('sucessoClassificatorio', [$candidatoId, $cargoId, $editalId])
            ->with('mensagemSuccess', 'Registro atualizado com sucesso!');
    }

    public function sucessoClassificatorio($idCandidato, $idCargo, $idEdital, $camada1 = '', $camada2 = 'pages', $page = 'SucessoClassificatorio')
    {
        return view('Publica/layoutLogado', [
            'camada1'     => $camada1,
            'camada2'     => $camada2,
            'pagina'      => $page,
            'idCandidato' => $idCandidato,
            'idCargo'     => $idCargo,
            'idEdital'    => $idEdital,
            'titulo'      => ucfirst('Registro concluído!'),
            'dataAtual'   => date('d/m/Y'),
        ]);
    }

    public function gerarComprovante($idEdital, $idCargo, $idCandidato)
    {
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

    public function loginGovBr()
    {
        $user    = $this->request->getVar('user');
        $destino = $this->request->getVar('destino') ?? 'Home';

        if (empty($user)) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['error' => 'Parâmetro user é obrigatório']);
        }

        $resultado = $this->govBrService->autenticar($user);
        if (!$resultado->sucesso) {
            log_message('error', 'Gov.BR Auth Error: ' . $resultado->erro);
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['error' => 'Falha na autenticação', 'details' => $resultado->erro]);
        }

        $dataSession = $this->govBrService->prepararDadosSessao($resultado->dados, $user);
        $this->sessaoService->definirUsuario($dataSession);
        log_message('info', "Usuário {$dataSession['nome']} ({$dataSession['cpf']}) autenticado via Gov.BR");

        return redirect()->to($destino);
    }

    public function logOut()
    {
        $usuario = $this->sessaoService->obterUsuario();
        $su = $usuario['su'] ?? null;
        $this->sessaoService->destruir();
        log_message('info', "Logout realizado para sessão: {$su}");
        return redirect()->route('home');
    }

    public function detalhesRecurso($protocolo, $camada1 = '', $camada2 = 'pages', $page = 'DetalhesRecurso')
    {
        if (!is_file(APPPATH . "Views/Publica/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (!checklogged()) {
            return redirect()->to('Home');
        }

        $dataSession = $this->sessaoService->obterUsuario();
        $candidato   = $this->candidatoService->buscarPorCpf($dataSession['cpf']);
        $detalhes    = $this->candidatoRecursoService->buscarDetalhesRecurso($protocolo);

        if (empty($detalhes)) {
            return redirect()->route('Cadastros')->with('mensagemError', 'Recurso não encontrado.');
        }

        return view('Publica/layoutLogado', [
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'    => $page,
            'protocolo' => $protocolo,
            'detalhes'  => $detalhes,
            'params'    => $dataSession,
            'titulo'    => 'Detalhes do Recurso',
            'dataAtual' => date('d/m/Y'),
        ]);
    }
}
