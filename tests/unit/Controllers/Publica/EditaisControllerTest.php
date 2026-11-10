<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Publica;

use App\Services\Base\SessaoService;
use App\Services\Editais\EditaisPublicoService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @internal
 */
final class EditaisControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private EditaisPublicoService $serviceMock;
    private SessaoService $sessaoMock;

    protected function setUp(): void
    {
        parent::setUp();

        helper('edital');

        $this->serviceMock = $this->createMock(EditaisPublicoService::class);
        $this->sessaoMock  = $this->createMock(SessaoService::class);

        // Substitui os serviços compartilhados para não bater no banco durante o teste de feature.
        Services::injectMock('editaisPublicoService', $this->serviceMock);
        Services::injectMock('sessao', $this->sessaoMock);
    }

    protected function tearDown(): void
    {
        Services::reset();
        parent::tearDown();
    }

    public function testPaginaEditaisRenderizaComFiltrosPadrao(): void
    {
        $this->serviceMock->method('listarEditais')
            ->with(['situacao' => 'Todas', 'secretaria' => '', 'cargo' => 'Todos'])
            ->willReturn([$this->criarEditalStub()]);

        $this->serviceMock->method('obterSecretariasFiltro')->willReturn([]);
        $this->serviceMock->method('obterCargosFiltro')->willReturn([]);
        $this->sessaoMock->method('estaLogado')->willReturn(false);

        $resultado = $this->get('/Editais');

        $resultado->assertOK();
        $resultado->assertSee('Editais (Processos Seletivos)');
        $resultado->assertSee('Processo Seletivo Simplificado nº 003/2026');
        $resultado->assertSee('Agente Administrativo');
        $resultado->assertSee('Ver processo completo');
        $resultado->assertSee('value="Todas" selected');
    }

    public function testFiltroSituacaoEncerradosEhRespeitado(): void
    {
        $this->serviceMock->method('listarEditais')
            ->with(['situacao' => 'Encerrados', 'secretaria' => '', 'cargo' => 'Todos'])
            ->willReturn([]);

        $this->serviceMock->method('obterSecretariasFiltro')->willReturn([]);
        $this->serviceMock->method('obterCargosFiltro')->willReturn([]);
        $this->sessaoMock->method('estaLogado')->willReturn(false);

        $resultado = $this->get('/Editais?situacao=Encerrados');

        $resultado->assertOK();
        $resultado->assertSee('value="Encerrados" selected');
        $resultado->assertSee('Nenhum edital encontrado para esta combinação de secretaria, cargo e situação.');
    }

    public function testMensagemDeVazioApareceComFiltrosCombinados(): void
    {
        $secretaria = (object) ['pk_id_secretaria' => 1, 'ds_nome_secretaria' => 'SEPLAG'];
        $cargo      = (object) ['pk_id_cargo' => 1, 'ds_nome_cargo' => 'Agente Administrativo'];

        $this->serviceMock->method('listarEditais')
            ->with(['situacao' => 'Vigentes', 'secretaria' => '1', 'cargo' => 'Agente Administrativo'])
            ->willReturn([]);

        $this->serviceMock->method('obterSecretariasFiltro')->willReturn([$secretaria]);
        $this->serviceMock->method('obterCargosFiltro')->willReturn([$cargo]);
        $this->sessaoMock->method('estaLogado')->willReturn(false);

        $resultado = $this->get('/Editais?situacao=Vigentes&secretaria=1&cargo=Agente%20Administrativo');

        $resultado->assertOK();
        $resultado->assertSee('Nenhum edital encontrado para esta combinação de secretaria, cargo e situação.');
        $resultado->assertSee('value="Vigentes" selected');
        $resultado->assertSee('value="1" selected');
        $resultado->assertSee('value="Agente Administrativo" selected');
    }

    public function testFiltroPorCargoRenderizaOpcoes(): void
    {
        $cargo = (object) ['pk_id_cargo' => 1, 'ds_nome_cargo' => 'Agente Administrativo'];

        $this->serviceMock->method('listarEditais')->willReturn([]);
        $this->serviceMock->method('obterSecretariasFiltro')->willReturn([]);
        $this->serviceMock->method('obterCargosFiltro')->willReturn([$cargo]);
        $this->sessaoMock->method('estaLogado')->willReturn(false);

        $resultado = $this->get('/Editais');

        $resultado->assertOK();
        $resultado->assertSee('Agente Administrativo');
    }

    public function testRetificacaoEhExibidaComBadgeVigente(): void
    {
        $edital = $this->criarEditalStub();
        $edital['retificacoes'] = [[
            'tipo'             => 'Retificação',
            'numero_formatado' => '001/2026',
            'tipo_id'          => 2,
            'arquivo'          => 'ret_001_2026.pdf',
            'vigente'          => true,
        ]];

        $this->serviceMock->method('listarEditais')->willReturn([$edital]);
        $this->serviceMock->method('obterSecretariasFiltro')->willReturn([]);
        $this->serviceMock->method('obterCargosFiltro')->willReturn([]);
        $this->sessaoMock->method('estaLogado')->willReturn(false);

        $resultado = $this->get('/Editais');

        $resultado->assertOK();
        $resultado->assertSee('Retificação');
        $resultado->assertSee('VIGENTE');
    }

    private function criarEditalStub(): array
    {
        return [
            'id'               => 1,
            'numero_formatado' => '003/2026',
            'numero'           => 3,
            'ano'              => 2026,
            'objeto'           => 'Agente Administrativo',
            'secretaria'       => (object) ['id' => 1, 'nome' => 'SEPLAG', 'sigla' => 'SEPLAG'],
            'situacao_id'      => 1,
            'situacao_label'   => 'Inscrições Abertas',
            'tag_class'        => 'home-tag--aberto',
            'inscricao_aberta' => true,
            'data_inicio'      => '09/09/2026',
            'data_fim'         => '24/09/2026',
            'periodo_inscricao' => '09/09/2026 a 24/09/2026',
            'data_homologacao' => null,
            'cargos'           => [(object) ['pk_id_cargo' => 1, 'ds_nome_cargo' => 'Agente Administrativo', 'nm_vagas' => 6]],
            'total_vagas'      => 6,
            'editais'          => [[
                'tipo'             => 'Edital de Abertura',
                'numero_formatado' => '003/2026',
                'tipo_id'          => 1,
                'arquivo'          => 'edital_003_2026.pdf',
                'vigente'          => true,
            ]],
            'retificacoes'     => [],
        ];
    }
}
