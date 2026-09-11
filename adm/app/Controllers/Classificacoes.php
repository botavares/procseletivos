<?php

namespace App\Controllers;
use FileSystemIterator;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Controller;
use App\Models\ClassificacaoModel;
use App\Models\CargosModel;
use App\Models\EditaisModel;

use App\Services\Classificacao\ClassificacaoService;
use App\Services\Classificacao\DesempateConfigService;
use App\Services\Cargos\CargoService;
use App\Services\Editais\EditalService;

use App\Traits\FormataNumeroEditalTrait;


use App\Models\CadastrosExperienciasModel;
use App\Models\CadastrosEscolaridadesModel;
use App\Models\CadastrosAperfeicoamentosModel;

use App\Models\ProtocolosModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Classificacoes extends BaseController{

    public function __construct(){
      
      
    }
    public function index($idEdital = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'Classificacoes'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $service = new ClassificacaoService();
        $serviceCargo = new CargoService();
        $dadosCargo = $serviceCargo->listarCargosId($idCargo);
        $classificacoes = $service->listarClassificacao($idEdital, $idCargo);

        // Verifica se há desempate dinâmico configurado
        $desempateConfigService = new DesempateConfigService();
        $configDesempate = $desempateConfigService->buscarConfiguracao((int)$idCargo);
        $usaDesempateDinamico = !empty($configDesempate);

        if ($usaDesempateDinamico) {
            $titulosTabela = ["Posição", "Candidato"];
            foreach ($configDesempate as $config) {
                $titulosTabela[] = $config->descricao ?: $config->tipoCriterio;
            }
            $titulosTabela[] = "Nascimento";
            $titulosTabela[] = "Total de Pontos";
        } else {
            $titulosTabela = ["Posição","Candidato","Pts. Experiência","Pts. Graduação","Pts. Pós-Graduação","Pts. Mestrado","Pts. Doutorado","Pts. Aperfeiçoamentos","Nascimento","Total de Pontos"];
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
            "titulosTabela" => $titulosTabela,
            'titulo'        => "Classificação ",
            'usaDesempateDinamico' => $usaDesempateDinamico,
            'configDesempate' => $configDesempate,
        ];

        echo view('layoutDash', $parametros);
        
    }

    public function reprocessar($edital, $cargo){
        $service = new \App\Services\Classificacao\ClassificacaoService();
        $service->reprocessar((int)$edital, (int)$cargo);
        return redirect()->back()
        ->with('success', 'Classificação reprocessada com sucesso.');
    }

    public function gerarClassificacao($secretaria, $edital, $cargo){
        $classificacaoModel = new ClassificacaoModel();
        $cargosModel = new CargosModel();
        $editaisModel = new EditaisModel();
        $experienciasModel = new CadastrosExperienciasModel();
        $escolaridadesModel = new CadastrosEscolaridadesModel();
        $aperfeicoamentosModel = new CadastrosAperfeicoamentosModel();
        $protocolosModel = new ProtocolosModel();

        $dadosProtocolo = $protocolosModel->protocolosCadastradosEdital($secretaria, $edital, $cargo);
        


        //PEGAR TODOS PROTOCOLOS DO EDITAL
        foreach($dadosProtocolo as $protocolo){
            
            $idCandidato = $protocolo->fk_id_cadastrado;

            //LIMPA A CLASSIFICAÇÃO ANTERIOR DO CANDIDATO PARA O EDITAL E CARGO
            $classificacaoModel->where([
                'fk_id_candidato' => $idCandidato,
                'fk_id_edital' => $edital,
                'fk_id_cargo' => $cargo
            ])->delete();

            

            //IDENTIFICA AS EXPERIÊNCIAS DO CANDIDATO
            $experienciaCandidato = $experienciasModel->buscarExperienciasParaClassificacao($idCandidato, $edital, $cargo);
            
            
            foreach($experienciaCandidato as $experiencia){
                
                if($experiencia->ds_status == 0){ //status = 0 significa que a experiência é classificatória, ou seja, tem pontuação definida no edital
                    $totalPontos = $experiencia->ds_quantidade * $experiencia->ds_multiplicador;
                    if($totalPontos > $experiencia->ds_quantidade_maxima){
                        $totalPontos = $experiencia->ds_quantidade_maxima;
                    }
                    $classificacaoModel->insert([
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
            $escolaridadeCandidato = $escolaridadesModel->buscarEscolaridadesParaClassificacao($idCandidato, $edital, $cargo);
            
            foreach($escolaridadeCandidato as $escolaridade){

                if($escolaridade->ds_status == 0){ //status = 0 significa que a escolaridade é classificatória, ou seja, tem pontuação definida no edital
                    $totalPontos = $escolaridade->ds_quantidade * $escolaridade->ds_multiplicador;
                    if($totalPontos > $escolaridade->ds_pontuacao_maxima){
                        $totalPontos = $escolaridade->ds_pontuacao_maxima;
                    }
                    $classificacaoModel->insert([
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
            $aperfeicoamentoCandidato = $aperfeicoamentosModel->buscarAperfeicoamentosParaClassificacao($idCandidato, $edital, $cargo);
            foreach($aperfeicoamentoCandidato as $aperfeicoamento){
                
                if($aperfeicoamento->ds_status == 0){ //status = 0 significa que a escolaridade é classificatória, ou seja, tem pontuação definida no edital
                    $totalPontos = $aperfeicoamento->ds_quantidade * $aperfeicoamento->ds_multiplicador;
                    if($totalPontos > $aperfeicoamento->ds_pontuacao_maxima){
                        $totalPontos = $aperfeicoamento->ds_pontuacao_maxima;
                    }
                    $classificacaoModel->insert([
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
        $service = new \App\Services\Classificacao\ClassificacaoService();
        $service->reprocessar((int)$edital, (int)$cargo);

        return redirect()->back()->with('success', 'Ranking reprocessado com sucesso.');
    }

    public function exportarXlsx($edital, $cargo)
    {
        $db = db_connect();

        $classificacoes = $db->table('tb_classificacao')
            ->where([
                'fk_id_edital' => (int) $edital,
                'fk_id_cargo'  => (int) $cargo,
            ])
            ->orderBy('ds_posicao', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($classificacoes)) {
            return redirect()->back()
                ->with('error', 'A classificação está vazia. Execute o reprocessamento antes de exportar.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Classificação');

        $headers = [
            'Posição',
            'Nome do Candidato',
            'Cargo',
            'Experiências',
            'Graduação',
            'Pós-Graduação',
            'Mestrado',
            'Doutorado',
            'Aperfeiçoamentos',
            'Total de Pontos',
            'Possui PNE',
            'Data de Processamento',
        ];

        $col = 1;
        foreach ($headers as $header) {
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
            $cell = $sheet->getCell($cellCoord);
            $cell->setValue($header);
            $cell->getStyle()->getFont()->setBold(true);
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('B4C7E7');
            $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $col++;
        }

        $row = 2;
        foreach ($classificacoes as $classificacao) {
            $dadosLinha = [
                $classificacao['ds_posicao'],
                $classificacao['ds_nome_candidato'],
                $classificacao['ds_nome_cargo'],
                $classificacao['nr_total_experiencias'],
                $classificacao['nr_total_graduacao'],
                $classificacao['nr_total_posgraduacao'],
                $classificacao['nr_total_mestrado'],
                $classificacao['nr_total_doutorado'],
                $classificacao['nr_total_aperfeicoamentos'],
                $classificacao['nr_total_pontos'],
                $classificacao['ds_possui_pne'] ? 'Sim' : 'Não',
                $classificacao['dt_processamento'],
            ];

            for ($c = 0; $c < count($dadosLinha); $c++) {
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c + 1) . $row;
                $sheet->setCellValue($cellCoord, $dadosLinha[$c]);
                $sheet->getCell($cellCoord)->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getCell($cellCoord)->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            $row++;
        }

        foreach (range(1, 12) as $colIndex) {
            $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
        }

        $editaisModel = new EditaisModel();
        $cargosModel  = new CargosModel();

        $editalInfo = $editaisModel->find($edital);
        $cargoInfo  = $cargosModel->find($cargo);

        $nomeEdital = $editalInfo->ds_numero_edital ?? 'edital';
        $nomeCargo  = $cargoInfo->ds_nome_cargo   ?? 'cargo';

        $safeEdital = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeEdital);
        $safeCargo  = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeCargo);
        $fileName   = "Classificacao_{$safeEdital}_{$safeCargo}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function gerarPdf($edital, $cargo){
        $service = new ClassificacaoService();
        $classificacoes = $service->listarClassificacao((int)$edital, (int)$cargo);

        if (empty($classificacoes)) {
            return redirect()->back()
                ->with('error', 'A classificação está vazia. Execute o reprocessamento antes de gerar o PDF.');
        }

        $editaisModel = new EditaisModel();
        $cargosModel  = new CargosModel();

        $editalInfo = $editaisModel->find($edital);
        $cargoInfo  = $cargosModel->find($cargo);

        $nomeEdital = $editalInfo->ds_numero_edital ?? 'edital';
        $nomeCargo  = $cargoInfo->ds_nome_cargo   ?? 'cargo';

        $dados = [
            'classificacoes' => $classificacoes,
            'nomeEdital'     => $nomeEdital,
            'nomeCargo'      => $nomeCargo,
            'dataGeracao'    => date('d/m/Y H:i:s'),
        ];

        $dompdf = new Dompdf();
        imprimir($dompdf, 'ClassificacaoPorCargo', $dados);
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Classificação');

        $editaisModel = new EditaisModel();
        $cargosModel  = new CargosModel();
        $editalInfo = $editaisModel->find($edital);
        $cargoInfo  = $cargosModel->find($cargo);

        $nomeEdital = $editalInfo->ds_numero_edital ?? 'edital';
        $nomeCargo  = $cargoInfo->ds_nome_cargo   ?? 'cargo';

        // Linha 1: Título institucional
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'PREFEITURA MUNICIPAL DE DIVINÓPOLIS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E78');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Linha 2: Edital e Cargo
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', "Edital: {$nomeEdital}  |  Cargo: {$nomeCargo}");
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('305496');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Linha 3: Data de geração
        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'Classificação gerada em ' . date('d/m/Y H:i:s'));
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('444444'));
        $sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // Linha 5: Cabeçalho da tabela
        $linhaCabecalho = 5;
        $col = 1;
        foreach ($titulos as $header) {
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $linhaCabecalho;
            $sheet->setCellValue($cellCoord, $header);
            $sheet->getStyle($cellCoord)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('305496');
            $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D9D9D9');
            $sheet->getRowDimension($linhaCabecalho)->setRowHeight(22);
            $col++;
        }

        // Dados
        $row = $linhaCabecalho + 1;
        foreach ($dados as $linha) {
            $col = 1;
            foreach ($titulos as $header) {
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                $valor = $linha[$header] ?? '';
                $sheet->setCellValue($cellCoord, $valor);
                $sheet->getStyle($cellCoord)->getFont()->setSize(11);
                $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D9D9D9');

                // Zebra striping
                if ($row % 2 == 0) {
                    $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');
                } else {
                    $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
                }

                $col++;
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // Auto-size nas colunas usadas
        $totalCols = count($titulos);
        foreach (range(1, $totalCols) as $colIndex) {
            $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
        }

        // Nome do arquivo
        $safeEdital = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeEdital);
        $safeCargo  = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeCargo);
        $fileName   = "Classificacao_{$safeEdital}_{$safeCargo}_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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