<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Transparencia;

use App\Presenters\TransparenciaPresenter;
use App\Repositories\Interfaces\ITransparenciaRepository;
use App\Services\Classificacao\DesempateConfigService;
use App\Services\Transparencia\TransparenciaService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TransparenciaServiceTest extends CIUnitTestCase
{
    private TransparenciaService $service;
    private $repositoryMock;
    private $presenterMock;
    private $desempateConfigMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock     = $this->createMock(ITransparenciaRepository::class);
        $this->presenterMock      = $this->createMock(TransparenciaPresenter::class);
        $this->desempateConfigMock = $this->createMock(DesempateConfigService::class);

        $this->service = new TransparenciaService(
            $this->repositoryMock,
            $this->presenterMock,
            $this->desempateConfigMock,
        );
    }

    public function testListarClassificacaoSemFiltrosUsaDefaults(): void
    {
        $this->desempateConfigMock->method('cargoPossuiConfiguracao')->willReturn(false);

        $this->repositoryMock
            ->method('listarClassificacao')
            ->willReturn(['dados' => [], 'total' => 0]);

        $this->presenterMock->method('processarSituacaoCandidatos')->willReturn([]);
        $this->presenterMock->method('calcularPaginacao')->willReturn([
            'paginaAtual'  => 1,
            'porPagina'    => 10,
            'total'        => 0,
            'totalPaginas' => 1,
            'inicio'       => 1,
            'fim'          => 1,
            'temAnterior'  => false,
            'temProxima'   => false,
        ]);

        $resultado = $this->service->listarClassificacao([]);

        $this->assertSame([], $resultado['dados']);
        $this->assertSame(0, $resultado['total']);
        $this->assertSame([], $resultado['colunas_ocultas']);
        $this->assertFalse($resultado['usa_desempate_dinamico']);
    }

    public function testListarClassificacaoComProcessoECargoPassaProcessoParaRepository(): void
    {
        $candidato = $this->criarCandidatoStub();

        $this->desempateConfigMock
            ->expects($this->once())
            ->method('cargoPossuiConfiguracao')
            ->with(7, 5, null)
            ->willReturn(false);

        $this->repositoryMock
            ->expects($this->once())
            ->method('listarClassificacao')
            ->with(
                1,     // pagina
                10,    // porPagina
                7,     // cargo
                '',    // busca
                false, // usaDesempateDinamico
                [],    // colunasDinamicas
                [],    // criteriosAdicionais
                5      // idProcesso
            )
            ->willReturn(['dados' => [$candidato], 'total' => 1]);

        $this->presenterMock
            ->method('processarSituacaoCandidatos')
            ->with([$candidato])
            ->willReturn([$candidato]);

        $this->presenterMock->method('calcularPaginacao')->willReturn([
            'paginaAtual'  => 1,
            'porPagina'    => 10,
            'total'        => 1,
            'totalPaginas' => 1,
            'inicio'       => 1,
            'fim'          => 1,
            'temAnterior'  => false,
            'temProxima'   => false,
        ]);

        $resultado = $this->service->listarClassificacao([
            'processo' => 5,
            'cargo'    => 7,
        ]);

        $this->assertCount(1, $resultado['dados']);
        $this->assertSame(1, $resultado['total']);
    }

    public function testListarClassificacaoComDesempateDinamicoMontaColunasDinamicas(): void
    {
        $candidato = $this->criarCandidatoStub();
        $colunaScore = (object) [
            'ds_chave_score' => 'PONTUACAO_TOTAL',
            'ds_label'       => 'Pontuação Total',
        ];

        $this->desempateConfigMock
            ->method('cargoPossuiConfiguracao')
            ->with(7, 5, null)
            ->willReturn(true);

        $this->repositoryMock
            ->method('buscarColunasDinamicas')
            ->with(7, '', 5)
            ->willReturn([$colunaScore]);

        $this->presenterMock
            ->method('resolverLabelsColunasDinamicas')
            ->with([$colunaScore], 7, 5)
            ->willReturn([
                'labels' => ['PONTUACAO_TOTAL' => 'Pontuação Total'],
                'tipos'  => ['PONTUACAO_TOTAL' => 'PONTUACAO'],
            ]);

        $this->repositoryMock
            ->method('carregarNomesReferencias')
            ->with(7)
            ->willReturn([]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('listarClassificacao')
            ->with(
                $this->anything(),
                $this->anything(),
                7,
                '',
                true,
                $this->anything(),
                [],
                5
            )
            ->willReturn(['dados' => [$candidato], 'total' => 1]);

        $this->presenterMock->method('processarSituacaoCandidatos')->willReturn([$candidato]);
        $this->presenterMock->method('calcularPaginacao')->willReturn([
            'paginaAtual'  => 1,
            'porPagina'    => 10,
            'total'        => 1,
            'totalPaginas' => 1,
            'inicio'       => 1,
            'fim'          => 1,
            'temAnterior'  => false,
            'temProxima'   => false,
        ]);

        $resultado = $this->service->listarClassificacao([
            'processo' => 5,
            'cargo'    => 7,
        ]);

        $this->assertTrue($resultado['usa_desempate_dinamico']);
        $this->assertNotEmpty($resultado['colunas_dinamicas']);
        $this->assertSame([], $resultado['criterios_adicionais']);
    }

    public function testListarClassificacaoSemDesempateBuscaColunasOcultasECriterios(): void
    {
        $candidato = $this->criarCandidatoStub();

        $this->desempateConfigMock
            ->method('cargoPossuiConfiguracao')
            ->with(7, 5, null)
            ->willReturn(false);

        $this->repositoryMock
            ->expects($this->once())
            ->method('obterColunasOcultas')
            ->with(7, '', 5)
            ->willReturn(['graduacao']);

        $this->repositoryMock
            ->expects($this->once())
            ->method('obterCriteriosAdicionaisAtivos')
            ->with(7, '', 5)
            ->willReturn([
                ['id' => 1, 'nome' => 'Curso de Capacitação', 'alias' => 'crit_1'],
            ]);

        $this->repositoryMock
            ->method('listarClassificacao')
            ->willReturn(['dados' => [$candidato], 'total' => 1]);

        $this->presenterMock->method('processarSituacaoCandidatos')->willReturn([$candidato]);
        $this->presenterMock->method('calcularPaginacao')->willReturn([
            'paginaAtual'  => 1,
            'porPagina'    => 10,
            'total'        => 1,
            'totalPaginas' => 1,
            'inicio'       => 1,
            'fim'          => 1,
            'temAnterior'  => false,
            'temProxima'   => false,
        ]);

        $resultado = $this->service->listarClassificacao([
            'processo' => 5,
            'cargo'    => 7,
        ]);

        $this->assertFalse($resultado['usa_desempate_dinamico']);
        $this->assertSame(['graduacao'], $resultado['colunas_ocultas']);
        $this->assertCount(1, $resultado['criterios_adicionais']);
    }

    public function testParametrosPageEPerPageSaoNormalizados(): void
    {
        $this->desempateConfigMock->method('cargoPossuiConfiguracao')->willReturn(false);

        $this->repositoryMock
            ->expects($this->once())
            ->method('listarClassificacao')
            ->with(
                2,  // pagina normalizada
                25, // porPagina normalizada
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(['dados' => [], 'total' => 0]);

        $this->presenterMock->method('processarSituacaoCandidatos')->willReturn([]);
        $this->presenterMock->method('calcularPaginacao')->willReturn([
            'paginaAtual'  => 1,
            'porPagina'    => 10,
            'total'        => 0,
            'totalPaginas' => 1,
            'inicio'       => 1,
            'fim'          => 1,
            'temAnterior'  => false,
            'temProxima'   => false,
        ]);

        $this->service->listarClassificacao([
            'page'     => 2,
            'per_page' => 25,
        ]);
    }

    private function criarCandidatoStub(): object
    {
        return (object) [
            'ds_posicao'          => 1,
            'ds_nome_candidato'   => 'Candidato Teste',
            'ds_nome_cargo'       => 'Agente Administrativo',
            'fk_id_processo'      => 5,
            'ds_nome_edital'      => 'Edital Teste',
            'dt_nascimento'       => '1990-01-01',
            'nr_total_pontos'     => 100,
            'nr_total_experiencias' => 10,
            'nr_total_graduacao'  => 20,
            'nr_total_posgraduacao' => 0,
            'nr_total_mestrado'   => 0,
            'nr_total_doutorado'  => 0,
            'nr_total_aperfeicoamentos' => 0,
            'fk_id_candidato'     => 1,
            'ds_possui_pne'       => 0,
            'fk_id_secretaria'    => 3,
            'situacao'            => null,
        ];
    }
}
