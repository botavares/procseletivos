<?php

declare(strict_types=1);

namespace Tests\Admin\Services\Convocados;

use App\Enums\SituacaoCandidatosEnum;
use App\Exceptions\Admin\ConvocacaoInvalidaException;
use App\Models\Admin\ConvocadosModel;
use App\Models\Admin\ProcessosConvocacaoModel;
use App\Models\Admin\SituacaoModel;
use App\Services\Admin\Convocados\ConvocadosService;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 */
final class ConvocadosServiceTest extends CIUnitTestCase
{
    private ConvocadosModel&MockObject $convocadosModelMock;
    private ProcessosConvocacaoModel&MockObject $processosModelMock;
    private SituacaoModel&MockObject $situacaoModelMock;
    private BaseConnection&MockObject $dbMock;
    private ConvocadosService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->convocadosModelMock = $this->getMockBuilder(ConvocadosModel::class)
            ->addMethods(['where', 'orderBy', 'whereIn'])
            ->onlyMethods(['first', 'findAll', 'update', 'insert', 'delete', 'countAllResults'])
            ->getMock();

        $this->processosModelMock = $this->getMockBuilder(ProcessosConvocacaoModel::class)
            ->addMethods(['where', 'orWhere', 'orderBy', 'orGroupStart', 'groupStart', 'groupEnd'])
            ->onlyMethods(['find', 'first'])
            ->getMock();

        foreach (['where', 'orWhere', 'orderBy', 'orGroupStart', 'groupStart', 'groupEnd'] as $method) {
            $this->processosModelMock->method($method)->willReturnSelf();
        }

        $this->convocadosModelMock->method('where')->willReturnSelf();
        $this->convocadosModelMock->method('orderBy')->willReturnSelf();
        $this->convocadosModelMock->method('whereIn')->willReturnSelf();

        $this->situacaoModelMock = $this->getMockBuilder(SituacaoModel::class)
            ->addMethods(['where', 'whereIn'])
            ->onlyMethods(['getSituacaoCandidatoCargoProcesso', 'salvar', 'atualizar', 'findAll'])
            ->getMock();
        $this->situacaoModelMock->method('where')->willReturnSelf();
        $this->situacaoModelMock->method('whereIn')->willReturnSelf();

        $this->dbMock = $this->getMockBuilder(BaseConnection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['transBegin', 'transStatus', 'transCommit', 'transRollback', 'error'])
            ->getMockForAbstractClass();

        $this->dbMock->method('transBegin')->willReturn(true);
        $this->dbMock->method('transStatus')->willReturn(true);
        $this->dbMock->method('transCommit')->willReturn(true);
        $this->dbMock->method('transRollback')->willReturn(true);

        $this->service = new ConvocadosService(
            $this->convocadosModelMock,
            $this->processosModelMock,
            $this->situacaoModelMock,
            $this->dbMock
        );
    }

    public function testBuscarConvocacaoAtualRetornaPrimeiraNaoDeletada(): void
    {
        $esperado = (object) [
            'pk_id_convocacao'     => 10,
            'fk_id_processo'       => 1,
            'fk_id_cargo'          => 2,
            'nm_ano_convocacao'    => 2026,
            'nm_numero_convocacao' => 3,
        ];

        $this->processosModelMock
            ->method('first')
            ->willReturn($esperado);

        $resultado = $this->service->buscarConvocacaoAtual(1, 2);

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarConvocacaoAnteriorRetornaNullQuandoNaoExisteAtual(): void
    {
        $this->processosModelMock
            ->method('find')
            ->with(99)
            ->willReturn(null);

        $this->assertNull($this->service->buscarConvocacaoAnterior(1, 2, 99));
    }

    public function testProcessarConvocacaoAnteriorNaoFazNadaSemAnterior(): void
    {
        $this->configurarConvocacaoAnterior(null);

        $this->situacaoModelMock->expects($this->never())->method('getSituacaoCandidatoCargoProcesso');

        $this->service->processarConvocacaoAnterior(1, 2, 10);
    }

    public function testProcessarConvocacaoAnteriorDesclassificaTitularNaoContratado(): void
    {
        $this->configurarConvocacaoAnterior(5);

        $this->convocadosModelMock
            ->method('where')
            ->willReturnSelf();
        $this->convocadosModelMock
            ->method('findAll')
            ->willReturnOnConsecutiveCalls(
                [(object) ['fk_id_candidato' => 1, 'nm_tipo' => 1]],
                []
            );

        $this->situacaoModelMock
            ->method('getSituacaoCandidatoCargoProcesso')
            ->willReturn([
                'pk_id_situacao' => 50,
                'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value,
            ]);

        $this->situacaoModelMock
            ->expects($this->once())
            ->method('atualizar')
            ->with(50, $this->callback(function (array $data): bool {
                return $data['nm_id_situacao'] === SituacaoCandidatosEnum::DESCLASSIFICADO->value
                    && $data['fk_id_candidato'] === 1
                    && $data['fk_id_cargo'] === 2
                    && $data['fk_id_processo'] === 1;
            }));

        $this->service->processarConvocacaoAnterior(1, 2, 10);
    }

    public function testProcessarConvocacaoAnteriorMantemTitularContratado(): void
    {
        $this->configurarConvocacaoAnterior(5);

        $this->convocadosModelMock
            ->method('where')
            ->willReturnSelf();
        $this->convocadosModelMock
            ->method('findAll')
            ->willReturnOnConsecutiveCalls(
                [(object) ['fk_id_candidato' => 1, 'nm_tipo' => 1]],
                []
            );

        $this->situacaoModelMock
            ->method('getSituacaoCandidatoCargoProcesso')
            ->willReturn([
                'pk_id_situacao' => 50,
                'nm_id_situacao' => SituacaoCandidatosEnum::CONTRATADO->value,
            ]);

        $this->situacaoModelMock->expects($this->never())->method('atualizar');
        $this->situacaoModelMock->expects($this->never())->method('salvar');

        $this->service->processarConvocacaoAnterior(1, 2, 10);
    }

    public function testProcessarConvocacaoAnteriorAguardaSuplenteNaoContratadoENaoConvocadoAtualmente(): void
    {
        $this->configurarConvocacaoAnterior(5);

        $this->convocadosModelMock
            ->method('where')
            ->willReturnSelf();
        $this->convocadosModelMock
            ->method('findAll')
            ->willReturnOnConsecutiveCalls(
                [(object) ['fk_id_candidato' => 2, 'nm_tipo' => 0]],
                []
            );

        $this->situacaoModelMock
            ->method('getSituacaoCandidatoCargoProcesso')
            ->willReturn([
                'pk_id_situacao' => 51,
                'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value,
            ]);

        $this->situacaoModelMock
            ->expects($this->once())
            ->method('atualizar')
            ->with(51, $this->callback(function (array $data): bool {
                return $data['nm_id_situacao'] === SituacaoCandidatosEnum::AGUARDANDO->value;
            }));

        $this->service->processarConvocacaoAnterior(1, 2, 10);
    }

    public function testProcessarConvocacaoAnteriorNaoAlteraSuplenteNaConvocacaoAtual(): void
    {
        $this->configurarConvocacaoAnterior(5);

        $this->convocadosModelMock
            ->method('where')
            ->willReturnSelf();
        $this->convocadosModelMock
            ->method('findAll')
            ->willReturnOnConsecutiveCalls(
                [(object) ['fk_id_candidato' => 2, 'nm_tipo' => 0]],
                [(object) ['fk_id_candidato' => 2, 'nm_tipo' => 0]]
            );

        $this->situacaoModelMock
            ->method('getSituacaoCandidatoCargoProcesso')
            ->willReturn([
                'pk_id_situacao' => 51,
                'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value,
            ]);

        $this->situacaoModelMock->expects($this->never())->method('atualizar');

        $this->service->processarConvocacaoAnterior(1, 2, 10);
    }

    public function testSalvarConvocadosSincronizaRegistros(): void
    {
        $this->configurarBuscarSituacoes(
            [1, 2, 3, 4],
            [
                1 => ['pk_id_situacao' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value],
                2 => ['pk_id_situacao' => 2, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value],
                3 => ['pk_id_situacao' => 3, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value],
                4 => ['pk_id_situacao' => 4, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value],
            ]
        );

        $this->convocadosModelMock
            ->method('findAll')
            ->willReturn([
                (object) ['pk_id_convocado' => 100, 'fk_id_convocacao' => 10, 'fk_id_candidato' => 4, 'nm_tipo' => 1],
            ]);

        $this->convocadosModelMock
            ->expects($this->once())
            ->method('delete')
            ->with(null, true)
            ->willReturn(true);

        $this->convocadosModelMock
            ->expects($this->exactly(3))
            ->method('insert')
            ->withConsecutive(
                [['fk_id_convocacao' => 10, 'fk_id_candidato' => 1, 'nm_tipo' => 1]],
                [['fk_id_convocacao' => 10, 'fk_id_candidato' => 2, 'nm_tipo' => 1]],
                [['fk_id_convocacao' => 10, 'fk_id_candidato' => 3, 'nm_tipo' => 0]],
            )
            ->willReturn(true);

        $this->situacaoModelMock
            ->expects($this->exactly(4))
            ->method('atualizar')
            ->withConsecutive(
                [4, ['fk_id_candidato' => 4, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value]],
                [1, ['fk_id_candidato' => 1, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value]],
                [2, ['fk_id_candidato' => 2, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value]],
                [3, ['fk_id_candidato' => 3, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value]],
            )
            ->willReturn(true);

        $this->service->salvarConvocados(10, [1, 2], [3], 1, 2);
    }

    public function testSalvarConvocadosAtualizaTipoQuandoCandidatoJaExiste(): void
    {
        $this->configurarBuscarSituacoes(
            [1],
            [1 => ['pk_id_situacao' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value]]
        );

        $this->convocadosModelMock
            ->method('findAll')
            ->willReturn([
                (object) ['pk_id_convocado' => 100, 'fk_id_convocacao' => 10, 'fk_id_candidato' => 1, 'nm_tipo' => 0],
            ]);

        $this->convocadosModelMock
            ->expects($this->once())
            ->method('update')
            ->with(100, ['nm_tipo' => 1])
            ->willReturn(true);

        $this->convocadosModelMock
            ->expects($this->never())
            ->method('delete');

        $this->situacaoModelMock
            ->expects($this->once())
            ->method('atualizar')
            ->with(1, ['fk_id_candidato' => 1, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value])
            ->willReturn(true);

        $this->service->salvarConvocados(10, [1], [], 1, 2);
    }

    public function testSalvarConvocadosIgnoraIdsInvalidos(): void
    {
        $this->configurarBuscarSituacoes(
            [5],
            [5 => ['pk_id_situacao' => 5, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value]]
        );

        $this->convocadosModelMock
            ->method('findAll')
            ->willReturn([]);

        $this->convocadosModelMock
            ->expects($this->once())
            ->method('insert')
            ->with(['fk_id_convocacao' => 10, 'fk_id_candidato' => 5, 'nm_tipo' => 1])
            ->willReturn(true);

        $this->situacaoModelMock
            ->expects($this->once())
            ->method('atualizar')
            ->with(5, ['fk_id_candidato' => 5, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value])
            ->willReturn(true);

        $this->service->salvarConvocados(10, ['0', 'abc', 5], ['0', '', null], 1, 2);
    }

    public function testSalvarConvocadosDaPrioridadeATitularSobreSuplente(): void
    {
        $this->configurarBuscarSituacoes(
            [1, 2],
            [
                1 => ['pk_id_situacao' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value],
                2 => ['pk_id_situacao' => 2, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value],
            ]
        );

        $this->convocadosModelMock
            ->method('findAll')
            ->willReturn([]);

        $this->convocadosModelMock
            ->expects($this->exactly(2))
            ->method('insert')
            ->withConsecutive(
                [['fk_id_convocacao' => 10, 'fk_id_candidato' => 1, 'nm_tipo' => 1]],
                [['fk_id_convocacao' => 10, 'fk_id_candidato' => 2, 'nm_tipo' => 0]],
            )
            ->willReturn(true);

        $this->situacaoModelMock
            ->expects($this->exactly(2))
            ->method('atualizar')
            ->withConsecutive(
                [1, ['fk_id_candidato' => 1, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value]],
                [2, ['fk_id_candidato' => 2, 'fk_id_cargo' => 2, 'fk_id_processo' => 1, 'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value]],
            )
            ->willReturn(true);

        $this->service->salvarConvocados(10, [1], [1, 2], 1, 2);
    }

    public function testSalvarConvocadosBloqueiaSuplenteQueFoiTitularAnteriormente(): void
    {
        $this->configurarConvocacaoAnterior(5);
        $this->configurarBuscarSituacoes(
            [3],
            [3 => ['pk_id_situacao' => 3, 'nm_id_situacao' => SituacaoCandidatosEnum::AGUARDANDO->value]]
        );

        $this->convocadosModelMock
            ->method('countAllResults')
            ->willReturn(1);

        $this->convocadosModelMock
            ->expects($this->never())
            ->method('insert');
        $this->situacaoModelMock
            ->expects($this->never())
            ->method('atualizar');

        $this->expectException(ConvocacaoInvalidaException::class);

        $this->service->salvarConvocados(10, [], [3], 1, 2);
    }

    public function testSalvarConvocadosBloqueiaCandidatoContratado(): void
    {
        $this->configurarBuscarSituacoes(
            [6],
            [6 => ['pk_id_situacao' => 6, 'nm_id_situacao' => SituacaoCandidatosEnum::CONTRATADO->value]]
        );

        $this->convocadosModelMock
            ->expects($this->never())
            ->method('insert');
        $this->situacaoModelMock
            ->expects($this->never())
            ->method('atualizar');

        $this->expectException(ConvocacaoInvalidaException::class);

        $this->service->salvarConvocados(10, [6], [], 1, 2);
    }

    public function testContratarCandidatoRemoveDaConvocacaoAtualESalvaSituacao(): void
    {
        $this->processosModelMock
            ->method('first')
            ->willReturn((object) [
                'pk_id_convocacao'     => 10,
                'nm_ano_convocacao'    => 2026,
                'nm_numero_convocacao' => 2,
            ]);

        $this->convocadosModelMock
            ->expects($this->once())
            ->method('delete')
            ->with(null, true)
            ->willReturn(true);

        $this->situacaoModelMock
            ->method('getSituacaoCandidatoCargoProcesso')
            ->willReturn(null);

        $this->situacaoModelMock
            ->expects($this->once())
            ->method('salvar')
            ->with([
                'fk_id_candidato' => 7,
                'fk_id_cargo'     => 2,
                'fk_id_processo'  => 1,
                'nm_id_situacao'  => SituacaoCandidatosEnum::CONTRATADO->value,
            ])
            ->willReturn(99);

        $this->service->contratarCandidato(1, 2, 7);
    }

    public function testContratarCandidatoAtualizaSituacaoExistente(): void
    {
        $this->processosModelMock
            ->method('first')
            ->willReturn((object) [
                'pk_id_convocacao'     => 10,
                'nm_ano_convocacao'    => 2026,
                'nm_numero_convocacao' => 2,
            ]);

        $this->convocadosModelMock
            ->expects($this->once())
            ->method('delete')
            ->with(null, true)
            ->willReturn(true);

        $this->situacaoModelMock
            ->method('getSituacaoCandidatoCargoProcesso')
            ->willReturn([
                'pk_id_situacao' => 8,
                'nm_id_situacao' => SituacaoCandidatosEnum::CONVOCADO->value,
            ]);

        $this->situacaoModelMock
            ->expects($this->once())
            ->method('atualizar')
            ->with(8, [
                'fk_id_candidato' => 7,
                'fk_id_cargo'     => 2,
                'fk_id_processo'  => 1,
                'nm_id_situacao'  => SituacaoCandidatosEnum::CONTRATADO->value,
            ])
            ->willReturn(true);

        $this->service->contratarCandidato(1, 2, 7);
    }

    private function configurarConvocacaoAnterior(?int $idAnterior): void
    {
        $this->processosModelMock
            ->method('find')
            ->with(10)
            ->willReturn((object) [
                'pk_id_convocacao'     => 10,
                'nm_ano_convocacao'    => 2026,
                'nm_numero_convocacao' => 2,
            ]);

        foreach (['where', 'orWhere', 'orderBy', 'orGroupStart', 'groupStart', 'groupEnd'] as $method) {
            $this->processosModelMock->method($method)->willReturnSelf();
        }

        $this->processosModelMock
            ->method('first')
            ->willReturn($idAnterior === null ? null : (object) [
                'pk_id_convocacao'     => $idAnterior,
                'nm_ano_convocacao'    => 2026,
                'nm_numero_convocacao' => 1,
            ]);
    }

    /**
     * @param int[]                 $ids
     * @param array<int, array> $situacoes
     */
    private function configurarBuscarSituacoes(array $ids, array $situacoes): void
    {
        $rows = [];
        foreach ($ids as $id) {
            if (isset($situacoes[$id])) {
                $rows[] = ['fk_id_candidato' => $id] + $situacoes[$id];
            }
        }

        $this->situacaoModelMock
            ->method('findAll')
            ->willReturn($rows);

        $this->situacaoModelMock
            ->method('getSituacaoCandidatoCargoProcesso')
            ->willReturnCallback(function (int $processoId, int $cargoId, int $candidatoId) use ($situacoes) {
                return $situacoes[$candidatoId] ?? null;
            });
    }
}
