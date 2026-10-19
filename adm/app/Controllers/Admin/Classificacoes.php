<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use FilesystemIterator;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Controller;
use App\Models\ClassificacaoModel;
use App\Models\CargosModel;
use App\Models\EditaisModel;

use App\Services\Classificacao\ClassificacaoService;
use App\Services\Classificacao\DesempateConfigService;
use App\Services\Classificacao\ClassificacaoColunasService;
use App\Services\Cargos\CargoService;
use App\Services\Editais\EditalService;


use App\Models\CadastrosExperienciasModel;
use App\Models\CadastrosEscolaridadesModel;
use App\Models\CadastrosAperfeicoamentosModel;

use App\Models\ProtocolosModel;

class Classificacoes extends BaseController{

    protected $classificacaoModel;
    protected $cargosModel;
    protected $editaisModel;
    protected $cadastrosExperienciasModel;
    protected $cadastrosEscolaridadesModel;
    protected $cadastrosAperfeicoamentosModel;
    protected $protocolosModel;

    public function __construct(){
        parent::__construct();
        $this->classificacaoModel            = new \App\Models\ClassificacaoModel();
        $this->cargosModel                     = new \App\Models\CargosModel();
        $this->editaisModel                    = new \App\Models\EditaisModel();
        $this->cadastrosExperienciasModel      = new \App\Models\CadastrosExperienciasModel();
        $this->cadastrosEscolaridadesModel     = new \App\Models\CadastrosEscolaridadesModel();
        $this->cadastrosAperfeicoamentosModel = new \App\Models\CadastrosAperfeicoamentosModel();
        $this->protocolosModel                = new \App\Models\ProtocolosModel();
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
        $configDesempate = service('desempateConfigService')->buscarConfiguracao((int)$idCargo);
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

        echo view('layoutDash', $parametros);
        
    }

    public function reprocessar($edital, $cargo){
        service('classificacaoService')->reprocessar((int)$edital, (int)$cargo);
        return redirect()->back()
        ->with('success', 'Classificação reprocessada com sucesso.');
    }

    public function gerarClassificacao($secretaria, $edital, $cargo){
        $dadosProtocolo = $this->protocolosModel->protocolosCadastradosEdital($secretaria, $edital, $cargo);
        


        //PEGAR TODOS PROTOCOLOS DO EDITAL
        foreach($dadosProtocolo as $protocolo){
            
            $idCandidato = $protocolo->fk_id_cadastrado;

            //LIMPA A CLASSIFICAÇÃO ANTERIOR DO CANDIDATO PARA O EDITAL E CARGO
            $this->classificacaoModel->where([
                'fk_id_candidato' => $idCandidato,
                'fk_id_edital' => $edital,
                'fk_id_cargo' => $cargo
            ])->delete();

            

            //IDENTIFICA AS EXPERIÊNCIAS DO CANDIDATO
            $experienciaCandidato = $this->cadastrosExperienciasModel->buscarExperienciasParaClassificacao($idCandidato, $edital, $cargo);
            
            
            foreach($experienciaCandidato as $experiencia){
                
                if($experiencia->ds_status == 0){ //status = 0 significa que a experiência é classificatória, ou seja, tem pontuação definida no edital
                    $totalPontos = $experiencia->ds_quantidade * $experiencia->ds_multiplicador;
                    if($totalPontos > $experiencia->ds_quantidade_maxima){
                        $totalPontos = $experiencia->ds_quantidade_maxima;
                    }
                    $this->classificacaoModel->insert([
                        'fk_id_candidato' => $idCandidato,
                        'fk_id_edital' => $edital,
                        'fk_id_cargo' => $cargo,
                        'ds_tipo_classificacao' => 'EXP',
                        'fk_id_tipo_classificacao' => $experiencia->fk_id_experiencia,
                        'ds_nome_tipo' => $experiencia->ds_nome_experiencia,
                        'ds_quantidade' => $experiencia->ds_quantidade,
                        'ds_total_tipo' => $totalPontos
                    ]);

                }
            }

            //IDENTIFICA AS ESCOLARIDADES DO CANDIDATO
            $escolaridadeCandidato = $this->cadastrosEscolaridadesModel->buscarEscolaridadesParaClassificacao($idCandidato, $edital, $cargo);
            
            foreach($escolaridadeCandidato as $escolaridade){

                if($escolaridade->ds_status == 0){ //status = 0 significa que a escolaridade é classificatória, ou seja, tem pontuação definida no edital
                    $totalPontos = $escolaridade->ds_quantidade * $escolaridade->ds_multiplicador;
                    if($totalPontos > $escolaridade->ds_pontuacao_maxima){
                        $totalPontos = $escolaridade->ds_pontuacao_maxima;
                    }
                    $this->classificacaoModel->insert([
                        'fk_id_candidato' => $idCandidato,
                        'fk_id_edital' => $edital,
                        'fk_id_cargo' => $cargo,
                        'ds_tipo_classificacao' => 'ESC',
                        'fk_id_tipo_classificacao' => $escolaridade->fk_id_escolaridade,
                        'ds_nome_tipo' => $escolaridade->ds_nome_escolaridade,
                        'ds_quantidade' => $escolaridade->ds_quantidade,
                        'ds_total_tipo' => $totalPontos
                    ]);

                }
            }

            //IDENTIFICA OS APERFEIÇOAMENTOS DO CANDIDATO
            $aperfeicoamentoCandidato = $this->cadastrosAperfeicoamentosModel->buscarAperfeicoamentosParaClassificacao($idCandidato, $edital, $cargo);
            foreach($aperfeicoamentoCandidato as $aperfeicoamento){
                
                if($aperfeicoamento->ds_status == 0){ //status = 0 significa que a escolaridade é classificatória, ou seja, tem pontuação definida no edital
                    $totalPontos = $aperfeicoamento->ds_quantidade * $aperfeicoamento->ds_multiplicador;
                    if($totalPontos > $aperfeicoamento->ds_pontuacao_maxima){
                        $totalPontos = $aperfeicoamento->ds_pontuacao_maxima;
                    }
                    $this->classificacaoModel->insert([
                        'fk_id_candidato' => $idCandidato,
                        'fk_id_edital' => $edital,
                        'fk_id_cargo' => $cargo,
                        'ds_tipo_classificacao' => 'APE',
                        'fk_id_tipo_classificacao' => $aperfeicoamento->fk_id_curso,
                        'ds_nome_tipo' => $aperfeicoamento->ds_nome_curso,
                        'ds_quantidade' => $aperfeicoamento->ds_quantidade,
                        'ds_total_tipo' => $totalPontos
                    ]);

                }
            }
        }
    }
    public function gerarRanking($edital, $cargo){
        $service = service('classificacaoService');
        $service->reprocessar((int)$edital, (int)$cargo);

        return redirect()->back()->with('success', 'Ranking reprocessado com sucesso.');
    }

    public function exportarXlsx($edital, $cargo)
    {
        $service = service('classificacaoService');
        $classificacoes = $service->listarClassificacao((int)$edital, (int)$cargo);

        if (empty($classificacoes)) {
            return redirect()->back()
                ->with('error', 'A classificação está vazia. Execute o reprocessamento antes de exportar.');
        }

        // Filtra apenas PCD se solicitado
        $apenasPcd = $this->request->getGet('pcd');
        $somentePcd = ($apenasPcd !== null && $apenasPcd == '1');
        if ($somentePcd) {
            $classificacoes = array_filter($classificacoes, function ($c) {
                return ($c['ds_possui_pne'] ?? 0) == 1;
            });
            if (empty($classificacoes)) {
                return redirect()->back()
                    ->with('error', 'Não há candidatos PCD nesta classificação.');
            }
        }

        // Detecta desempate dinâmico (mesma lógica do index)
        $desempateConfigService = service('desempateConfigService');
        $configDesempate = $desempateConfigService->buscarConfiguracao((int)$cargo);
        // Remove PONTUACAO_TOTAL da exibição (já existe coluna fixa "Total de Pontos")
        $configDesempate = array_values(array_filter($configDesempate, fn($c) => $c->tipoCriterio !== 'PONTUACAO_TOTAL'));
        $usaDesempateDinamico = !empty($configDesempate);

        $editalInfo = $this->editaisModel->find($edital);
        $cargoInfo  = $this->cargosModel->find($cargo);
        $nomeEdital = $editalInfo->ds_numero_edital ?? 'edital';
        $nomeCargo  = $cargoInfo->ds_nome_cargo   ?? 'cargo';

        $colunasDinamicas = [];
        $dadosDinamicos   = [];
        if (!$usaDesempateDinamico) {
            $colunasService = service('classificacaoColunasService');
            $colunasDinamicas = $colunasService->obterColunas((int)$cargo);
            $idsCandidatos = array_column($classificacoes, 'fk_id_candidato');
            $dadosDinamicos = $colunasService->obterDadosDinamicos((int)$edital, (int)$cargo, $idsCandidatos);
        }

        service('excelExportService')->exportarClassificacao(
            $classificacoes,
            ['edital' => $nomeEdital, 'cargo' => $nomeCargo],
            $configDesempate,
            $colunasDinamicas,
            $dadosDinamicos,
            $usaDesempateDinamico,
            $somentePcd
        );
    }

    public function gerarPdf($edital, $cargo){
        $service = service('classificacaoService');
        $classificacoes = $service->listarClassificacao((int)$edital, (int)$cargo);

        if (empty($classificacoes)) {
            return redirect()->back()
                ->with('error', 'A classificação está vazia. Execute o reprocessamento antes de gerar o PDF.');
        }

        $editalInfo = $this->editaisModel->find($edital);
        $cargoInfo  = $this->cargosModel->find($cargo);

        $nomeEdital = $editalInfo->ds_numero_edital ?? 'edital';
        $nomeCargo  = $cargoInfo->ds_nome_cargo   ?? 'cargo';

        // Detecta desempate dinâmico
        $desempateConfigService = service('desempateConfigService');
        $configDesempate = $desempateConfigService->buscarConfiguracao((int)$cargo);
        // Remove PONTUACAO_TOTAL da exibição (já existe coluna fixa "Total de Pontos")
        $configDesempate = array_values(array_filter($configDesempate, fn($c) => $c->tipoCriterio !== 'PONTUACAO_TOTAL'));
        $usaDesempateDinamico = !empty($configDesempate);

        $colunasDinamicas = [];
        $dadosDinamicos = [];
        if (!$usaDesempateDinamico) {
            $colunasService = service('classificacaoColunasService');
            $colunasDinamicas = $colunasService->obterColunas((int)$cargo);
            $idsCandidatos = array_column($classificacoes, 'fk_id_candidato');
            $dadosDinamicos = $colunasService->obterDadosDinamicos((int)$edital, (int)$cargo, $idsCandidatos);
        }

        $dados = [
            'classificacoes' => $classificacoes,
            'nomeEdital'     => $nomeEdital,
            'nomeCargo'      => $nomeCargo,
            'dataGeracao'    => date('d/m/Y H:i:s'),
            'usaDesempateDinamico' => $usaDesempateDinamico,
            'configDesempate' => $configDesempate,
            'colunasDinamicas' => $colunasDinamicas,
            'dadosDinamicos' => $dadosDinamicos,
        ];

        service('pdfExportService')->gerarPdf('ClassificacaoPorCargo', $dados, 'ClassificacaoPorCargo');
    }

    public function exportarTabela($edital, $cargo){
        $dadosJson  = $this->request->getPost('dados');
        $titulosJson = $this->request->getPost('titulos');

        $dados = json_decode($dadosJson, true);
        $titulos = json_decode($titulosJson, true);

        if (empty($dados) || empty($titulos)) {
            return redirect()->back()
                ->with('error', 'Nenhum dado foi recebido para exportação. Verifique se a tabela possui registros.');
        }

        $editalInfo = $this->editaisModel->find($edital);
        $cargoInfo  = $this->cargosModel->find($cargo);

        $nomeEdital = $editalInfo->ds_numero_edital ?? 'edital';
        $nomeCargo  = $cargoInfo->ds_nome_cargo   ?? 'cargo';

        service('excelExportService')->exportarTabela(
            $dados,
            $titulos,
            ['edital' => $nomeEdital, 'cargo' => $nomeCargo]
        );
    }

     /*===============================================================================
            FUNÇÃO: salvarEscolha;
            OBJETIVO: Salvar a escolha do edital e curso e redirecionar para a listagem de candidatos;
            PARAMETROS: nenhum
            CRIAÇÃO:25/09/2025
            MODIFICADO:
            RESUMO: Função salva a escolha do edital e curso e redireciona para a listagem de candidatos.
    ==============================================================================*/
    public function salvarEscolha(){
        $idEdital = $this->request->getPost('edital');
        $idCargo  = $this->request->getPost('cargo');

        // redireciona para GET
        return redirect()->to(base_url("Classificacoes/{$idEdital}/{$idCargo}"));
    }
    
}