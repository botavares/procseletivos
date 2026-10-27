<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

readonly class ProcessosDocumentosDTO
{
    public function __construct(
        public ?int $idDocumento,
        public int $processoReferencia,
        public int $tipo,
        public int $numeroDocumento,
        public int $anoDocumento,
        public string $codigoOficial,
        public string $link,
        public string $arquivo,
        public int $versaoVigente,
        public int $documentoRetificado,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            idDocumento: isset($data['pk_id_documento']) ? (int) $data['pk_id_documento'] : null,
            processoReferencia: (int) ($data['fk_processo_referencia'] ?? 0),
            tipo: (int) ($data['nm_tipo'] ?? 0),
            numeroDocumento: (int) ($data['nm_numero_documento'] ?? 0),
            anoDocumento: (int) ($data['nm_ano_documento'] ?? 0),
            codigoOficial: (string) ($data['ds_codigo_oficial'] ?? ''),
            link: (string) ($data['ds_link'] ?? ''),
            arquivo: (string) ($data['ds_arquivo'] ?? ''),
            versaoVigente: (int) ($data['nm_versao_vigente'] ?? 0),
            documentoRetificado: (int) ($data['nm_documento_retificado'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'pk_id_documento'         => $this->idDocumento,
            'fk_processo_referencia'  => $this->processoReferencia,
            'nm_tipo'                 => $this->tipo,
            'nm_numero_documento'     => $this->numeroDocumento,
            'nm_ano_documento'        => $this->anoDocumento,
            'ds_codigo_oficial'       => $this->codigoOficial,
            'ds_link'                 => $this->link,
            'ds_arquivo'              => $this->arquivo,
            'nm_versao_vigente'       => $this->versaoVigente,
            'nm_documento_retificado' => $this->documentoRetificado,
        ];
    }
}
