<?php

declare(strict_types=1);

namespace App\Services\Admin\Export;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Service para geração de QR Codes.
 *
 * Embora ainda não utilizado ativamente nos controllers Admin,
 * centraliza a lógica de configuração e renderização de QR Codes
 * para futuras funcionalidades (ex.: comprovantes digitais,
 * validação de documentos, etc.).
 */
class QrCodeService
{
    /**
     * Gera um QR Code em formato base64 (imagem PNG inline)
     * ou salva em arquivo, conforme configuração.
     *
     * @param string $dados   Texto/URL a ser codificado.
     * @param int    $tamanho Tamanho em pixels (padrão 300).
     * @param string $tipo    'base64' ou 'png'. Padrão 'base64'.
     *
     * @return string Caminho do arquivo ou string base64.
     */
    public function gerar(string $dados, int $tamanho = 300, string $tipo = 'base64'): string
    {
        $options = new QROptions([
            'outputType'   => $tipo === 'base64' ? QRCode::OUTPUT_IMAGE_PNG : QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'     => QRCode::ECC_L,
            'scale'        => 5,
            'imageBase64'  => $tipo === 'base64',
        ]);

        $qrcode = new QRCode($options);

        if ($tipo === 'base64') {
            return $qrcode->render($dados);
        }

        $caminho = WRITEPATH . 'qrcodes/' . uniqid('qr_') . '.png';
        if (!is_dir(dirname($caminho))) {
            mkdir(dirname($caminho), 0755, true);
        }
        $qrcode->render($dados, $caminho);
        return $caminho;
    }

    /**
     * Gera QR Code para um candidato/protocolo específico.
     *
     * @param int    $idCandidato
     * @param int    $idEdital
     * @param string $urlValidacao URL pública de validação
     *
     * @return string Base64 da imagem PNG.
     */
    public function gerarParaCandidato(int $idCandidato, int $idEdital, string $urlValidacao): string
    {
        $payload = json_encode([
            'candidato' => $idCandidato,
            'edital'    => $idEdital,
            'url'       => $urlValidacao,
            'gerado_em' => date('c'),
        ]);

        return $this->gerar($payload, 300, 'base64');
    }
}
