<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCadastradosExperiencias extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_experiencias' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_cadastrado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
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
            'fk_id_experiencia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'ds_multiplicador' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'default'    => 0,
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_experiencias');
        $this->forge->addUniqueKey(['fk_id_cadastrado', 'fk_id_edital', 'fk_id_cargo', 'fk_id_experiencia']);
        $this->forge->addKey('fk_id_edital', false, false, 'fk_id_edital');
        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo');
        $this->forge->addKey('fk_id_experiencia', false, false, 'fk_id_experiencia');

        $this->forge->addForeignKey('fk_id_cadastrado', 'tb_cadastrados', 'pk_id_cadastrado', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_edital', 'tb_editais', 'pk_id_edital', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_experiencia', 'tb_experiencias', 'pk_id_experiencia', '', 'CASCADE');

        $this->forge->createTable('tb_cadastrados_experiencias');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cadastrados_experiencias');
    }
}
