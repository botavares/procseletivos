<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Enums\Admin\ClassificacaoStatusEnum;
use App\Models\Admin\ClassificacaoModel;
use App\Models\Admin\CargosModel;
use App\Models\Admin\ProtocolosModel;
use App\Models\Admin\CadastrosExperienciasModel;
use App\Models\Admin\CadastrosEscolaridadesModel;
use App\Models\Admin\CadastrosAperfeicoamentosModel;
use App\Models\ProcessosSeletivosModel;

class Classificacoes extends BaseController{

    protected $classificacaoModel;
    protected $cargosModel;
    protected $processosSeletivosModel;
    protected $protocolosModel;

    public function __construct(){
        $this->classificacaoModel     = new \App\Models\Admin\ClassificacaoModel();
        $this->cargosModel             = new \App\Models\Admin\CargosModel();
        $this->processosSeletivosModel = new \App\Models\ProcessosSeletivosModel();
        $this->protocolosModel        = new \App\Models\Admin\ProtocolosModel();
    }

    public function index($idProcesso = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'Classificacoes'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        if (!$idProcesso || !$idCargo) {
            return redirect()->to(base_url('home'))->with('error', 'Processo ou cargo não informado.');
        }

        $dadosCargo   = service('cargoService')->listarCargosId($idCargo);
        $dadosProcesso = $this->processosSeletivosModel->find($idProcesso);

        if (!$dadosCargo || !$dadosProcesso) {
            return redirect()->to(base_url('home'))->with('error', 'Cargo ou Processo não encontrado.');
        }

        $idSecretaria = (int) session('fk_id_secretaria');

        $classificacoes = service('classificacaoService')->listarClassificacao((int)$idProcesso, (int)$idCargo, $idSecretaria);

        // Verifica se há desempate dinâmico configurado
        $configDesempate = service('adminDesempateConfigService')->buscarConfiguracao((int)$idCargo, (int)$idProcesso, $idSecretaria);
        // Remove PONTUACAO_TOTAL da exibição (já existe coluna fixa "Total de Pontos")
        $configDesempate = array_values(array_filter($configDesempate, fn($c) => $c->tipoCriterio !== 'PONTUACAO_TOTAL'));
        $usaDesempateDinamico = !empty($configDesempate);

        // Colunas dinâmicas baseadas nas configurações do cargo (filtradas por processo)
        $colunasService = service('classificacaoColunasService');
        $colunasDinamicas = $colunasService->obterColunas((int)$idCargo, (int)$idProcesso, $idSecretaria);

        // Busca dados dinâmicos dos candidatos
        $dadosDinamicos = [];
        if (!empty($classificacoes)) {
            $idsCandidatos = array_column($classificacoes, 'fk_id_candidato');
            $dadosDinamicos = $colunasService->obterDadosDinamicos((int)$idProcesso, (int)$idCargo, $idsCandidatos, $idSecretaria);
        }

        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'classificacoes' => $classificacoes,
            'idProcesso'    => $idProcesso,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'nomeProcesso'  => $this->formatarNumeroProcesso($dadosProcesso),
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Classificação ",
            'usaDesempateDinamico' => $usaDesempateDinamico,
            'configDesempate' => $configDesempate,
            'colunasDinamicas' => $colunasDinamicas,
            'dadosDinamicos' => $dadosDinamicos,
        ];

        echo view('Admin/layoutDash', $parametros);
    }

    private function formatarNumeroProcesso($processo): string
    {
        if (empty($processo)) {
            return '';
        }
        $numero = str_pad((string)($processo->nm_numero_processo ?? ''), 3, '0', STR_PAD_LEFT);
        $ano    = $processo->nm_ano_processo ?? '';
        return $ano ? "{$numero}/{$ano}" : $numero;
    }

    public function reprocessar($processo, $cargo){
        $idSecretaria = (int) session('fk_id_secretaria');
        service('classificacaoService')->reprocessar((int)$processo, (int)$cargo, $idSecretaria);
        return redirect()->back()->with('success', 'Classificação reprocessada com sucesso.');
    }

    /**
     * Dispara a classificacao (preliminar ou homologacao) para o processo/cargo selecionados.
     * Apenas administradores podem executar essa acao.
     */
    public function disparar()
    {
        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        if (session('administrador') != '1') {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Apenas administradores podem disparar a classificação.');
        }

        $processo = (int) $this->request->getPost('processo');
        $cargo    = (int) $this->request->getPost('cargo');
        $tipo     = (string) $this->request->getPost('tipo_classificacao');

        if ($processo <= 0 || $cargo <= 0 || !in_array($tipo, ['preliminar', 'homologacao'], true)) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Processo, cargo ou tipo de classificação inválidos.');
        }

        try {
            service('disparoClassificacaoService')->disparar(
                $processo,
                $cargo,
                $tipo,
                (int) session('id')
            );

            $labelTipo = $tipo === 'preliminar' ? 'preliminar' : 'homologação';
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemSuccess', "Classificação {$labelTipo} disparada com sucesso.");
        } catch (\Exception $e) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', $e->getMessage());
        }
    }

    public function gerarClassificacao($secretaria, $processo, $cargo){
        if ((int) $secretaria !== (int) session('fk_id_secretaria')) {
            return redirect()->to(base_url('home'))->with('error', 'Secretaria inválida para o usuário logado.');
        }

        $protocolos = $this->protocolosModel->protocolosCadastradosProcesso($secretaria, $processo, $cargo);

        foreach ($protocolos as $protocolo) {
            $idCandidato = $protocolo->fk_id_cadastrado;

            // Limpa classificação anterior
            $this->classificacaoModel->where([
                'fk_id_secretaria' => $secretaria,
                'fk_id_candidato'  => $idCandidato,
                'fk_id_processo'   => $processo,
                'fk_id_cargo'      => $cargo,
            ])->delete();

            // Experiências
            $experiencias = (new CadastrosExperienciasModel())
                ->buscarExperienciasParaClassificacao($idCandidato, $processo, $cargo);
            foreach ($experiencias as $exp) {
                if ((int) $exp->ds_status !== ClassificacaoStatusEnum::CLASSIFICATORIA->value) continue;
                $total = (float) $exp->ds_quantidade * (float) $exp->ds_multiplicador;
                $max   = (float) ($exp->ds_quantidade_maxima ?? 0);
                if ($max > 0 && $total > $max) $total = $max;
                $this->classificacaoModel->insert([
                    'fk_id_secretaria'         => $secretaria,
                    'fk_id_candidato'          => $idCandidato,
                    'fk_id_processo'           => $processo,
                    'fk_id_cargo'              => $cargo,
                    'ds_tipo_classificacao'    => 'EXP',
                    'fk_id_tipo_classificacao' => $exp->fk_id_experiencia,
                    'ds_nome_tipo'             => $exp->ds_nome_experiencia,
                    'ds_quantidade'            => $exp->ds_quantidade,
                    'ds_total_tipo'            => $total,
                ]);
            }

            // Escolaridades
            $escolaridades = (new CadastrosEscolaridadesModel())
                ->buscarEscolaridadesParaClassificacao($idCandidato, $processo, $cargo);
            foreach ($escolaridades as $esc) {
                if ((int) $esc->ds_status !== ClassificacaoStatusEnum::CLASSIFICATORIA->value) continue;
                $total = (float) $esc->ds_quantidade * (float) $esc->ds_multiplicador;
                $max   = (float) ($esc->ds_pontuacao_maxima ?? 0);
                if ($max > 0 && $total > $max) $total = $max;
                $this->classificacaoModel->insert([
                    'fk_id_secretaria'         => $secretaria,
                    'fk_id_candidato'          => $idCandidato,
                    'fk_id_processo'           => $processo,
                    'fk_id_cargo'              => $cargo,
                    'ds_tipo_classificacao'    => 'ESC',
                    'fk_id_tipo_classificacao' => $esc->fk_id_escolaridade,
                    'ds_nome_tipo'             => $esc->ds_nome_escolaridade,
                    'ds_quantidade'            => $esc->ds_quantidade,
                    'ds_total_tipo'            => $total,
                ]);
            }

            // Aperfeiçoamentos
            $aperfeicoamentos = (new CadastrosAperfeicoamentosModel())
                ->buscarAperfeicoamentosParaClassificacao($idCandidato, $processo, $cargo);
            foreach ($aperfeicoamentos as $ape) {
                if ((int) $ape->ds_status !== ClassificacaoStatusEnum::CLASSIFICATORIA->value) continue;
                $total = (float) $ape->ds_quantidade * (float) $ape->ds_multiplicador;
                $max   = (float) ($ape->ds_pontuacao_maxima ?? 0);
                if ($max > 0 && $total > $max) $total = $max;
                $this->classificacaoModel->insert([
                    'fk_id_secretaria'         => $secretaria,
                    'fk_id_candidato'          => $idCandidato,
                    'fk_id_processo'           => $processo,
                    'fk_id_cargo'              => $cargo,
                    'ds_tipo_classificacao'    => 'APE',
                    'fk_id_tipo_classificacao' => $ape->fk_id_curso,
                    'ds_nome_tipo'             => $ape->ds_nome_curso,
                    'ds_quantidade'            => $ape->ds_quantidade,
                    'ds_total_tipo'            => $total,
                ]);
            }
        }
    }

    public function gerarRanking($processo, $cargo){
        $idSecretaria = (int) session('fk_id_secretaria');
        $service = service('classificacaoService');
        $service->reprocessar((int)$processo, (int)$cargo, $idSecretaria);

        return redirect()->back()->with('success', 'Ranking reprocessado com sucesso.');
    }

    private function obterDadosExportacao(int $processo, int $cargo, array $classificacoes): array
    {
        $idSecretaria = (int) session('fk_id_secretaria');

        $configDesempate = service('adminDesempateConfigService')->buscarConfiguracao($cargo, $processo, $idSecretaria);
        $configDesempate = array_values(array_filter($configDesempate, fn($c) => $c->tipoCriterio !== 'PONTUACAO_TOTAL'));
        $usaDesempateDinamico = !empty($configDesempate);

        $colunasDinamicas = [];
        $dadosDinamicos   = [];
        if (!$usaDesempateDinamico) {
            $colunasService   = service('classificacaoColunasService');
            $colunasDinamicas = $colunasService->obterColunas($cargo, $processo, $idSecretaria);
            $dadosDinamicos   = $colunasService->obterDadosDinamicos($processo, $cargo, array_column($classificacoes, 'fk_id_candidato'), $idSecretaria);
        }

        $dadosProcesso = $this->processosSeletivosModel->find($processo);
        $dadosCargo    = $this->cargosModel->find($cargo);

        return [
            'nomeProcesso'   => $this->formatarNumeroProcesso($dadosProcesso),
            'nomeCargo'      => $dadosCargo->ds_nome_cargo   ?? 'cargo',
            'configDesempate' => $configDesempate,
            'usaDesempateDinamico' => $usaDesempateDinamico,
            'colunasDinamicas' => $colunasDinamicas,
            'dadosDinamicos' => $dadosDinamicos,
        ];
    }

    public function exportarXlsx($processo, $cargo)
    {
        $idSecretaria   = (int) session('fk_id_secretaria');
        $classificacoes = service('classificacaoService')->listarClassificacao((int)$processo, (int)$cargo, $idSecretaria);

        if (empty($classificacoes)) {
            return redirect()->back()->with('error', 'A classificação está vazia. Execute o reprocessamento antes de exportar.');
        }

        $somentePcd = $this->request->getGet('pcd') == '1';
        if ($somentePcd) {
            $classificacoes = array_filter($classificacoes, fn($c) => ($c['ds_possui_pne'] ?? 0) == 1);
            if (empty($classificacoes)) {
                return redirect()->back()->with('error', 'Não há candidatos PCD nesta classificação.');
            }
        }

        $dados = $this->obterDadosExportacao((int)$processo, (int)$cargo, $classificacoes);

        service('excelExportService')->exportarClassificacao(
            $classificacoes,
            ['processo' => $dados['nomeProcesso'], 'cargo' => $dados['nomeCargo']],
            $dados['configDesempate'],
            $dados['colunasDinamicas'],
            $dados['dadosDinamicos'],
            $dados['usaDesempateDinamico'],
            $somentePcd
        );
    }

    public function gerarPdf($processo, $cargo){
        $idSecretaria   = (int) session('fk_id_secretaria');
        $classificacoes = service('classificacaoService')->listarClassificacao((int)$processo, (int)$cargo, $idSecretaria);

        if (empty($classificacoes)) {
            return redirect()->back()->with('error', 'A classificação está vazia. Execute o reprocessamento antes de gerar o PDF.');
        }

        $dados = $this->obterDadosExportacao((int)$processo, (int)$cargo, $classificacoes);

        service('pdfExportService')->gerarPdf('ClassificacaoPorCargo', [
            'classificacoes'       => $classificacoes,
            'nomeProcesso'         => $dados['nomeProcesso'],
            'nomeCargo'            => $dados['nomeCargo'],
            'dataGeracao'          => date('d/m/Y H:i:s'),
            'usaDesempateDinamico' => $dados['usaDesempateDinamico'],
            'configDesempate'      => $dados['configDesempate'],
            'colunasDinamicas'     => $dados['colunasDinamicas'],
            'dadosDinamicos'       => $dados['dadosDinamicos'],
        ], 'ClassificacaoPorCargo');
    }

    public function exportarTabela($processo, $cargo){
        $dados   = json_decode($this->request->getPost('dados'), true);
        $titulos = json_decode($this->request->getPost('titulos'), true);

        if (empty($dados) || empty($titulos)) {
            return redirect()->back()->with('error', 'Nenhum dado foi recebido para exportação. Verifique se a tabela possui registros.');
        }

        $dadosProcesso = $this->processosSeletivosModel->find($processo);
        $dadosCargo    = $this->cargosModel->find($cargo);

        service('excelExportService')->exportarTabela($dados, $titulos, [
            'processo' => $this->formatarNumeroProcesso($dadosProcesso),
            'cargo'    => $dadosCargo->ds_nome_cargo   ?? 'cargo',
        ]);
    }

    public function salvarEscolha(){
        $idSecretaria = (int) session('fk_id_secretaria');
        $processo = (int) $this->request->getPost('processo');
        $cargo    = (int) $this->request->getPost('cargo');

        // Valida se o cargo pertence à secretaria do usuário logado
        $dadosCargo = $this->cargosModel->find($cargo);
        if (!$dadosCargo || (int) $dadosCargo->fk_id_secretaria !== $idSecretaria) {
            return redirect()->to(base_url_adm('Dashboard'))->with('error', 'Cargo não pertence à secretaria do usuário logado.');
        }

        return redirect()->to(base_url_adm("Classificacoes/{$processo}/{$cargo}"));
    }
}
