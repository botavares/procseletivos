<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbStatusProcessosLog extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_status_log' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'dt_inicio' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'dt_fim' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'nm_total_processos' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'nm_processos_alterados' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'nm_status_execucao' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'null'       => false,
                'default'    => 0,
                'comment'    => '0=Pendente, 1=Sucesso, 2=Falha, 3=Parcial',
            ],
            'ds_erro' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ds_detalhe' => [
                'type'    => 'LONGTEXT',
                'null'    => true,
                'comment' => 'Array de transições: [{id_processo, status_anterior, status_novo}]',
            ],
            'ds_origem' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'CRON',
                'comment'    => 'CRON, CLI, MANUAL',
            ],
            'ds_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
                'default'    => null,
            ],
            'dt_criacao' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_status_log');
        $this->forge->addKey('nm_status_execucao', false, false, 'idx_status_execucao');
        $this->forge->addKey('dt_inicio', false, false, 'idx_dt_inicio');
        $this->forge->addKey('ds_origem', false, false, 'idx_origem');

        $this->forge->createTable('tb_status_processos_log');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_status_processos_log');
    }
}
