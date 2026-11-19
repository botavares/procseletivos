<?php

declare(strict_types=1);

namespace Tests\Admin\Services\ProcessosConvocacao;

use App\Models\Admin\ProcessosConvocacaoModel;
use App\Models\Common\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;
use App\Services\Admin\ProcessosConvocacao\ProcessosConvocacaoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosConvocacaoServiceTest extends CIUnitTestCase
{
    private ProcessosConvocacaoModel $modelMock;
    private ProcessosSeletivosModel $processosModelMock;
    private LogsService $logsServiceMock;
    private ProcessosConvocacaoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->modelMock         = $this->createMock(ProcessosConvocacaoModel::class);
        $this->processosModelMock = $this->createMock(ProcessosSeletivosModel::class);
        $this->logsServiceMock    = $this->createMock(LogsService::class);

        $this->service = new ProcessosConvocacaoService(
            $this->modelMock,
            $this->processosModelMock,
            $this->logsServiceMock
        );
    }

    public function testListarPorProcessoDelegaParaModel(): void
    {
        $esperado = [
            ['pk_id_convocacao' => 1, 'nm_numero_convocacao' => 1, 'nm_ano_convocacao' => 2026],
        ];

        $this->modelMock->method('listarConvocacaoProcesso')
            ->with(123)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorProcesso(123);

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarPorIdRetornaPrimeiroRegistro(): void
    {
        $esperado = [
            'pk_id_convocacao'     => 5,
            'fk_id_processo'       => 10,
            'fk_id_cargo'          => 20,
            'nm_numero_convocacao' => 2,
            'nm_ano_convocacao'    => 2026,
        ];

        $this->modelMock->method('listarConvocacaoProcesso')
            ->with(0, 5)
            ->willReturn([$esperado]);

        $resultado = $this->service->buscarPorId(5);

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarPorIdRetornaNullQuandoNaoEncontra(): void
    {
        $this->modelMock->method('listarConvocacaoProcesso')
            ->with(0, 999)
            ->willReturn([]);

        $this->assertNull($this->service->buscarPorId(999));
    }
}
