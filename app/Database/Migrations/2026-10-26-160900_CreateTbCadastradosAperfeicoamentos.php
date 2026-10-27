<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCadastradosAperfeicoamentos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_aperfeicoamento' => [
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
            'fk_id_curso' => [
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
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_aperfeicoamento');
        $this->forge->addUniqueKey(['fk_id_cadastrado', 'fk_id_edital', 'fk_id_cargo', 'fk_id_curso']);
        $this->forge->addKey('fk_id_edital', false, false, 'fk_id_edital');
        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo');
        $this->forge->addKey('fk_id_curso', false, false, 'fk_id_curso');

        $this->forge->addForeignKey('fk_id_cadastrado', 'tb_cadastrados', 'pk_id_cadastrado', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_edital', 'tb_editais', 'pk_id_edital', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_curso', 'tb_cursos_aperfeicoamentos', 'pk_id_curso', '', 'CASCADE');

        $this->forge->createTable('tb_cadastrados_aperfeicoamentos');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cadastrados_aperfeicoamentos');
    }
}
