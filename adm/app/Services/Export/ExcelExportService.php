<?php

declare(strict_types=1);

namespace App\Services\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Service especializado em exportação para Excel (.xlsx).
 *
 * Centraliza toda a lógica de criação de planilhas, estilização,
 * cabeçalho institucional e stream de download.
 */
class ExcelExportService implements ExportadorInterface
{
    /**
     * {@inheritDoc}
     *
     * Estrutura esperada em $config:
     * - 'nomeArquivo'     => string (obrigatório)
     * - 'tituloAba'       => string (opcional, padrão 'Exportação')
     * - 'headers'         => array  (obrigatório)
     * - 'linhas'          => array  (obrigatório) – array de arrays associativos
     * - 'tituloInstitucional' => string (opcional)
     * - 'subtitulo'       => string (opcional)
     * - 'dataGeracao'     => string (opcional, padrão agora)
     */
    public function exportar(array $dados, array $config): void
    {
        $nomeArquivo      = $config['nomeArquivo'] ?? 'exportacao_' . date('Ymd_His') . '.xlsx';
        $tituloAba        = $config['tituloAba'] ?? 'Exportação';
        $headers          = $config['headers'] ?? [];
        $linhas           = $config['linhas'] ?? [];
        $tituloInstitucional = $config['tituloInstitucional'] ?? 'PREFEITURA MUNICIPAL DE DIVINÓPOLIS';
        $subtitulo        = $config['subtitulo'] ?? '';
        $dataGeracao      = $config['dataGeracao'] ?? 'Gerada em ' . date('d/m/Y H:i:s');

        if (empty($headers)) {
            throw new \InvalidArgumentException('Configuração de exportação Excel deve conter "headers".');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($tituloAba);

        $totalCols = count($headers);
        $ultimaColuna = Coordinate::stringFromColumnIndex($totalCols);

        // ====== CABEÇALHO INSTITUCIONAL ======
        $sheet->mergeCells("A1:{$ultimaColuna}1");
        $sheet->setCellValue('A1', $tituloInstitucional);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E78');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells("A2:{$ultimaColuna}2");
        $sheet->setCellValue('A2', $subtitulo);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('305496');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $sheet->mergeCells("A3:{$ultimaColuna}3");
        $sheet->setCellValue('A3', $dataGeracao);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('444444'));
        $sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // ====== CABEÇALHO DA TABELA (linha 5) ======
        $linhaCabecalho = 5;
        $col = 1;
        foreach ($headers as $header) {
            $cellCoord = Coordinate::stringFromColumnIndex($col) . $linhaCabecalho;
            $sheet->setCellValue($cellCoord, $header);
            $sheet->getStyle($cellCoord)->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFF'));
            $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('305496');
            $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D9D9D9');
            $col++;
        }
        $sheet->getRowDimension($linhaCabecalho)->setRowHeight(22);

        // ====== DADOS ======
        $row = $linhaCabecalho + 1;
        foreach ($linhas as $linha) {
            $col = 1;
            foreach ($headers as $header) {
                $cellCoord = Coordinate::stringFromColumnIndex($col) . $row;
                $valor = $linha[$header] ?? '';
                $sheet->setCellValue($cellCoord, $valor);
                $this->aplicarEstiloCelula($sheet, $cellCoord, $row);
                $col++;
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // Auto-size
        foreach (range(1, $totalCols) as $colIndex) {
            $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
        }

        // Headers HTTP
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$nomeArquivo}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Exporta uma lista de candidatos para Excel.
     *
     * @param array $candidatos Array de objetos/stdClass com dados dos candidatos.
     * @param array $metadados  ['edital' => string, 'cargo' => string, 'somentePcd' => bool]
     */
    public function exportarCandidatos(array $candidatos, array $metadados): void
    {
        $nomeEdital = $metadados['edital'] ?? 'edital';
        $nomeCargo  = $metadados['cargo']  ?? 'cargo';
        $somentePcd = $metadados['somentePcd'] ?? false;

        $safeEdital = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeEdital);
        $safeCargo  = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeCargo);
        $sufixoPcd  = $somentePcd ? '_PCD' : '';
        $nomeArquivo = "Candidatos{$sufixoPcd}_{$safeEdital}_{$safeCargo}_" . date('Ymd_His') . '.xlsx';

        $headers = ['Data de Insc.', 'Nome do Candidato', 'Nascimento', 'Telefone', 'Email'];
        $linhas = [];

        foreach ($candidatos as $c) {
            $linhas[] = [
                'Data de Insc.'       => date('d/m/Y', strtotime($c->ds_data_cadastro ?? '')),
                'Nome do Candidato'   => $c->ds_nome ?? '',
                'Nascimento'          => date('d/m/Y', strtotime($c->ds_nascimento ?? '')),
                'Telefone'            => mask($c->ds_celular ?? '', '(##) #####-####'),
                'Email'               => $c->ds_email ?? '',
            ];
        }

        $this->exportar($linhas, [
            'nomeArquivo'         => $nomeArquivo,
            'tituloAba'           => 'Candidatos',
            'headers'             => $headers,
            'linhas'              => $linhas,
            'subtitulo'           => "Edital: {$nomeEdital}  |  Cargo: {$nomeCargo}",
            'dataGeracao'         => 'Listagem gerada em ' . date('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Exporta classificação para Excel com colunas dinâmicas ou desempate.
     */
    public function exportarClassificacao(
        array $classificacoes,
        array $metadados,
        array $configDesempate,
        array $colunasDinamicas,
        array $dadosDinamicos,
        bool $usaDesempateDinamico,
        bool $somentePcd = false
    ): void {
        $nomeEdital = $metadados['edital'] ?? 'edital';
        $nomeCargo  = $metadados['cargo']  ?? 'cargo';

        $safeEdital = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeEdital);
        $safeCargo  = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeCargo);
        $sufixoPcd  = $somentePcd ? '_PCD' : '';
        $nomeArquivo = "Classificacao{$sufixoPcd}_{$safeEdital}_{$safeCargo}_" . date('Ymd_His') . '.xlsx';

        $headers = ['Posição', 'Candidato'];

        if ($usaDesempateDinamico) {
            foreach ($configDesempate as $config) {
                $headers[] = $config->descricao ?: $config->tipoCriterio;
            }
            $headers[] = 'Nascimento';
            $headers[] = 'Total de Pontos';
        } else {
            foreach ($colunasDinamicas as $col) {
                if ($col['tipo'] !== 'fixo') {
                    $headers[] = $col['label'];
                }
            }
            $headers[] = 'Nascimento';
            $headers[] = 'Total de Pontos';
        }
        $headers[] = 'PCD';

        $linhas = [];
        foreach ($classificacoes as $classificacao) {
            $linha = [];
            $linha['Posição']        = $classificacao['ds_posicao'] ?? '';
            $linha['Candidato']      = $classificacao['ds_nome_candidato'] ?? '';

            if ($usaDesempateDinamico) {
                foreach ($configDesempate as $config) {
                    $chave = method_exists($config, 'chaveScore') ? $config->chaveScore() : ($config->chave ?? '');
                    $scoreData = $classificacao['_scores'][$chave] ?? null;
                    $valor = is_array($scoreData) ? ($scoreData['nr_valor'] ?? 0) : ($scoreData ?? 0);
                    $colName = $config->descricao ?: $config->tipoCriterio;
                    $linha[$colName] = is_numeric($valor) ? number_format((float)$valor, 2, ',', '.') : esc($valor);
                }
                $linha['Nascimento']      = date('d/m/Y', strtotime($classificacao['dt_nascimento'] ?? ''));
                $linha['Total de Pontos'] = $classificacao['nr_total_pontos'] ?? 0;
            } else {
                $candDados = $dadosDinamicos[$classificacao['fk_id_candidato']] ?? [];
                foreach ($colunasDinamicas as $coluna) {
                    if ($coluna['tipo'] !== 'fixo') {
                        $valor = $candDados[$coluna['chave']] ?? 0;
                        $linha[$coluna['label']] = is_numeric($valor) ? number_format((float)$valor, 2, ',', '.') : esc($valor);
                    }
                }
                $linha['Nascimento']      = date('d/m/Y', strtotime($classificacao['dt_nascimento'] ?? ''));
                $linha['Total de Pontos'] = $classificacao['nr_total_pontos'] ?? 0;
            }

            $linha['PCD'] = (($classificacao['ds_possui_pne'] ?? 0) == 1) ? 'SIM' : 'NÃO';
            $linhas[] = $linha;
        }

        $this->exportar($linhas, [
            'nomeArquivo'         => $nomeArquivo,
            'tituloAba'           => 'Classificação',
            'headers'             => $headers,
            'linhas'              => $linhas,
            'subtitulo'           => "Edital: {$nomeEdital}  |  Cargo: {$nomeCargo}",
            'dataGeracao'         => 'Classificação gerada em ' . date('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Exporta uma tabela genérica (dados + títulos via POST).
     */
    public function exportarTabela(array $dados, array $titulos, array $metadados): void
    {
        $nomeEdital = $metadados['edital'] ?? 'edital';
        $nomeCargo  = $metadados['cargo']  ?? 'cargo';

        $safeEdital = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeEdital);
        $safeCargo  = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeCargo);
        $nomeArquivo = "Classificacao_{$safeEdital}_{$safeCargo}_" . date('Ymd_His') . '.xlsx';

        $linhas = [];
        foreach ($dados as $linhaDados) {
            $linha = [];
            foreach ($titulos as $titulo) {
                $linha[$titulo] = $linhaDados[$titulo] ?? '';
            }
            $linhas[] = $linha;
        }

        $this->exportar($linhas, [
            'nomeArquivo'         => $nomeArquivo,
            'tituloAba'           => 'Classificação',
            'headers'             => $titulos,
            'linhas'              => $linhas,
            'subtitulo'           => "Edital: {$nomeEdital}  |  Cargo: {$nomeCargo}",
            'dataGeracao'         => 'Classificação gerada em ' . date('d/m/Y H:i:s'),
        ]);
    }

    private function aplicarEstiloCelula(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $cellCoord, int $row, string $horizontalAlign = Alignment::HORIZONTAL_CENTER): void
    {
        $sheet->getStyle($cellCoord)->getFont()->setSize(11);
        $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal($horizontalAlign)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D9D9D9');

        if ($row % 2 == 0) {
            $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');
        } else {
            $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
        }
    }
}
