<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\IPdfService;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService implements IPdfService
{
    protected Dompdf $dompdf;

    public function __construct(?Options $options = null)
    {
        $options = $options ?? new Options();
        $options->set('defaultFont', 'Arial');
        $this->dompdf = new Dompdf($options);
    }

    /**
     * Gera comprovante de inscrição
     */
    public function gerarPdf(array $dados, string $documento): string
    {
        $html = view('Publica/impressos/' . $documento, $dados);

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        return $this->dompdf->output();
    }
}
