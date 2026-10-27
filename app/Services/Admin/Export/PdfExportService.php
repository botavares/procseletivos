<?php

declare(strict_types=1);

namespace App\Services\Admin\Export;

use Dompdf\Dompdf;

/**
 * Service especializado em geração de PDF via Dompdf.
 *
 * Encapsula a renderização de views em PDF, numeração de páginas
 * e stream de download, eliminando a dependência direta dos controllers
 * ao helper global `imprimir()`.
 */
class PdfExportService implements ExportadorInterface
{
    /**
     * {@inheritDoc}
     *
     * Config esperada:
     * - 'view'       => string nome da view em impressos/
     * - 'dados'      => array  dados para a view
     * - 'nomeArquivo'=> string nome do arquivo PDF (sem extensão)
     */
    public function exportar(array $dados, array $config): void
    {
        $view        = $config['view'] ?? '';
        $dadosView   = $config['dados'] ?? [];
        $nomeArquivo = $config['nomeArquivo'] ?? 'documento';

        if (empty($view)) {
            throw new \InvalidArgumentException('Configuração de exportação PDF deve conter "view".');
        }

        $dompdf = new Dompdf();
        $html = view('impressos/' . $view, $dadosView);
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Numeração de página no rodapé
        $canvas = $dompdf->getCanvas();
        $font   = $dompdf->getFontMetrics()->getFont('Helvetica');
        $size   = 8;
        $color  = [0, 0, 0];
        $width  = $canvas->get_width();
        $height = $canvas->get_height();
        $text   = "Página {PAGE_NUM} de {PAGE_COUNT}";
        $canvas->page_text($width - 100, $height - 15, $text, $font, $size, $color);

        $dompdf->stream($nomeArquivo . '.pdf', ['Attachment' => false]);
        exit;
    }

    /**
     * Gera PDF a partir de uma view administrativa.
     *
     * @param string $view       Nome da view (ex.: 'ClassificacaoPorCargo')
     * @param array  $dados      Dados a serem passados para a view
     * @param string $nomeArquivo Nome do arquivo sem extensão
     */
    public function gerarPdf(string $view, array $dados, string $nomeArquivo): void
    {
        $this->exportar([], [
            'view'        => $view,
            'dados'       => $dados,
            'nomeArquivo' => $nomeArquivo,
        ]);
    }
}
