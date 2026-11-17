<?php

declare(strict_types=1);

namespace Tests\Admin\Services\ProcessosDocumentos;

use App\DTOs\Admin\ProcessosDocumentosDTO;
use App\Enums\Admin\SituacaoProcessosEnum;
use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;
use App\Services\ProcessosDocumentos\ProcessosDocumentosService;
use App\Services\SituacaoProcessosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosDocumentosServiceTest extends CIUnitTestCase
{
    private ProcessosDocumentosService $service;
    private $modelMock;
    private $logsMock;
    private $processosModelMock;
    private $situacaoServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        helper('edital');
        $this->modelMock             = $this->createMock(ProcessosDocumentosModel::class);
        $this->logsMock              = $this->createMock(LogsService::class);
        $this->processosModelMock    = $this->createMock(ProcessosSeletivosModel::class);
        $this->situacaoServiceMock   = $this->createMock(SituacaoProcessosService::class);
        $this->service               = new ProcessosDocumentosService(
            $this->modelMock,
            $this->logsMock,
            $this->processosModelMock,
            $this->situacaoServiceMock
        );
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

    public function testListarPorProcessoIdParaGridDefineMaisRecenteParaResultadoParcial(): void
    {
        $docs = [
            (object) [
                'pk_id_documento'   => 1,
                'nm_tipo'             => 3,
                'nm_versao_vigente'   => 0,
                'nm_numero_documento' => 10,
                'nm_ano_documento'    => 2026,
                'ds_codigo_oficial'   => 'RP-1',
            ],
            (object) [
                'pk_id_documento'   => 5,
                'nm_tipo'             => 3,
                'nm_versao_vigente'   => 0,
                'nm_numero_documento' => 11,
                'nm_ano_documento'    => 2026,
                'ds_codigo_oficial'   => 'RP-2',
            ],
            (object) [
                'pk_id_documento'   => 2,
                'nm_tipo'             => 1,
                'nm_versao_vigente'   => 1,
                'nm_numero_documento' => 1,
                'nm_ano_documento'    => 2026,
                'ds_codigo_oficial'   => 'ED-1',
            ],
        ];

        $this->modelMock->method('porProcessoId')
            ->with(1)
            ->willReturn($docs);

        $resultado = $this->service->listarPorProcessoIdParaGrid(1);

        $this->assertCount(3, $resultado);

        $rpMaisRecente = array_values(array_filter($resultado, fn($d) => (int) $d->pk_id_documento === 5))[0];
        $this->assertSame('Mais recente', $rpMaisRecente->vigente_label);
        $this->assertSame('badge-info', $rpMaisRecente->vigente_classe);

        $rpAntigo = array_values(array_filter($resultado, fn($d) => (int) $d->pk_id_documento === 1))[0];
        $this->assertSame('-', $rpAntigo->vigente_label);
        $this->assertSame('text-muted', $rpAntigo->vigente_classe);

        $edital = array_values(array_filter($resultado, fn($d) => (int) $d->pk_id_documento === 2))[0];
        $this->assertSame('Sim', $edital->vigente_label);
        $this->assertSame('badge-success', $edital->vigente_classe);
        $this->assertSame('Edital de Abertura', $edital->tipo_label);
    }

    public function testListarPorProcessoIdParaGridMostraSubstituicaoParaEditalAntigo(): void
    {
        $docs = [
            (object) [
                'pk_id_documento'         => 4,
                'nm_tipo'                 => TiposDocumentosEnum::EDITAL_ABERTURA->value,
                'nm_versao_vigente'       => 0,
                'nm_numero_documento'   => 1,
                'nm_ano_documento'        => 2026,
                'ds_codigo_oficial'       => 'ED-ABERTURA',
                'dt_data_substituicao'    => '2026-11-15 09:00:00',
                'nm_documento_retificado' => 0,
            ],
            (object) [
                'pk_id_documento'         => 5,
                'nm_tipo'                 => TiposDocumentosEnum::RETIFICACAO->value,
                'nm_versao_vigente'       => 1,
                'nm_numero_documento'   => 1,
                'nm_ano_documento'        => 2026,
                'ds_codigo_oficial'       => 'ED-RETIFICACAO',
                'dt_data_substituicao'    => null,
                'nm_documento_retificado' => 4,
            ],
        ];

        $this->modelMock->method('porProcessoId')
            ->with(1)
            ->willReturn($docs);

        $resultado = $this->service->listarPorProcessoIdParaGrid(1);

        $this->assertCount(2, $resultado);

        // Retificação vigente deve vir primeiro
        $this->assertSame(5, (int) $resultado[0]->pk_id_documento);
        $this->assertSame('Sim', $resultado[0]->vigente_label);
        $this->assertSame('badge-success', $resultado[0]->vigente_classe);

        // Edital substituído deve mostrar data de substituição e código/link da retificação
        $edital = array_values(array_filter($resultado, fn($d) => (int) $d->pk_id_documento === 4))[0];
        $this->assertStringContainsString('substituido em 15/11/2026', $edital->vigente_label);
        $this->assertStringContainsString('pela retificação: ED-RETIFICACAO', $edital->vigente_label);
        $this->assertSame('text-muted', $edital->vigente_classe);
        $this->assertSame('ED-RETIFICACAO', $edital->retificacao_codigo);
        $this->assertSame(url_diario_oficial('ED-RETIFICACAO'), $edital->retificacao_link);
    }

    public function testCriarRetificacaoAtualizaEditalDeAbertura(): void
    {
        $edital = (object) [
            'pk_id_documento' => 10,
            'fk_processo_referencia' => 1,
            'nm_tipo' => 1,
            'nm_numero_documento' => 123,
            'nm_ano_documento' => 2026,
        ];

        $this->modelMock->method('buscarEditalAberturaPorProcesso')
            ->with(1)
            ->willReturn($edital);
        $this->modelMock->method('buscarRetificacaoVigente')
            ->with(1, null)
            ->willReturn(null);
        $this->modelMock->method('update')
            ->withConsecutive(
                [10, $this->callback(function ($data) {
                    return isset($data['dt_data_substituicao'])
                        && $data['nm_versao_vigente'] === 0;
                })],
                // Não deve haver segundo update quando não existe retificação vigente anterior
            );
        $this->modelMock->method('insert')
            ->with($this->callback(function (array $data) {
                return $data['nm_documento_retificado'] === 10
                    && $data['dt_data_publicacao'] !== null
                    && $data['nm_versao_vigente'] === 1
                    && $data['fk_id_secretaria'] === 1;
            }))
            ->willReturn(20);

        $dto = $this->criarDto(tipo: 2, versaoVigente: 0);

        $id = $this->service->criar($dto);
        $this->assertSame(20, $id);
    }

    public function testCriarRetificacaoDesativaRetificacaoAnterior(): void
    {
        $edital = (object) [
            'pk_id_documento' => 10,
            'fk_processo_referencia' => 1,
            'nm_tipo' => 1,
            'nm_numero_documento' => 123,
            'nm_ano_documento' => 2026,
        ];

        $retificacaoAnterior = (object) [
            'pk_id_documento' => 20,
            'fk_processo_referencia' => 1,
            'nm_tipo' => 2,
            'nm_versao_vigente' => 1,
        ];

        $this->modelMock->method('buscarEditalAberturaPorProcesso')
            ->with(1)
            ->willReturn($edital);
        $this->modelMock->method('buscarRetificacaoVigente')
            ->with(1, null)
            ->willReturn($retificacaoAnterior);

        $atualizacoes = [];
        $this->modelMock->method('update')
            ->willReturnCallback(function ($id, $data) use (&$atualizacoes) {
                $atualizacoes[$id] = $data;
                return true;
            });

        $this->modelMock->method('insert')
            ->with($this->callback(function (array $data) {
                return $data['nm_documento_retificado'] === 10
                    && $data['nm_versao_vigente'] === 1;
            }))
            ->willReturn(30);

        $dto = $this->criarDto(tipo: 2, versaoVigente: 0);

        $id = $this->service->criar($dto);
        $this->assertSame(30, $id);

        $this->assertArrayHasKey(10, $atualizacoes);
        $this->assertSame(0, $atualizacoes[10]['nm_versao_vigente']);
        $this->assertNotNull($atualizacoes[10]['dt_data_substituicao']);

        $this->assertArrayHasKey(20, $atualizacoes);
        $this->assertSame(0, $atualizacoes[20]['nm_versao_vigente']);
        $this->assertNotNull($atualizacoes[20]['dt_data_substituicao']);
    }

    public function testCriarResultadoParcialSincronizaDataDivulgacaoESituacao(): void
    {
        $dto = $this->criarDto(tipo: TiposDocumentosEnum::RESULTADO_PARCIAL->value);
        $processoAntes = $this->criarProcessoStub();
        $processoDepois = clone $processoAntes;
        $processoDepois->dt_divulga_resultado = '2026-10-28 10:00:00';

        $this->modelMock->method('insert')->willReturn(50);

        $chamadasFind = [$processoAntes, $processoDepois];
        $indiceFind = 0;
        $this->processosModelMock
            ->method('find')
            ->willReturnCallback(function () use (&$chamadasFind, &$indiceFind) {
                return $chamadasFind[$indiceFind++];
            });

        $atualizacoes = [];
        $this->processosModelMock
            ->method('update')
            ->willReturnCallback(function ($id, $data) use (&$atualizacoes) {
                $atualizacoes[] = $data;
                return true;
            });

        $this->situacaoServiceMock
            ->method('calcularSituacao')
            ->with($processoDepois)
            ->willReturn(SituacaoProcessosEnum::RESULTADO_PUBLICADO);

        $id = $this->service->criar($dto);

        $this->assertSame(50, $id);
        $this->assertSame('2026-10-28 10:00:00', $atualizacoes[0]['dt_divulga_resultado']);
        $this->assertSame(SituacaoProcessosEnum::RESULTADO_PUBLICADO->value, $atualizacoes[1]['nm_situacao']);
    }

    public function testCriarHomologacaoSincronizaDataHomologacaoESituacao(): void
    {
        $dto = $this->criarDto(tipo: TiposDocumentosEnum::HOMOLOGACAO->value);
        $processoAntes = $this->criarProcessoStub();
        $processoDepois = clone $processoAntes;
        $processoDepois->dt_data_homologacao = '2026-10-29 10:00:00';

        $this->modelMock->method('insert')->willReturn(60);

        $chamadasFind = [$processoAntes, $processoDepois];
        $indiceFind = 0;
        $this->processosModelMock
            ->method('find')
            ->willReturnCallback(function () use (&$chamadasFind, &$indiceFind) {
                return $chamadasFind[$indiceFind++];
            });

        $atualizacoes = [];
        $this->processosModelMock
            ->method('update')
            ->willReturnCallback(function ($id, $data) use (&$atualizacoes) {
                $atualizacoes[] = $data;
                return true;
            });

        $this->situacaoServiceMock
            ->method('calcularSituacao')
            ->with($processoDepois)
            ->willReturn(SituacaoProcessosEnum::VIGENTE);

        $id = $this->service->criar($dto);

        $this->assertSame(60, $id);
        $this->assertSame('2026-10-28 10:00:00', $atualizacoes[0]['dt_data_homologacao']);
        $this->assertSame(SituacaoProcessosEnum::VIGENTE->value, $atualizacoes[1]['nm_situacao']);
    }

    public function testAtualizarResultadoParcialSobrescreveDataDivulgacao(): void
    {
        $dto = $this->criarDto(id: 5, tipo: TiposDocumentosEnum::RESULTADO_PARCIAL->value);
        $anterior = (object) [
            'pk_id_documento'         => 5,
            'fk_processo_referencia'  => 1,
            'nm_tipo'                 => TiposDocumentosEnum::RESULTADO_PARCIAL->value,
            'nm_numero_documento'     => 100,
            'nm_ano_documento'        => 2025,
            'ds_codigo_oficial'       => 'COD-OLD',
            'ds_link'                 => '',
            'ds_arquivo'              => '',
            'nm_versao_vigente'       => 1,
            'nm_documento_retificado' => 0,
        ];

        $processoAntes = $this->criarProcessoStub();
        $processoAntes->dt_divulga_resultado = '2026-10-20 10:00:00';
        $processoDepois = clone $processoAntes;
        $processoDepois->dt_divulga_resultado = '2026-10-28 10:00:00';

        $this->modelMock->method('find')->with(5)->willReturn($anterior);
        $this->modelMock->method('update')->with(5, $dto->toArray())->willReturn(true);

        $chamadasFind = [$processoAntes, $processoDepois];
        $indiceFind = 0;
        $this->processosModelMock
            ->method('find')
            ->willReturnCallback(function () use (&$chamadasFind, &$indiceFind) {
                return $chamadasFind[$indiceFind++];
            });

        $atualizacoes = [];
        $this->processosModelMock
            ->method('update')
            ->willReturnCallback(function ($id, $data) use (&$atualizacoes) {
                $atualizacoes[] = $data;
                return true;
            });

        $this->situacaoServiceMock
            ->method('calcularSituacao')
            ->with($processoDepois)
            ->willReturn(SituacaoProcessosEnum::RESULTADO_PUBLICADO);

        $this->service->atualizar(5, $dto);

        $this->assertSame('2026-10-28 10:00:00', $atualizacoes[0]['dt_divulga_resultado']);
    }

    private function criarProcessoStub(): object
    {
        return (object) [
            'pk_id_processo'       => 1,
            'fk_id_secretaria'     => 1,
            'nm_numero_processo'   => 3,
            'nm_ano_processo'      => 2026,
            'ds_objeto'            => 'Agente Administrativo',
            'dt_inicio_inscricao'  => '2026-09-09',
            'dt_fim_inscricao'     => '2026-09-24',
            'dt_data_homologacao'  => null,
            'dt_divulga_resultado' => null,
            'nm_meses_validade'    => 24,
            'nm_prorrogado'        => 0,
            'nm_situacao'          => SituacaoProcessosEnum::INSCRICOES_ABERTAS->value,
        ];
    }

    private function criarDto(?int $id = null, int $tipo = 1, int $versaoVigente = 1): ProcessosDocumentosDTO
    {
        return new ProcessosDocumentosDTO(
            idDocumento: $id,
            secretaria: 1,
            processoReferencia: 1,
            tipo: $tipo,
            numeroDocumento: 123,
            anoDocumento: 2026,
            codigoOficial: 'COD-001',
            link: 'https://exemplo.com',
            arquivo: 'doc.pdf',
            versaoVigente: $versaoVigente,
            documentoRetificado: 0,
            dataPublicacao: '2026-10-28 10:00:00',
        );
    }
}
