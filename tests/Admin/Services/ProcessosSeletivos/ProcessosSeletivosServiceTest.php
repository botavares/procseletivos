<?php

declare(strict_types=1);

namespace Tests\Admin\Services\ProcessosSeletivos;

use App\DTOs\Admin\ProcessosSeletivosDTO;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;
use App\Services\Admin\ProcessosSeletivos\ProcessosSeletivosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosSeletivosServiceTest extends CIUnitTestCase
{
    private ProcessosSeletivosService $service;
    private $modelMock;
    private $logsMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->modelMock = $this->createMock(ProcessosSeletivosModel::class);
        $this->logsMock  = $this->createMock(LogsService::class);
        $this->service   = new ProcessosSeletivosService($this->modelMock, $this->logsMock);
    }

    public function testCriarProcesso(): void
    {
        $dto = $this->criarDto();

        $this->modelMock->method('insert')
            ->with($dto->toArray())
            ->willReturn(42);

        $this->logsMock->method('inserirLog')
            ->with('Criar', $this->anything(), 'tb_processos');

        $id = $this->service->criar($dto);

        $this->assertSame(42, $id);
    }

    public function testAtualizarProcesso(): void
    {
        $dto = $this->criarDto(id: 1);
        $anterior = (object) [
            'pk_id_processo'        => 1,
            'fk_id_secretaria'      => 1,
            'nm_numero_processo'    => 99999,
            'nm_ano_processo'     => 2025,
            'ds_objeto'           => 'Objeto Antigo',
            'nm_situacao'         => 1,
            'dt_inicio_inscricao' => '2025-01-01',
            'dt_fim_inscricao'    => '2025-02-01',
            'dt_divulga_resultado' => null,
            'nm_meses_validade'   => 6,
            'nm_prorrogado'       => 0,
        ];

        $this->modelMock->method('find')
            ->with(1)
            ->willReturn($anterior);

        $this->modelMock->method('update')
            ->with(1, $dto->toArray())
            ->willReturn(true);

        $this->logsMock->method('inserirLog')
            ->with('Alterar', $this->stringContains('Campos:'), 'tb_processos');

        $resultado = $this->service->atualizar(1, $dto);

        $this->assertTrue($resultado);
    }

    public function testDeletarProcesso(): void
    {
        $this->modelMock->method('delete')
            ->with(1)
            ->willReturn(true);

        $this->logsMock->method('inserirLog')
            ->with('Deletar', $this->anything(), 'tb_processos');

        $resultado = $this->service->deletar(1);

        $this->assertTrue($resultado);
    }

    public function testListarPorSecretaria(): void
    {
        $esperado = [(object) ['pk_id_processo' => 1]];
        $this->modelMock->method('porSecretaria')
            ->with(5)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorSecretaria(5);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorAno(): void
    {
        $esperado = [(object) ['pk_id_processo' => 1]];
        $this->modelMock->method('porAno')
            ->with(2026)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorAno(2026);

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarPorNumeroEAno(): void
    {
        $esperado = (object) ['pk_id_processo' => 1];
        $this->modelMock->method('porNumeroEAno')
            ->with(123, 2026)
            ->willReturn($esperado);

        $resultado = $this->service->buscarPorNumeroEAno(123, 2026);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorSituacao(): void
    {
        $esperado = [(object) ['pk_id_processo' => 1]];
        $this->modelMock->method('porSituacao')
            ->with(2)
            ->willReturn($esperado);

        $resultado = $this->service->listarPorSituacao(2);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorFaixaDeTempo(): void
    {
        $esperado = [(object) ['pk_id_processo' => 1]];
        $this->modelMock->method('porFaixaDeTempo')
            ->with('2026-01-01', '2026-12-31')
            ->willReturn($esperado);

        $resultado = $this->service->listarPorFaixaDeTempo('2026-01-01', '2026-12-31');

        $this->assertSame($esperado, $resultado);
    }

    private function criarDto(?int $id = null): ProcessosSeletivosDTO
    {
        return new ProcessosSeletivosDTO(
            idProcesso: $id,
            idSecretaria: 1,
            numeroProcesso: 12345,
            anoProcesso: 2026,
            objeto: 'Objeto Teste',
            situacao: 1,
            inicioInscricao: '2026-01-01',
            fimInscricao: '2026-02-01',
            divulgaResultado: null,
            mesesValidade: 12,
            prorrogado: 0,
        );
    }
}
