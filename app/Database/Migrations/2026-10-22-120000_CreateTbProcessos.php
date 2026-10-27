<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbProcessos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_processo' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_secretaria' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'nm_numero_processo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'nm_ano_processo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_objeto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'nm_situacao' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => '1=Inscrições Abertas, 2=Inscrições Encerradas, 3=Resultado Publicado, 4=Vigente, 5=Encerrado',
            ],
            'dt_inicio_inscricao' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'dt_fim_inscricao' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'dt_divulga_resultado' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'nm_meses_validade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 12,
            ],
            'nm_prorrogado' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'null'       => false,
                'default'    => 0,
                'comment'    => '0=Não, 1=Sim',
            ],
            'is_deletado' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'NULL=Ativo, datetime=Soft deleted (CI4)',
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_processo');
        $this->forge->addKey('fk_id_secretaria', false, false, 'idx_secretaria');
        $this->forge->addKey('nm_ano_processo', false, false, 'idx_ano');
        $this->forge->addKey('nm_situacao', false, false, 'idx_situacao');
        $this->forge->addKey(['dt_inicio_inscricao', 'dt_fim_inscricao'], false, false, 'idx_inscricoes');
        $this->forge->addKey(['nm_numero_processo', 'nm_ano_processo'], false, false, 'idx_processo_ano');

        $this->forge->createTable('tb_processos');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_processos');
    }
}
