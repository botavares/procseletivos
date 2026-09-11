<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCargosDesempatesConfig extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pk_id_desempate' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_cargo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_ordem' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 1,
            ],
            'ds_tipo_criterio' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'fk_id_referencia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'ds_direcao' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => false,
                'default'    => 'DESC',
            ],
            'ds_descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ds_parametro_extra' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'dt_criacao' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'dt_atualizacao' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
                'on_update' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_desempate');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['fk_id_cargo', 'ds_ordem']);

        $this->forge->createTable('tb_cargos_desempates_config', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_cargos_desempates_config', true);
    }
}
