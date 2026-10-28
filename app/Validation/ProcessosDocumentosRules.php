<?php

declare(strict_types=1);

namespace App\Validation;

use App\Enums\Admin\TiposDocumentosEnum;

class ProcessosDocumentosRules
{
    /**
     * Verifica se o valor contém apenas números.
     */
    public function apenas_numeros(?string $value = null, ?string $params = null, array $data = []): bool
    {
        if ($value === null || $value === '') {
            return false;
        }
        return ctype_digit($value);
    }

    /**
     * Verifica se o ano é igual ou maior que o ano atual.
     */
    public function ano_atual_ou_futuro(?string $value = null, ?string $params = null, array $data = []): bool
    {
        if ($value === null || $value === '') {
            return false;
        }
        if (!ctype_digit($value)) {
            return false;
        }
        $anoInformado = (int) $value;
        $anoAtual     = (int) date('Y');
        return $anoInformado >= $anoAtual;
    }

    /**
     * Arquivo é obrigatório para EDITAL_ABERTURA, RETIFICACAO e REPUBLICACAO.
     * Deve ser PDF.
     */
    public function arquivo_pdf_condicional(?string $value = null, ?string $params = null, array $data = []): bool
    {
        $tipo = isset($data['nm_tipo']) ? (int) $data['nm_tipo'] : 0;

        $tiposComArquivoObrigatorio = [
            TiposDocumentosEnum::EDITAL_ABERTURA->value,
            TiposDocumentosEnum::RETIFICACAO->value,
            TiposDocumentosEnum::REPUBLICACAO->value,
        ];

        // Se não é um tipo com arquivo obrigatório, o arquivo deve estar vazio
        if (!in_array($tipo, $tiposComArquivoObrigatorio, true)) {
            return $value === null || $value === '';
        }

        // Se é um tipo com arquivo obrigatório e o arquivo está vazio, é inválido
        if ($value === null || $value === '') {
            return false;
        }

        // Deve terminar com .pdf
        return strtolower(pathinfo($value, PATHINFO_EXTENSION)) === 'pdf';
    }

    /**
     * Versão vigente não pode ser preenchida se o tipo for RETIFICACAO.
     */
    public function versao_vigente_regra(?string $value = null, ?string $params = null, array $data = []): bool
    {
        $tipo = isset($data['nm_tipo']) ? (int) $data['nm_tipo'] : 0;

        if ($tipo === TiposDocumentosEnum::RETIFICACAO->value) {
            return $value === null || $value === '' || (int) $value === 0;
        }

        return true;
    }

    /**
     * Documento retificado só pode ter valor se o tipo for RETIFICACAO.
     */
    public function documento_retificado_regra(?string $value = null, ?string $params = null, array $data = []): bool
    {
        $tipo = isset($data['nm_tipo']) ? (int) $data['nm_tipo'] : 0;

        if ($tipo !== TiposDocumentosEnum::RETIFICACAO->value) {
            return $value === null || $value === '' || (int) $value === 0;
        }

        return true;
    }
}
