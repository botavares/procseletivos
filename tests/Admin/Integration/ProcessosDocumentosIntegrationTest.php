<?php

declare(strict_types=1);

namespace Tests\Admin\Integration;

use App\DTOs\Admin\ProcessosDocumentosDTO;
use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Services\Admin\LogsService;
use App\Services\Admin\ProcessosDocumentos\ProcessosDocumentosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosDocumentosIntegrationTest extends CIUnitTestCase
{
    private ProcessosDocumentosService $service;
    private ProcessosDocumentosModel $model;
    private $dbConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbConnection = \Config\Database::connect('tests');

        // Cria tabelas manualmente para SQLite3 in-memory (com prefixo db_)
        $this->dbConnection->query("
            CREATE TABLE IF NOT EXISTS db_tb_processos (
                pk_id_processo INTEGER PRIMARY KEY AUTOINCREMENT,
                fk_id_secretaria INTEGER NOT NULL,
                nm_numero_processo INTEGER NOT NULL,
                nm_ano_processo INTEGER NOT NULL,
                ds_objeto TEXT NOT NULL,
                nm_situacao INTEGER NOT NULL,
                dt_inicio_inscricao TEXT NOT NULL,
                dt_fim_inscricao TEXT NOT NULL,
                dt_divulga_resultado TEXT,
                nm_meses_validade INTEGER NOT NULL DEFAULT 12,
                nm_prorrogado INTEGER NOT NULL DEFAULT 0,
                is_deletado TEXT
            )
        ");

        $this->dbConnection->query("
            CREATE TABLE IF NOT EXISTS db_tb_processos_documentos (
                pk_id_documento INTEGER PRIMARY KEY AUTOINCREMENT,
                fk_processo_referencia INTEGER NOT NULL,
                nm_tipo INTEGER NOT NULL DEFAULT 0,
                nm_numero_documento INTEGER NOT NULL,
                nm_ano_documento INTEGER NOT NULL,
                ds_codigo_oficial TEXT NOT NULL,
                ds_link TEXT NOT NULL,
                ds_arquivo TEXT NOT NULL,
                nm_versao_vigente INTEGER NOT NULL DEFAULT 0,
                nm_documento_retificado INTEGER NOT NULL DEFAULT 0,
                is_deletado TEXT
            )
        ");

        // Limpa registros anteriores
        $this->dbConnection->query("DELETE FROM db_tb_processos_documentos");
        $this->dbConnection->query("DELETE FROM db_tb_processos");
        $this->dbConnection->query("DELETE FROM sqlite_sequence WHERE name='db_tb_processos_documentos'");
        $this->dbConnection->query("DELETE FROM sqlite_sequence WHERE name='db_tb_processos'");

        // Insere um processo base para relacionamento
        $this->dbConnection->query("
            INSERT INTO db_tb_processos (
                pk_id_processo, fk_id_secretaria, nm_numero_processo, nm_ano_processo,
                ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao,
                dt_divulga_resultado, nm_meses_validade, nm_prorrogado
            ) VALUES (
                1, 1, 999, 2026, 'Processo Teste', 1, '2026-01-01', '2026-12-31', NULL, 12, 0
            )
        ");

        $this->model = new ProcessosDocumentosModel();
        $logsMock = $this->createMock(LogsService::class);
        $this->service = new ProcessosDocumentosService($this->model, $logsMock);
    }

    protected function tearDown(): void
    {
        // Remove tabelas após os testes
        $this->dbConnection->query("DROP TABLE IF EXISTS db_tb_processos_documentos");
        $this->dbConnection->query("DROP TABLE IF EXISTS db_tb_processos");
        parent::tearDown();
    }

    public function testCadastroDocumento(): void
    {
        $dto = new ProcessosDocumentosDTO(
            idDocumento: null,
            processoReferencia: 1,
            tipo: TiposDocumentosEnum::EDITAL_ABERTURA->value,
            numeroDocumento: 100,
            anoDocumento: 2026,
            codigoOficial: 'COD-001',
            link: 'https://exemplo.com',
            arquivo: 'doc.pdf',
            versaoVigente: 1,
            documentoRetificado: 0,
        );

        $id = $this->service->criar($dto);

        $this->assertGreaterThan(0, $id);

        $registro = $this->model->find($id);
        $this->assertNotNull($registro);
        $this->assertSame(100, (int) $registro->nm_numero_documento);
        $this->assertSame(2026, (int) $registro->nm_ano_documento);
        $this->assertSame('COD-001', $registro->ds_codigo_oficial);
        $this->assertNull($registro->is_deletado);
    }

    public function testAlteracaoDocumento(): void
    {
        $dto = new ProcessosDocumentosDTO(
            idDocumento: null,
            processoReferencia: 1,
            tipo: TiposDocumentosEnum::EDITAL_ABERTURA->value,
            numeroDocumento: 100,
            anoDocumento: 2026,
            codigoOficial: 'COD-001',
            link: 'https://exemplo.com',
            arquivo: 'doc.pdf',
            versaoVigente: 1,
            documentoRetificado: 0,
        );

        $id = $this->service->criar($dto);

        $dtoAtualizado = new ProcessosDocumentosDTO(
            idDocumento: $id,
            processoReferencia: 1,
            tipo: TiposDocumentosEnum::EDITAL_ABERTURA->value,
            numeroDocumento: 101,
            anoDocumento: 2026,
            codigoOficial: 'COD-002',
            link: 'https://exemplo.com',
            arquivo: 'doc.pdf',
            versaoVigente: 1,
            documentoRetificado: 0,
        );

        $resultado = $this->service->atualizar($id, $dtoAtualizado);
        $this->assertTrue($resultado);

        $registro = $this->model->find($id);
        $this->assertSame(101, (int) $registro->nm_numero_documento);
        $this->assertSame('COD-002', $registro->ds_codigo_oficial);
    }

    public function testSoftDeleteDocumento(): void
    {
        $dto = new ProcessosDocumentosDTO(
            idDocumento: null,
            processoReferencia: 1,
            tipo: TiposDocumentosEnum::EDITAL_ABERTURA->value,
            numeroDocumento: 100,
            anoDocumento: 2026,
            codigoOficial: 'COD-001',
            link: 'https://exemplo.com',
            arquivo: 'doc.pdf',
            versaoVigente: 1,
            documentoRetificado: 0,
        );

        $id = $this->service->criar($dto);
        $this->assertNotNull($this->model->find($id));

        $resultado = $this->service->deletar($id);
        $this->assertTrue($resultado);

        // find() não deve retornar registros deletados por soft delete
        $this->assertNull($this->model->find($id));

        // Mas deve existir se consultar com withDeleted()
        $registroDeletado = $this->model->withDeleted()->find($id);
        $this->assertNotNull($registroDeletado);
        $this->assertNotNull($registroDeletado->is_deletado);
    }
}
