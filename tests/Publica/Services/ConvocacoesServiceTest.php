<?php

declare(strict_types=1);

namespace Tests\Publica\Services;

use App\Enums\SituacaoCandidatosEnum;
use App\Repositories\Publica\Interfaces\IConvocacoesRepository;
use App\Services\Publica\ConvocacoesService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ConvocacoesServiceTest extends CIUnitTestCase
{
    private IConvocacoesRepository $repositoryMock;
    private ConvocacoesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(IConvocacoesRepository::class);
        $this->service         = new ConvocacoesService($this->repositoryMock);
    }

    public function testListarPorProcessoSemConvocacoesRetornaArrayVazio(): void
    {
        $this->repositoryMock->method('listarPorProcesso')
            ->with(2)
            ->willReturn([]);

        $resultado = $this->service->listarPorProcesso(2);

        $this->assertSame([], $resultado);
    }

    public function testListarPorProcessoMontaEstruturaEsperada(): void
    {
        $this->repositoryMock->method('listarPorProcesso')
            ->with(2)
            ->willReturn([
                [
                    'pk_id_convocacao'       => 10,
                    'fk_id_processo'         => 2,
                    'fk_id_documento'        => 18,
                    'fk_id_cargo'            => 5,
                    'nm_numero_convocacao'   => 1,
                    'nm_ano_convocacao'      => 2026,
                    'ds_nome_cargo'          => 'Assistente Social',
                    'ds_codigo_oficial'      => '155555555',
                    'ds_link'                => '',
                    'ds_arquivo'             => 'conv_001.pdf',
                    'dt_data_publicacao'     => '2026-11-17',
                ],
            ]);

        $this->repositoryMock->method('listarConvocadosPorConvocacao')
            ->with(10)
            ->willReturn([
                [
                    'pk_id_convocado' => 100,
                    'fk_id_candidato' => 20,
                    'nm_tipo'         => 1,
                    'ds_nome'         => 'Adriana Vilela Moraes',
                ],
                [
                    'pk_id_convocado' => 101,
                    'fk_id_candidato' => 21,
                    'nm_tipo'         => 0,
                    'ds_nome'         => 'Beatriz Campos Rocha',
                ],
            ]);

        $this->repositoryMock->method('buscarSituacao')
            ->willReturn(['nm_id_situacao' => 1]);

        $this->repositoryMock->method('buscarClassificacao')
            ->willReturnMap([
                [2, 5, 20, ['ds_posicao' => '1º']],
                [2, 5, 21, ['ds_posicao' => '2º']],
            ]);

        $resultado = $this->service->listarPorProcesso(2);

        $this->assertCount(1, $resultado);
        $this->assertSame('001/2026', $resultado[0]['n']);
        $this->assertSame('17/11/2026', $resultado[0]['dt']);
        $this->assertSame('155555555', $resultado[0]['ed']);
        $this->assertSame('conv_001.pdf', $resultado[0]['arquivo']);
        $this->assertSame(2, $resultado[0]['q']);
        $this->assertCount(2, $resultado[0]['lista']);

        $this->assertSame('1º', $resultado[0]['lista'][0]['c']);
        $this->assertSame('Adriana Vilela Moraes', $resultado[0]['lista'][0]['nm']);
        $this->assertSame('Assistente Social', $resultado[0]['lista'][0]['cg']);
        $this->assertSame('TITULAR', $resultado[0]['lista'][0]['cd']);

        $this->assertSame('2º', $resultado[0]['lista'][1]['c']);
        $this->assertSame('SUPLENTE', $resultado[0]['lista'][1]['cd']);
    }

    public function testConvocadoDesclassificadoNaoApareceNaLista(): void
    {
        $this->repositoryMock->method('listarPorProcesso')
            ->with(2)
            ->willReturn([
                [
                    'pk_id_convocacao'       => 10,
                    'fk_id_processo'         => 2,
                    'fk_id_documento'        => 18,
                    'fk_id_cargo'            => 5,
                    'nm_numero_convocacao'   => 1,
                    'nm_ano_convocacao'      => 2026,
                    'ds_nome_cargo'          => 'Assistente Social',
                    'ds_codigo_oficial'      => '155555555',
                    'ds_link'                => '',
                    'ds_arquivo'             => 'conv_001.pdf',
                    'dt_data_publicacao'     => '2026-11-17',
                ],
            ]);

        $this->repositoryMock->method('listarConvocadosPorConvocacao')
            ->with(10)
            ->willReturn([
                [
                    'pk_id_convocado' => 100,
                    'fk_id_candidato' => 20,
                    'nm_tipo'         => 1,
                    'ds_nome'         => 'Adriana Vilela Moraes',
                ],
                [
                    'pk_id_convocado' => 101,
                    'fk_id_candidato' => 21,
                    'nm_tipo'         => 0,
                    'ds_nome'         => 'Candidato Desclassificado',
                ],
            ]);

        $this->repositoryMock->method('buscarSituacao')
            ->willReturnCallback(static function (int $processoId, int $cargoId, int $candidatoId) {
                return $candidatoId === 21
                    ? ['nm_id_situacao' => SituacaoCandidatosEnum::DESCLASSIFICADO->value]
                    : ['nm_id_situacao' => 1];
            });

        $this->repositoryMock->method('buscarClassificacao')
            ->willReturnMap([
                [2, 5, 20, ['ds_posicao' => '1º']],
            ]);

        $resultado = $this->service->listarPorProcesso(2);

        $this->assertCount(1, $resultado[0]['lista']);
        $this->assertSame('Adriana Vilela Moraes', $resultado[0]['lista'][0]['nm']);
        $this->assertSame(1, $resultado[0]['q']);
    }
}
