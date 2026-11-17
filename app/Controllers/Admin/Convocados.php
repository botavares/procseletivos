<?php

namespace App\Controllers\Admin;

use App\Enums\SituacaoCandidatosEnum;
use App\Models\Admin\CargosModel;
use App\Models\Admin\ClassificacaoModel;
use App\Models\Admin\SituacaoModel;
use App\Services\Admin\Convocados\ConvocadosService;
use App\Services\Admin\ProcessosConvocacao\ProcessosConvocacaoService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Convocados extends BaseController
{
    protected ?ProcessosConvocacaoService $convocacaoService = null;
    protected ?ConvocadosService $convocadosService = null;
    protected ?ClassificacaoModel $classificacaoModel = null;
    protected ?CargosModel $cargosModel = null;
    protected ?SituacaoModel $situacaoModel = null;

    public function __construct()
    {
        $this->convocacaoService = service('processosConvocacaoService');
        $this->convocadosService  = service('convocadosService');
        $this->classificacaoModel = new ClassificacaoModel();
        $this->cargosModel        = new CargosModel();
        $this->situacaoModel      = service('situacaoModel');
    }

    /**
     * Exibe a tela de convocação para um processo e cargo.
     * Os candidatos são exibidos ordenados pela classificação, permitindo
     * marcar titulares e suplentes para a convocação atual.
     */
    public function index($idProcesso = null, $idCargo = null)
    {
        $camada1 = '';
        $camada2 = 'pages';
        $page    = 'Convocados';

        if (!is_file(APPPATH . 'Views/Admin/' . ($camada1 ? $camada1 . '/' : '') . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        $idProcesso = (int) $idProcesso;
        $idCargo    = (int) $idCargo;

        if (!$idProcesso || !$idCargo) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Processo ou cargo não informados.');
        }

        $idSecretaria = (int) session('fk_id_secretaria');

        $dadosCargo = $this->cargosModel->find($idCargo);
        if (!$dadosCargo || (int) $dadosCargo->fk_id_secretaria !== $idSecretaria) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Cargo não pertence à secretaria do usuário logado.');
        }

        $convocacaoAtual = $this->convocadosService->buscarConvocacaoAtual($idProcesso, $idCargo);
        if (!$convocacaoAtual) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Não existe convocação ativa para este processo/cargo.');
        }

        $idConvocacao = (int) $convocacaoAtual->pk_id_convocacao;

        // Atualiza situação de titulares/suplentes da convocação anterior
        $this->convocadosService->processarConvocacaoAnterior($idProcesso, $idCargo, $idConvocacao);

        $candidatosClassificados = $this->classificacaoModel->listarClassificacao($idProcesso, $idCargo);
        $candidatosClassificados = $this->filtrarCandidatosConvocaveis($candidatosClassificados, $idProcesso, $idCargo);

        $convocadosAtual         = $this->convocadosService->buscarConvocadosPorConvocacao($idConvocacao);

        $titulares = [];
        $suplentes = [];
        foreach ($convocadosAtual as $convocado) {
            $id = (int) $convocado->fk_id_candidato;
            if ((int) $convocado->nm_tipo === 1) {
                $titulares[$id] = true;
            } else {
                $suplentes[$id] = true;
            }
        }

        echo view('Admin/layoutDash', [
            'camada1'         => $camada1,
            'camada2'         => $camada2,
            'pagina'          => $page,
            'candidatos'      => $candidatosClassificados,
            'titulares'       => $titulares,
            'suplentes'       => $suplentes,
            'titulosTabela'   => ['Ordem classificação', 'Nome do Candidato', 'Titular', 'Suplente'],
            'titulo'          => 'Convocar Candidatos',
            'nomeCargo'       => $dadosCargo->ds_nome_cargo,
            'idProcesso'      => $idProcesso,
            'idCargo'         => $idCargo,
            'idConvocacao'    => $idConvocacao,
            'modoInscritos'   => false,
        ]);
    }

    /**
     * Recebe a escolha do modal de convocação do dashboard.
     */
    public function salvarEscolha()
    {
        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        $idConvocacao = (int) $this->request->getPost('convocacao');

        if ($idConvocacao <= 0) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Selecione um edital de convocação.');
        }

        $dadosConvocacao = $this->convocacaoService->buscarPorId($idConvocacao);
        if (!$dadosConvocacao) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Edital de convocação não encontrado.');
        }

        $convocacao = (object) $dadosConvocacao;

        return redirect()->to(base_url_adm("Convocados/{$convocacao->fk_id_processo}/{$convocacao->fk_id_cargo}"));
    }

    /**
     * Retorna as convocações vinculadas a um processo seletivo (AJAX).
     */
    public function getConvocacoesByProcesso()
    {
        $idProcesso = $this->request->getPost('idProcesso');
        if (!$idProcesso) {
            return $this->response->setJSON(['convocacoes' => []]);
        }

        $convocacoes = $this->convocacaoService->listarPorProcesso((int) $idProcesso);

        return $this->response->setJSON(['convocacoes' => $convocacoes]);
    }

    /**
     * Remove candidatos cuja situação os impede de ser convocados novamente
     * (eliminados ou já contratados).
     */
    private function filtrarCandidatosConvocaveis(array $candidatos, int $idProcesso, int $idCargo): array
    {
        $situacoes = $this->situacaoModel
            ->where('fk_id_processo', $idProcesso)
            ->where('fk_id_cargo', $idCargo)
            ->findAll();

        $situacaoPorCandidato = [];
        foreach ($situacoes as $situacao) {
            $situacaoPorCandidato[(int) $situacao['fk_id_candidato']] = (int) $situacao['nm_id_situacao'];
        }

        $situacoesBloqueantes = [
            SituacaoCandidatosEnum::DESCLASSIFICADO->value,
            SituacaoCandidatosEnum::CONTRATADO->value,
        ];

        return array_values(array_filter($candidatos, function (array $candidato) use ($situacaoPorCandidato, $situacoesBloqueantes): bool {
            $idCandidato = (int) $candidato['fk_id_candidato'];
            $situacao    = $situacaoPorCandidato[$idCandidato] ?? SituacaoCandidatosEnum::AGUARDANDO->value;

            return !in_array($situacao, $situacoesBloqueantes, true);
        }));
    }

    /**
     * Salva os candidatos titulares e suplentes da convocação atual.
     */
    public function convocarCandidato()
    {
        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        $idProcesso   = (int) $this->request->getPost('idProcesso');
        $idCargo      = (int) $this->request->getPost('idCargo');
        $idConvocacao = (int) $this->request->getPost('idConvocacao');

        if (!$idProcesso || !$idCargo || !$idConvocacao) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Dados insuficientes para convocar candidatos.');
        }

        $titulares = $this->request->getPost('titular') ?? [];
        $suplentes = $this->request->getPost('suplente') ?? [];

        if (!is_array($titulares)) {
            $titulares = [];
        }
        if (!is_array($suplentes)) {
            $suplentes = [];
        }

        try {
            $this->convocadosService->salvarConvocados($idConvocacao, $titulares, $suplentes, $idProcesso, $idCargo);
        } catch (\Throwable $e) {
            return redirect()->to(base_url_adm("Convocados/{$idProcesso}/{$idCargo}"))
                ->with('mensagemError', 'Erro ao salvar convocados: ' . $e->getMessage());
        }

        return redirect()->to(base_url_adm("Convocados/{$idProcesso}/{$idCargo}"))
            ->with('mensagemSuccess', 'Candidatos convocados com sucesso.');
    }
}
