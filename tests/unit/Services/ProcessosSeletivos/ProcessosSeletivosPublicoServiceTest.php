<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ProcessosSeletivos;

use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Models\Admin\SecretariasModel;
use App\Models\CargosAperfeicoamentosModel;
use App\Models\CargosCriteriosAdicionaisModel;
use App\Models\CargosDesempateConfigModel;
use App\Models\CargosEscolaridadesModel;
use App\Models\CargosExperienciasModel;
use App\Models\CargosModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;
use App\Services\ProcessosSeletivos\ProcessosSeletivosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosSeletivosPublicoServiceTest extends CIUnitTestCase
{
    private ProcessosSeletivosModel $processosModel;
    private ProcessosDocumentosModel $documentosModel;
    private SecretariasModel $secretariasModel;
    private CargosModel $cargosModel;
    private CargosEscolaridadesModel $cargosEscolaridadesModel;
    private CargosExperienciasModel $cargosExperienciasModel;
    private CargosAperfeicoamentosModel $cargosAperfeicoamentosModel;
    private CargosCriteriosAdicionaisModel $cargosCriteriosAdicionaisModel;
    private CargosDesempateConfigModel $cargosDesempateConfigModel;
    private LogsService $logsService;
    private ProcessosSeletivosService $service;

    protected function setUp(): void
    {
        parent::setUp();

        helper('edital');

        $this->processosModel   = $this->createMock(ProcessosSeletivosModel::class);
        $this->documentosModel  = $this->createMock(ProcessosDocumentosModel::class);
        $this->secretariasModel = $this->getMockBuilder(SecretariasModel::class)
            ->onlyMethods(['findAll'])
            ->getMock();
        $this->cargosModel      = $this->getMockBuilder(CargosModel::class)
            ->onlyMethods(['findAll'])
            ->getMock();
        $this->logsService      = $this->createMock(LogsService::class);

        $this->cargosEscolaridadesModel       = $this->createMock(CargosEscolaridadesModel::class);
        $this->cargosExperienciasModel        = $this->createMock(CargosExperienciasModel::class);
        $this->cargosAperfeicoamentosModel    = $this->createMock(CargosAperfeicoamentosModel::class);
        $this->cargosCriteriosAdicionaisModel = $this->createMock(CargosCriteriosAdicionaisModel::class);
        $this->cargosDesempateConfigModel     = $this->createMock(CargosDesempateConfigModel::class);

        $this->service = new ProcessosSeletivosService(
            $this->processosModel,
            $this->logsService,
            $this->documentosModel,
            $this->secretariasModel,
            $this->cargosModel,
            $this->cargosEscolaridadesModel,
            $this->cargosExperienciasModel,
            $this->cargosAperfeicoamentosModel,
            $this->cargosCriteriosAdicionaisModel,
            $this->cargosDesempateConfigModel,
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

    public function testListarAbertosParaHomeExibeApenasProcessosComEditalECargos(): void
    {
        $abertoCompleto  = $this->criarProcessoStub(id: 1, situacao: 1);
        $abertoSemEdital = $this->criarProcessoStub(id: 2, situacao: 1);
        $abertoSemCargo  = $this->criarProcessoStub(id: 3, situacao: 1);
        $agendado        = $this->criarProcessoStub(id: 4, situacao: 6);

        $service = $this->criarServiceComStub([$abertoCompleto, $abertoSemEdital, $abertoSemCargo, $agendado]);

        $this->documentosModel
            ->method('existeEditalAbertura')
            ->willReturnCallback(fn(int $id) => $id === 1);

        $this->processosModel
            ->method('cargosPorProcesso')
            ->willReturnCallback(fn(int $id) => match ($id) {
                1 => [$this->criarCargoStub()],
                default => [],
            });

        $this->cargosEscolaridadesModel
            ->method('existePorProcessoCargo')
            ->willReturnCallback(fn(int $proc, int $cargo) => $proc === 1 && $cargo === 1);

        $this->cargosExperienciasModel
            ->method('existePorProcessoCargo')
            ->willReturnCallback(fn(int $proc, int $cargo) => $proc === 1 && $cargo === 1);

        $this->cargosAperfeicoamentosModel
            ->method('existePorProcessoCargo')
            ->willReturnCallback(fn(int $proc, int $cargo) => $proc === 1 && $cargo === 1);

        $this->cargosCriteriosAdicionaisModel
            ->method('existePorProcessoCargo')
            ->willReturnCallback(fn(int $proc, int $cargo) => $proc === 1 && $cargo === 1);

        $this->cargosDesempateConfigModel
            ->method('existePorProcessoCargo')
            ->willReturnCallback(fn(int $proc, int $cargo) => $proc === 1 && $cargo === 1);

        $resultado = $service->listarAbertosParaHome();

        $this->assertCount(1, $resultado);
        $this->assertSame(1, $resultado[0]->pk_id_processo);
    }

    public function testListarAbertosParaHomeOcultaCargoSemDadosClassificatorios(): void
    {
        $abertoCompleto               = $this->criarProcessoStub(id: 1, situacao: 1);
        $abertoSemDadosClassificatorios = $this->criarProcessoStub(id: 2, situacao: 1);

        $service = $this->criarServiceComStub([$abertoCompleto, $abertoSemDadosClassificatorios]);

        $this->documentosModel->method('existeEditalAbertura')->willReturn(true);

        $this->processosModel
            ->method('cargosPorProcesso')
            ->willReturnCallback(fn(int $id) => [$this->criarCargoStub()]);

        // Apenas o processo 1 possui dados classificatórios
        $this->cargosEscolaridadesModel
            ->method('existePorProcessoCargo')
            ->willReturnCallback(fn(int $proc) => $proc === 1);

        $this->cargosExperienciasModel->method('existePorProcessoCargo')->willReturn(false);
        $this->cargosAperfeicoamentosModel->method('existePorProcessoCargo')->willReturn(false);
        $this->cargosCriteriosAdicionaisModel->method('existePorProcessoCargo')->willReturn(false);
        $this->cargosDesempateConfigModel->method('existePorProcessoCargo')->willReturn(true);

        $resultado = $service->listarAbertosParaHome();

        $this->assertCount(1, $resultado);
        $this->assertSame(1, $resultado[0]->pk_id_processo);
    }

    public function testListarAbertosParaHomeOcultaCargoSemDesempate(): void
    {
        $abertoCompleto      = $this->criarProcessoStub(id: 1, situacao: 1);
        $abertoSemDesempate  = $this->criarProcessoStub(id: 2, situacao: 1);

        $service = $this->criarServiceComStub([$abertoCompleto, $abertoSemDesempate]);

        $this->documentosModel->method('existeEditalAbertura')->willReturn(true);

        $this->processosModel
            ->method('cargosPorProcesso')
            ->willReturnCallback(fn(int $id) => [$this->criarCargoStub()]);

        $this->cargosEscolaridadesModel->method('existePorProcessoCargo')->willReturn(true);
        $this->cargosExperienciasModel->method('existePorProcessoCargo')->willReturn(true);
        $this->cargosAperfeicoamentosModel->method('existePorProcessoCargo')->willReturn(true);
        $this->cargosCriteriosAdicionaisModel->method('existePorProcessoCargo')->willReturn(true);

        // Apenas o processo 1 possui desempate configurado
        $this->cargosDesempateConfigModel
            ->method('existePorProcessoCargo')
            ->willReturnCallback(fn(int $proc) => $proc === 1);

        $resultado = $service->listarAbertosParaHome();

        $this->assertCount(1, $resultado);
        $this->assertSame(1, $resultado[0]->pk_id_processo);
    }

    public function testObterAlertasAptidaoProcessoRetornaTodasAsMensagensEsperadas(): void
    {
        $processo = $this->criarProcessoStub(id: 1, situacao: 1);

        $this->documentosModel->method('existeEditalAbertura')->willReturn(false);
        $this->processosModel->method('cargosPorProcesso')->with(1)->willReturn([]);

        $alertas = $this->service->obterAlertasAptidaoProcesso($processo);

        $this->assertContains('Não foi vinculado nenhum edital de abertura', $alertas);
        $this->assertContains('Não foi vinculado cargo(s) ao processo seletivo', $alertas);
        $this->assertNotContains('Cargo vinculado ao processo não tem dados classificatórios', $alertas);
        $this->assertNotContains('Cargo vinculado ao processo sem critérios de desempate', $alertas);
    }

    public function testObterAlertasAptidaoProcessoDetectaDadosClassificatoriosEDesempateAusentes(): void
    {
        $processo = $this->criarProcessoStub(id: 1, situacao: 1);

        $this->documentosModel->method('existeEditalAbertura')->willReturn(true);
        $this->processosModel->method('cargosPorProcesso')->with(1)->willReturn([$this->criarCargoStub()]);

        $this->cargosEscolaridadesModel->method('existePorProcessoCargo')->willReturn(false);
        $this->cargosExperienciasModel->method('existePorProcessoCargo')->willReturn(false);
        $this->cargosAperfeicoamentosModel->method('existePorProcessoCargo')->willReturn(false);
        $this->cargosCriteriosAdicionaisModel->method('existePorProcessoCargo')->willReturn(false);
        $this->cargosDesempateConfigModel->method('existePorProcessoCargo')->willReturn(false);

        $alertas = $this->service->obterAlertasAptidaoProcesso($processo);

        $this->assertContains('Cargo vinculado ao processo não tem dados classificatórios', $alertas);
        $this->assertContains('Cargo vinculado ao processo sem critérios de desempate', $alertas);
    }

    public function testListarAbertosParaHomeRetornaVazioQuandoNenhumProcesso(): void
    {
        $service = $this->criarServiceComStub([]);

        $resultado = $service->listarAbertosParaHome();

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

    private function criarServiceComStub(array $processosStub): ProcessosSeletivosService
    {
        return new class(
            $this->processosModel,
            $this->logsService,
            $this->documentosModel,
            $this->secretariasModel,
            $this->cargosModel,
            $this->cargosEscolaridadesModel,
            $this->cargosExperienciasModel,
            $this->cargosAperfeicoamentosModel,
            $this->cargosCriteriosAdicionaisModel,
            $this->cargosDesempateConfigModel,
            $processosStub,
        ) extends ProcessosSeletivosService {
            private array $processosStub;

            public function __construct(
                $processosModel,
                $logsService,
                $documentosModel,
                $secretariasModel,
                $cargosModel,
                $cargosEscolaridadesModel,
                $cargosExperienciasModel,
                $cargosAperfeicoamentosModel,
                $cargosCriteriosAdicionaisModel,
                $cargosDesempateConfigModel,
                array $processosStub,
            ) {
                parent::__construct(
                    $processosModel,
                    $logsService,
                    $documentosModel,
                    $secretariasModel,
                    $cargosModel,
                    $cargosEscolaridadesModel,
                    $cargosExperienciasModel,
                    $cargosAperfeicoamentosModel,
                    $cargosCriteriosAdicionaisModel,
                    $cargosDesempateConfigModel,
                );
                $this->processosStub = $processosStub;
            }

            public function listarAtivosComSituacao(): array
            {
                return $this->processosStub;
            }
        };
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
