<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Enums\Admin\ClassificacaoStatusEnum;
use App\Models\Admin\ClassificacaoModel;
use App\Models\Admin\CargosModel;
use App\Models\Admin\EditaisModel;
use App\Models\Admin\ProtocolosModel;
use App\Models\Admin\CadastrosExperienciasModel;
use App\Models\Admin\CadastrosEscolaridadesModel;
use App\Models\Admin\CadastrosAperfeicoamentosModel;

class Classificacoes extends BaseController{

    protected $classificacaoModel;
    protected $cargosModel;
    protected $editaisModel;
    protected $protocolosModel;

    public function __construct(){
        $this->classificacaoModel = new \App\Models\Admin\ClassificacaoModel();
        $this->cargosModel        = new \App\Models\Admin\CargosModel();
        $this->editaisModel       = new \App\Models\Admin\EditaisModel();
        $this->protocolosModel    = new \App\Models\Admin\ProtocolosModel();
    }
    public function index($idEdital = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'Classificacoes'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $dadosCargo = service('cargoService')->listarCargosId($idCargo);
        $classificacoes = service('classificacaoService')->listarClassificacao($idEdital, $idCargo);

        // Verifica se há desempate dinâmico configurado
        $configDesempate = service('adminDesempateConfigService')->buscarConfiguracao((int)$idCargo);
        // Remove PONTUACAO_TOTAL da exibição (já existe coluna fixa "Total de Pontos")
        $configDesempate = array_values(array_filter($configDesempate, fn($c) => $c->tipoCriterio !== 'PONTUACAO_TOTAL'));
        $usaDesempateDinamico = !empty($configDesempate);

        // Colunas dinâmicas baseadas nas configurações do cargo
        $colunasService = service('classificacaoColunasService');
        $colunasDinamicas = $colunasService->obterColunas((int)$idCargo);

        // Busca dados dinâmicos dos candidatos
        $dadosDinamicos = [];
        if (!empty($classificacoes)) {
            $idsCandidatos = array_column($classificacoes, 'fk_id_candidato');
            $dadosDinamicos = $colunasService->obterDadosDinamicos((int)$idEdital, (int)$idCargo, $idsCandidatos);
        }

        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'classificacoes' => $classificacoes,
            'idEdital'      => $idEdital,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
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

    public function reprocessar($edital, $cargo){
        service('classificacaoService')->reprocessar((int)$edital, (int)$cargo);
        return redirect()->back()->with('success', 'Classificação reprocessada com sucesso.');
    }

    public function gerarClassificacao($secretaria, $edital, $cargo){
        $protocolos = $this->protocolosModel->protocolosCadastradosEdital($secretaria, $edital, $cargo);

        foreach ($protocolos as $protocolo) {
            $idCandidato = $protocolo->fk_id_cadastrado;

            // Limpa classificação anterior
            $this->classificacaoModel->where([
                'fk_id_candidato' => $idCandidato,
                'fk_id_edital'    => $edital,
                'fk_id_cargo'     => $cargo,
            ])->delete();

            // Experiências
            $experiencias = (new CadastrosExperienciasModel())
                ->buscarExperienciasParaClassificacao($idCandidato, $edital, $cargo);
            foreach ($experiencias as $exp) {
                if ((int) $exp->ds_status !== ClassificacaoStatusEnum::CLASSIFICATORIA->value) continue;
                $total = (float) $exp->ds_quantidade * (float) $exp->ds_multiplicador;
                $max   = (float) ($exp->ds_quantidade_maxima ?? 0);
                if ($max > 0 && $total > $max) $total = $max;
                $this->classificacaoModel->insert([
                    'fk_id_candidato'          => $idCandidato,
                    'fk_id_edital'             => $edital,
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
                ->buscarEscolaridadesParaClassificacao($idCandidato, $edital, $cargo);
            foreach ($escolaridades as $esc) {
                if ((int) $esc->ds_status !== ClassificacaoStatusEnum::CLASSIFICATORIA->value) continue;
                $total = (float) $esc->ds_quantidade * (float) $esc->ds_multiplicador;
                $max   = (float) ($esc->ds_pontuacao_maxima ?? 0);
                if ($max > 0 && $total > $max) $total = $max;
                $this->classificacaoModel->insert([
                    'fk_id_candidato'          => $idCandidato,
                    'fk_id_edital'             => $edital,
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
                ->buscarAperfeicoamentosParaClassificacao($idCandidato, $edital, $cargo);
            foreach ($aperfeicoamentos as $ape) {
                if ((int) $ape->ds_status !== ClassificacaoStatusEnum::CLASSIFICATORIA->value) continue;
                $total = (float) $ape->ds_quantidade * (float) $ape->ds_multiplicador;
                $max   = (float) ($ape->ds_pontuacao_maxima ?? 0);
                if ($max > 0 && $total > $max) $total = $max;
                $this->classificacaoModel->insert([
                    'fk_id_candidato'          => $idCandidato,
                    'fk_id_edital'             => $edital,
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
    public function gerarRanking($edital, $cargo){
        $service = service('classificacaoService');
        $service->reprocessar((int)$edital, (int)$cargo);

        return redirect()->back()->with('success', 'Ranking reprocessado com sucesso.');
    }

    private function obterDadosExportacao(int $edital, int $cargo, array $classificacoes): array
    {
        $configDesempate = service('adminDesempateConfigService')->buscarConfiguracao($cargo);
        $configDesempate = array_values(array_filter($configDesempate, fn($c) => $c->tipoCriterio !== 'PONTUACAO_TOTAL'));
        $usaDesempateDinamico = !empty($configDesempate);

        $colunasDinamicas = [];
        $dadosDinamicos   = [];
        if (!$usaDesempateDinamico) {
            $colunasService   = service('classificacaoColunasService');
            $colunasDinamicas = $colunasService->obterColunas($cargo);
            $dadosDinamicos   = $colunasService->obterDadosDinamicos($edital, $cargo, array_column($classificacoes, 'fk_id_candidato'));
        }

        return [
            'nomeEdital'     => $this->editaisModel->find($edital)->ds_numero_edital ?? 'edital',
            'nomeCargo'      => $this->cargosModel->find($cargo)->ds_nome_cargo   ?? 'cargo',
            'configDesempate' => $configDesempate,
            'usaDesempateDinamico' => $usaDesempateDinamico,
            'colunasDinamicas' => $colunasDinamicas,
            'dadosDinamicos' => $dadosDinamicos,
        ];
    }

    public function exportarXlsx($edital, $cargo)
    {
        $classificacoes = service('classificacaoService')->listarClassificacao((int)$edital, (int)$cargo);

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

        $dados = $this->obterDadosExportacao((int)$edital, (int)$cargo, $classificacoes);

        service('excelExportService')->exportarClassificacao(
            $classificacoes,
            ['edital' => $dados['nomeEdital'], 'cargo' => $dados['nomeCargo']],
            $dados['configDesempate'],
            $dados['colunasDinamicas'],
            $dados['dadosDinamicos'],
            $dados['usaDesempateDinamico'],
            $somentePcd
        );
    }

    public function gerarPdf($edital, $cargo){
        $classificacoes = service('classificacaoService')->listarClassificacao((int)$edital, (int)$cargo);

        if (empty($classificacoes)) {
            return redirect()->back()->with('error', 'A classificação está vazia. Execute o reprocessamento antes de gerar o PDF.');
        }

        $dados = $this->obterDadosExportacao((int)$edital, (int)$cargo, $classificacoes);

        service('pdfExportService')->gerarPdf('ClassificacaoPorCargo', [
            'classificacoes'       => $classificacoes,
            'nomeEdital'           => $dados['nomeEdital'],
            'nomeCargo'            => $dados['nomeCargo'],
            'dataGeracao'          => date('d/m/Y H:i:s'),
            'usaDesempateDinamico' => $dados['usaDesempateDinamico'],
            'configDesempate'      => $dados['configDesempate'],
            'colunasDinamicas'     => $dados['colunasDinamicas'],
            'dadosDinamicos'       => $dados['dadosDinamicos'],
        ], 'ClassificacaoPorCargo');
    }

    public function exportarTabela($edital, $cargo){
        $dados   = json_decode($this->request->getPost('dados'), true);
        $titulos = json_decode($this->request->getPost('titulos'), true);

        if (empty($dados) || empty($titulos)) {
            return redirect()->back()->with('error', 'Nenhum dado foi recebido para exportação. Verifique se a tabela possui registros.');
        }

        service('excelExportService')->exportarTabela($dados, $titulos, [
            'edital' => $this->editaisModel->find($edital)->ds_numero_edital ?? 'edital',
            'cargo'  => $this->cargosModel->find($cargo)->ds_nome_cargo   ?? 'cargo',
        ]);
    }

    public function salvarEscolha(){
        return redirect()->to(base_url("Classificacoes/{$this->request->getPost('edital')}/{$this->request->getPost('cargo')}"));
    }
    
}