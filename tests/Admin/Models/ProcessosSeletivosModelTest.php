<?php

declare(strict_types=1);

namespace Tests\Admin\Models;

use App\Models\ProcessosSeletivosModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class ProcessosSeletivosModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed;
    protected string $modelClass = ProcessosSeletivosModel::class;
    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->criarTabela();
        $this->model = new ProcessosSeletivosModel($this->db);
    }

    protected function tearDown(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("DROP TABLE IF EXISTS {$prefix}tb_processos");
        parent::tearDown();
    }

    private function criarTabela(): void
    {
        $prefix = $this->db->getPrefix();
        // Usamos TEXT no SQLite para simular DATETIME (CI4 soft delete)
        $sql = "CREATE TABLE {$prefix}tb_processos (
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
            is_deletado TEXT DEFAULT NULL
        )";
        $this->db->query($sql);
    }

    public function testCrudCompleto(): void
    {
        // === INSERT ===
        $dados = [
            'fk_id_secretaria'     => 1,
            'nm_numero_processo'   => 2026001,
            'nm_ano_processo'      => 2026,
            'ds_objeto'            => 'Objeto Teste',
            'nm_situacao'          => 1,
            'dt_inicio_inscricao'  => '2026-01-01',
            'dt_fim_inscricao'     => '2026-02-01',
            'dt_divulga_resultado' => null,
            'nm_meses_validade'    => 12,
            'nm_prorrogado'        => 0,
        ];

        $id = (int) $this->model->insert($dados);
        $this->assertGreaterThan(0, $id);

        $registro = $this->model->find($id);
        $this->assertNotNull($registro);
        $this->assertSame(2026001, (int) $registro->nm_numero_processo);

        // === UPDATE ===
        $this->model->update($id, [
            'nm_numero_processo' => 2026002,
            'nm_situacao'      => 2,
        ]);

        $atualizado = $this->model->find($id);
        $this->assertNotNull($atualizado);
        $this->assertSame(2026002, (int) $atualizado->nm_numero_processo);
        $this->assertSame(2, (int) $atualizado->nm_situacao);

        // === SOFT DELETE (CI4 nativo) ===
        $this->model->delete($id);

        // Após soft delete, não deve aparecer em consultas normais
        $deletado = $this->model->find($id);
        $this->assertNull($deletado);

        // Mas deve existir no banco com is_deletado preenchido (CI4 grava datetime)
        $prefix = $this->db->getPrefix();
        $raw = $this->db->query("SELECT is_deletado FROM {$prefix}tb_processos WHERE pk_id_processo = {$id}")->getRow();
        $this->assertNotNull($raw);
        $this->assertNotNull($raw->is_deletado, 'Soft delete deve preencher is_deletado com datetime');
    }

    public function testPorAno(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}tb_processos (fk_id_secretaria, nm_numero_processo, nm_ano_processo, ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao, nm_meses_validade, nm_prorrogado, is_deletado)
                              VALUES (1, 1, 2026, 'Obj', 1, '2026-01-01', '2026-02-01', 12, 0, NULL)");
        $this->db->query("INSERT INTO {$prefix}tb_processos (fk_id_secretaria, nm_numero_processo, nm_ano_processo, ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao, nm_meses_validade, nm_prorrogado, is_deletado)
                              VALUES (1, 2, 2025, 'Obj', 1, '2025-01-01', '2025-02-01', 12, 0, NULL)");

        $resultado = $this->model->porAno(2026);
        $this->assertCount(1, $resultado);
        $this->assertSame(1, (int) $resultado[0]->nm_numero_processo);
    }

    public function testPorSecretaria(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}tb_processos (fk_id_secretaria, nm_numero_processo, nm_ano_processo, ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao, nm_meses_validade, nm_prorrogado, is_deletado)
                              VALUES (5, 3, 2026, 'Obj', 1, '2026-01-01', '2026-02-01', 12, 0, NULL)");

        $resultado = $this->model->porSecretaria(5);
        $this->assertCount(1, $resultado);
        $this->assertSame(3, (int) $resultado[0]->nm_numero_processo);
    }

    public function testPorNumeroEAno(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}tb_processos (fk_id_secretaria, nm_numero_processo, nm_ano_processo, ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao, nm_meses_validade, nm_prorrogado, is_deletado)
                              VALUES (1, 4, 2024, 'Obj', 1, '2024-01-01', '2024-02-01', 12, 0, NULL)");

        $resultado = $this->model->porNumeroEAno(4, 2024);
        $this->assertNotNull($resultado);
        $this->assertSame(4, (int) $resultado->nm_numero_processo);
    }

    public function testPorSituacao(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}tb_processos (fk_id_secretaria, nm_numero_processo, nm_ano_processo, ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao, nm_meses_validade, nm_prorrogado, is_deletado)
                              VALUES (1, 5, 2026, 'Obj', 3, '2026-01-01', '2026-02-01', 12, 0, NULL)");

        $resultado = $this->model->porSituacao(3);
        $this->assertCount(1, $resultado);
        $this->assertSame(3, (int) $resultado[0]->nm_situacao);
    }

    public function testPorFaixaDeTempo(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}tb_processos (fk_id_secretaria, nm_numero_processo, nm_ano_processo, ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao, nm_meses_validade, nm_prorrogado, is_deletado)
                              VALUES (1, 6, 2026, 'Obj', 1, '2026-03-01', '2026-04-01', 12, 0, NULL)");
        $this->db->query("INSERT INTO {$prefix}tb_processos (fk_id_secretaria, nm_numero_processo, nm_ano_processo, ds_objeto, nm_situacao, dt_inicio_inscricao, dt_fim_inscricao, nm_meses_validade, nm_prorrogado, is_deletado)
                              VALUES (1, 7, 2026, 'Obj', 1, '2026-05-01', '2026-06-01', 12, 0, NULL)");

        $resultado = $this->model->porFaixaDeTempo('2026-02-01', '2026-05-01');
        $this->assertCount(1, $resultado);
        $this->assertSame(6, (int) $resultado[0]->nm_numero_processo);
    }
}
