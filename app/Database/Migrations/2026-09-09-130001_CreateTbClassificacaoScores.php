<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbClassificacaoScores extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pk_id_score' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_classificacao' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_tipo_score' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'ds_chave_score' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'ds_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nr_valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'ds_valor_texto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'dt_criacao' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_score');
        $this->forge->addForeignKey('fk_id_classificacao', 'tb_classificacao', 'pk_id_classificacao', 'CASCADE', 'CASCADE');
        $this->forge->addKey('fk_id_classificacao');
        $this->forge->addKey(['fk_id_classificacao', 'ds_chave_score']);

        $this->forge->createTable('tb_classificacao_scores', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_classificacao_scores', true);
    }
}
