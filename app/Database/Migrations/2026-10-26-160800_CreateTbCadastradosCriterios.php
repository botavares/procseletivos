<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCadastradosCriterios extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_criterios' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_cadastrado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
            ],
            'fk_id_edital' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_cargo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_criterio' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
            ],
            'ds_quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'ds_multiplicador' => [
                'type'    => 'FLOAT',
                'null'    => true,
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_criterios');
        $this->forge->addUniqueKey(['fk_id_cadastrado', 'fk_id_edital', 'fk_id_cargo', 'fk_id_criterio'], 'fk_id_cadastrado_fk_id_edital_fk_id_cargo_fk_id_criterio');
        $this->forge->addKey('fk_id_edital', false, false, 'fk_id_edital');
        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo');
        $this->forge->addKey('fk_id_criterio', false, false, 'fk_id_criterio');

        $this->forge->addForeignKey('fk_id_cadastrado', 'tb_cadastrados', 'pk_id_cadastrado', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_edital', 'tb_editais', 'pk_id_edital', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_criterio', 'tb_criterios_adicionais', 'pk_id_criterio', '', 'CASCADE');

        $this->forge->createTable('tb_cadastrados_criterios');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cadastrados_criterios');
    }
}
