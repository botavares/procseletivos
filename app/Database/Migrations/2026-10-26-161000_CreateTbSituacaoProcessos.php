<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbSituacaoProcessos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_situacao_processo' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_processo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'nm_id_situacao' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => '1=Inscrições Abertas, 2=Inscrições Encerradas, 3=Resultado Publicado, 4=Vigente, 5=Encerrado, 6=Agendado',
            ],
            'dt_ultima_alteracao' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_situacao_processo');
        $this->forge->addUniqueKey('fk_id_processo', 'uniq_processo');
        $this->forge->addKey('nm_id_situacao', false, false, 'idx_situacao');

        $this->forge->addForeignKey('fk_id_processo', 'tb_processos', 'pk_id_processo', 'CASCADE', 'CASCADE');

        $this->forge->createTable('tb_situacao_processos');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_situacao_processos');
    }
}
