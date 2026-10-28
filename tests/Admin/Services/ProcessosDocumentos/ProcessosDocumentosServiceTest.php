<?php

declare(strict_types=1);

namespace Tests\Admin\Services\ProcessosDocumentos;

use App\DTOs\Admin\ProcessosDocumentosDTO;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Services\Admin\LogsService;
use App\Services\ProcessosDocumentos\ProcessosDocumentosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosDocumentosServiceTest extends CIUnitTestCase
{
    private ProcessosDocumentosService $service;
    private $modelMock;
    private $logsMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->modelMock = $this->createMock(ProcessosDocumentosModel::class);
        $this->logsMock  = $this->createMock(LogsService::class);
        $this->service   = new ProcessosDocumentosService($this->modelMock, $this->logsMock);
    }

    public function testCriarDocumento(): void
    {
        $dto = $this->criarDto();

        $this->modelMock->method('insert')
            ->with($dto->toArray())
            ->willReturn(42);

        $this->logsMock->method('inserirLog')
            ->with('Criar', $this->anything(), 'tb_processos_documentos');

        $id = $this->service->criar($dto);

        $this->assertSame(42, $id);
    }

    public function testAtualizarDocumento(): void
    {
        $dto = $this->criarDto(id: 1);
        $anterior = (object) [
            'pk_id_documento'         => 1,
            'fk_processo_referencia'  => 1,
            'nm_tipo'                 => 1,
            'nm_numero_documento'     => 100,
            'nm_ano_documento'        => 2025,
            'ds_codigo_oficial'       => 'COD-OLD',
            'ds_link'                 => '',
            'ds_arquivo'              => '',
            'nm_versao_vigente'       => 1,
            'nm_documento_retificado' => 0,
        ];

        $this->modelMock->method('find')
            ->with(1)
            ->willReturn($anterior);

        $this->modelMock->method('update')
            ->with(1, $dto->toArray())
            ->willReturn(true);

        $this->logsMock->method('inserirLog')
            ->with('Alterar', $this->stringContains('Campos:'), 'tb_processos_documentos');

        $resultado = $this->service->atualizar(1, $dto);

        $this->assertTrue($resultado);
    }

    public function testDeletarDocumento(): void
    {
        $this->modelMock->method('delete')
            ->with(1)
            ->willReturn(true);

        $this->logsMock->method('inserirLog')
            ->with('Deletar', $this->anything(), 'tb_processos_documentos');

        $resultado = $this->service->deletar(1);

        $this->assertTrue($resultado);
    }

    public function testBuscarPorId(): void
    {
        $esperado = (object) ['pk_id_documento' => 1];
        $this->modelMock->method('find')
            ->with(1)
            ->willReturn($esperado);

        $resultado = $this->service->buscarPorId(1);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorSecretaria(): void
    {
        $esperado = [(object) ['pk_id_documento' => 1]];
        $this->modelMock->method('porSecretaria')
            ->with(5)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorSecretaria(5);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorTipo(): void
    {
        $esperado = [(object) ['pk_id_documento' => 1]];
        $this->modelMock->method('porTipo')
            ->with(2)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorTipo(2);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorNumeroProcesso(): void
    {
        $esperado = [(object) ['pk_id_documento' => 1]];
        $this->modelMock->method('porNumeroProcesso')
            ->with(123, 2026)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorNumeroProcesso(123, 2026);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorNumeroDocumentoEAno(): void
    {
        $esperado = [(object) ['pk_id_documento' => 1]];
        $this->modelMock->method('porNumeroDocumentoEAno')
            ->with(456, 2026)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorNumeroDocumentoEAno(456, 2026);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorFaixaDeTempo(): void
    {
        $esperado = [(object) ['pk_id_documento' => 1]];
        $this->modelMock->method('porFaixaDeTempo')
            ->with('2026-01-01', '2026-12-31')
            ->willReturn($esperado);

        $resultado = $this->service->listarPorFaixaDeTempo('2026-01-01', '2026-12-31');

        $this->assertSame($esperado, $resultado);
    }

    private function criarDto(?int $id = null): ProcessosDocumentosDTO
    {
        return new ProcessosDocumentosDTO(
            idDocumento: $id,
            processoReferencia: 1,
            tipo: 1,
            numeroDocumento: 123,
            anoDocumento: 2026,
            codigoOficial: 'COD-001',
            link: 'https://exemplo.com',
            arquivo: 'doc.pdf',
            versaoVigente: 1,
            documentoRetificado: 0,
            dataPublicacao: '2026-10-28 10:00:00',
        );
    }
}
