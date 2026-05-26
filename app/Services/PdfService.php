<?php
namespace App\Services;
use Dompdf\Dompdf;
use Dompdf\Options;
class PdfService
{
    protected Dompdf $dompdf;
    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $this->dompdf = new Dompdf($options);
    }
    /**
     * Gera comprovante de inscrição
     */
    public function gerarComprovante(array $dados): string{
        
        $html = view('impressos/comprovante', $dados);
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        return $this->dompdf->output();
    }


    /**
     * Retorna descrição da deficiência
     */
    public function formatarDeficiencia(?int $idPne, ?string $outraPne): string
    {
        $deficiencias = [
            1 => 'não possui deficiência',
            2 => 'possui deficiência auditiva',
            3 => 'possui deficiência física',
            4 => 'possui deficiência múltipla',
            5 => 'possui deficiência mental',
            6 => 'possui deficiência reabilitado',
            7 => 'possui deficiência visual',
            8 => 'possui deficiência: ' . $outraPne,
        ];
        return $deficiencias[$idPne] ?? 'não informado';
    }
}