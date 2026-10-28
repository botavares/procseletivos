<?php

declare(strict_types=1);

namespace Tests\Admin\DTOs;

use App\DTOs\Admin\ProcessosDocumentosDTO;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosDocumentosDTOTest extends CIUnitTestCase
{
    public function testFromArrayMapeiaCamposCorretamente(): void
    {
        $data = [
            'pk_id_documento'         => 10,
            'fk_processo_referencia'  => 5,
            'nm_tipo'                 => 1,
            'nm_numero_documento'     => 123,
            'nm_ano_documento'        => 2026,
            'ds_codigo_oficial'       => 'COD-001',
            'ds_link'                 => 'https://exemplo.com',
            'ds_arquivo'              => 'arquivo.pdf',
            'nm_versao_vigente'       => 2,
            'nm_documento_retificado' => 0,
            'dt_data_publicacao'      => '2026-10-28 10:00:00',
        ];

        $dto = ProcessosDocumentosDTO::fromArray($data);

        $this->assertSame(10, $dto->idDocumento);
        $this->assertSame(5, $dto->processoReferencia);
        $this->assertSame(1, $dto->tipo);
        $this->assertSame(123, $dto->numeroDocumento);
        $this->assertSame(2026, $dto->anoDocumento);
        $this->assertSame('COD-001', $dto->codigoOficial);
        $this->assertSame('https://exemplo.com', $dto->link);
        $this->assertSame('arquivo.pdf', $dto->arquivo);
        $this->assertSame(2, $dto->versaoVigente);
        $this->assertSame(0, $dto->documentoRetificado);
        $this->assertSame('2026-10-28 10:00:00', $dto->dataPublicacao);
    }

    public function testToArrayRetornaEstruturaCorreta(): void
    {
        $dto = new ProcessosDocumentosDTO(
            idDocumento: null,
            processoReferencia: 1,
            tipo: 1,
            numeroDocumento: 100,
            anoDocumento: 2026,
            codigoOficial: 'COD-TEST',
            link: '',
            arquivo: '',
            versaoVigente: 1,
            documentoRetificado: 0,
            dataPublicacao: '2026-10-28 10:00:00',
        );

        $array = $dto->toArray();

        $this->assertArrayHasKey('pk_id_documento', $array);
        $this->assertArrayHasKey('fk_processo_referencia', $array);
        $this->assertArrayHasKey('nm_tipo', $array);
        $this->assertArrayHasKey('dt_data_publicacao', $array);
        $this->assertNull($array['pk_id_documento']);
        $this->assertSame(1, $array['fk_processo_referencia']);
        $this->assertSame('2026-10-28 10:00:00', $array['dt_data_publicacao']);
    }

    public function testFromArraySemIdDocumento(): void
    {
        $dto = ProcessosDocumentosDTO::fromArray([
            'fk_processo_referencia' => 1,
            'nm_tipo' => 2,
            'nm_numero_documento' => 1,
            'nm_ano_documento' => 2026,
            'ds_codigo_oficial' => 'X',
        ]);

        $this->assertNull($dto->idDocumento);
    }
}
