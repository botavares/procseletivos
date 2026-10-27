<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCargosAperfeicoamentos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_cargo_aperfeicoamento' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_processos_seletivo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
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
            'ds_pontuacao_minima' => [
                'type'       => 'FLOAT',
                'null'       => false,
                'default'    => 0,
            ],
            'ds_pontuacao_maxima' => [
                'type'       => 'FLOAT',
                'null'       => false,
                'default'    => 0,
            ],
            'ds_tipo_campo' => [
                'type'       => 'CHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'INPUT, CHECK',
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_cargo_aperfeicoamento');
        $this->forge->addUniqueKey(['fk_id_processos_seletivo', 'fk_id_cargo', 'fk_id_curso']);
        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo');
        $this->forge->addKey('fk_id_curso', false, false, 'fk_id_curso');

        $this->forge->addForeignKey('fk_id_processos_seletivo', 'tb_processos', 'pk_id_processo', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_curso', 'tb_cursos_aperfeicoamentos', 'pk_id_curso', '', 'CASCADE');

        $this->forge->createTable('tb_cargos_aperfeicoamentos');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cargos_aperfeicoamentos');
    }
}
