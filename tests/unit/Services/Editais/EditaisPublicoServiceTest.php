<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Editais;

use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Models\Admin\SecretariasModel;
use App\Models\CargosModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Editais\EditaisPublicoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EditaisPublicoServiceTest extends CIUnitTestCase
{
    private ProcessosSeletivosModel $processosModel;
    private ProcessosDocumentosModel $documentosModel;
    private SecretariasModel $secretariasModel;
    private CargosModel $cargosModel;
    private EditaisPublicoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        helper('edital');

        $this->processosModel   = $this->createMock(ProcessosSeletivosModel::class);
        $this->documentosModel   = $this->createMock(ProcessosDocumentosModel::class);
        $this->secretariasModel  = $this->getMockBuilder(SecretariasModel::class)
            ->onlyMethods(['findAll'])
            ->getMock();
        $this->cargosModel       = $this->getMockBuilder(CargosModel::class)
            ->onlyMethods(['findAll'])
            ->getMock();

        $this->service = new EditaisPublicoService(
            $this->processosModel,
            $this->documentosModel,
            $this->secretariasModel,
            $this->cargosModel,
        );
    }

    public function testListarEditaisRetornaEstruturaEsperada(): void
    {
        $processo = $this->criarProcessoStub();
        $cargo    = $this->criarCargoStub();
        $edital   = $this->criarDocumentoStub(TiposDocumentosEnum::EDITAL_ABERTURA);

        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([$processo]);
        $this->processosModel->method('cargosPorProcesso')->with(1)->willReturn([$cargo]);
        $this->documentosModel->method('porProcessoId')->with(1)->willReturn([$edital]);

        $resultado = $this->service->listarEditais();

        $this->assertCount(1, $resultado);
        $this->assertSame(1, $resultado[0]['id']);
        $this->assertSame('003/2026', $resultado[0]['numero_formatado']);
        $this->assertSame('Agente Administrativo', $resultado[0]['objeto']);
        $this->assertSame('SEPLAG', $resultado[0]['secretaria']->sigla);
        $this->assertCount(1, $resultado[0]['cargos']);
        $this->assertCount(1, $resultado[0]['editais']);
        $this->assertSame('home-tag--aberto', $resultado[0]['tag_class']);
        $this->assertTrue($resultado[0]['inscricao_aberta']);
    }

    public function testFiltroSituacaoEncerradosOcultaAbertos(): void
    {
        $aberto              = $this->criarProcessoStub(id: 1, situacao: 1);
        $inscricoesEncerradas = $this->criarProcessoStub(id: 2, situacao: 2);
        $encerrado            = $this->criarProcessoStub(id: 3, situacao: 5);

        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([$aberto, $inscricoesEncerradas, $encerrado]);
        $this->processosModel->method('cargosPorProcesso')->willReturn([]);
        $this->documentosModel->method('porProcessoId')->willReturn([]);

        $resultado = $this->service->listarEditais(['situacao' => 'Encerrados']);

        $this->assertCount(2, $resultado);
        $this->assertSame(2, $resultado[0]['id']);
        $this->assertSame('Inscrições Encerradas', $resultado[0]['situacao_label']);
        $this->assertSame(3, $resultado[1]['id']);
        $this->assertSame('Encerrado', $resultado[1]['situacao_label']);
    }

    public function testFiltroSituacaoTodasExibeTodasAsSituacoes(): void
    {
        $aberto    = $this->criarProcessoStub(id: 1, situacao: 1);
        $vigente   = $this->criarProcessoStub(id: 2, situacao: 4);
        $encerrado = $this->criarProcessoStub(id: 3, situacao: 5);

        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([$aberto, $vigente, $encerrado]);
        $this->processosModel->method('cargosPorProcesso')->willReturn([]);
        $this->documentosModel->method('porProcessoId')->willReturn([]);

        $resultado = $this->service->listarEditais(['situacao' => 'Todas']);

        $this->assertCount(3, $resultado);
    }

    public function testFiltroSituacaoVigentesSohExibeVigentes(): void
    {
        $aberto    = $this->criarProcessoStub(id: 1, situacao: 1);   // Inscrições abertas
        $vigente   = $this->criarProcessoStub(id: 2, situacao: 4);   // Vigente
        $encerrado = $this->criarProcessoStub(id: 3, situacao: 5);   // Encerrado

        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([$aberto, $vigente, $encerrado]);
        $this->processosModel->method('cargosPorProcesso')->willReturn([]);
        $this->documentosModel->method('porProcessoId')->willReturn([]);

        $resultado = $this->service->listarEditais(['situacao' => 'Vigentes']);

        $this->assertCount(1, $resultado);
        $this->assertSame(2, $resultado[0]['id']);
        $this->assertSame('Vigente', $resultado[0]['situacao_label']);
    }

    public function testFiltroPorSecretaria(): void
    {
        $processoSeplag = $this->criarProcessoStub(id: 1, secretariaId: 1);
        $processoOutra  = $this->criarProcessoStub(id: 2, secretariaId: 99);

        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([$processoSeplag, $processoOutra]);
        $this->processosModel->method('cargosPorProcesso')->willReturn([]);
        $this->documentosModel->method('porProcessoId')->willReturn([]);

        $resultado = $this->service->listarEditais(['secretaria' => '1']);

        $this->assertCount(1, $resultado);
        $this->assertSame(1, $resultado[0]['id']);
    }

    public function testFiltroPorCargo(): void
    {
        $processo = $this->criarProcessoStub(id: 1);
        $cargoCorreto = $this->criarCargoStub(nome: 'Agente Administrativo');
        $cargoOutro   = $this->criarCargoStub(nome: 'Auxiliar de Serviços');

        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([$processo]);
        $this->processosModel->method('cargosPorProcesso')->with(1)->willReturn([$cargoCorreto, $cargoOutro]);
        $this->documentosModel->method('porProcessoId')->willReturn([]);

        $resultado = $this->service->listarEditais(['cargo' => 'Agente Administrativo']);

        $this->assertCount(1, $resultado);
        $this->assertCount(2, $resultado[0]['cargos']);
    }

    public function testRetificacoesSaoExibidasSeparadasDoEdital(): void
    {
        $processo = $this->criarProcessoStub();
        $edital   = $this->criarDocumentoStub(TiposDocumentosEnum::EDITAL_ABERTURA, vigente: true);
        $ret      = $this->criarDocumentoStub(TiposDocumentosEnum::RETIFICACAO, vigente: true);

        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([$processo]);
        $this->processosModel->method('cargosPorProcesso')->willReturn([]);
        $this->documentosModel->method('porProcessoId')->with(1)->willReturn([$edital, $ret]);

        $resultado = $this->service->listarEditais();

        $this->assertCount(1, $resultado[0]['editais']);
        $this->assertCount(1, $resultado[0]['retificacoes']);
        $this->assertSame('Retificação', $resultado[0]['retificacoes'][0]['tipo']);
        $this->assertTrue($resultado[0]['retificacoes'][0]['vigente']);
    }

    public function testListarEditaisVazioQuandoNenhumProcesso(): void
    {
        $this->processosModel->method('listarComSituacaoESecretaria')->willReturn([]);

        $resultado = $this->service->listarEditais();

        $this->assertSame([], $resultado);
    }

    public function testObterSecretariasFiltro(): void
    {
        $secretarias = [(object) ['pk_id_secretaria' => 1, 'ds_nome_secretaria' => 'SEPLAG']];
        $this->secretariasModel->method('findAll')->willReturn($secretarias);

        $resultado = $this->service->obterSecretariasFiltro();

        $this->assertSame($secretarias, $resultado);
    }

    public function testObterCargosFiltro(): void
    {
        $cargos = [(object) ['pk_id_cargo' => 1, 'ds_nome_cargo' => 'Agente Administrativo']];
        $this->cargosModel->method('findAll')->willReturn($cargos);

        $resultado = $this->service->obterCargosFiltro();

        $this->assertSame($cargos, $resultado);
    }

    private function criarProcessoStub(
        int $id = 1,
        int $situacao = 1,
        int $secretariaId = 1,
    ): object {
        return (object) [
            'pk_id_processo'      => $id,
            'fk_id_secretaria'    => $secretariaId,
            'nm_numero_processo'  => 3,
            'nm_ano_processo'     => 2026,
            'ds_objeto'           => 'Agente Administrativo',
            'dt_inicio_inscricao' => '2026-09-09',
            'dt_fim_inscricao'    => '2026-09-24',
            'dt_data_homologacao' => null,
            'nm_id_situacao'      => $situacao,
            'ds_nome_secretaria'  => 'Secretaria Municipal de Planejamento, Gestão, Ciência e Tecnologia',
            'ds_sigla_secretaria' => 'SEPLAG',
        ];
    }

    private function criarCargoStub(string $nome = 'Agente Administrativo'): object
    {
        return (object) [
            'pk_id_cargo'        => 1,
            'ds_nome_cargo'      => $nome,
            'nm_vagas'           => 6,
            'nm_vagas_pcd'       => 1,
            'nm_carga_horaria'   => 40,
            'nm_tipo_carga_horaria' => 1,
            'ds_requisito_provimento' => 'Ensino médio completo',
        ];
    }

    private function criarDocumentoStub(TiposDocumentosEnum $tipo, bool $vigente = false): object
    {
        return (object) [
            'pk_id_documento'     => 1,
            'nm_tipo'             => $tipo->value,
            'nm_numero_documento' => 3,
            'nm_ano_documento'    => 2026,
            'ds_codigo_oficial'   => 'DOM-4344',
            'ds_arquivo'          => 'edital_003_2026.pdf',
            'ds_link'             => null,
            'nm_versao_vigente'   => $vigente ? 1 : 0,
            'dt_data_publicacao'  => '2026-09-05',
        ];
    }
}
