<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbAperfeicoamentosCargosElegiveis extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_aperfeicoamento_cargo_elegivel' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_curso' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_cargo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_aperfeicoamento_cargo_elegivel');
        $this->forge->addUniqueKey(['fk_id_curso', 'fk_id_cargo'], 'uk_aperfeicoamento_cargo');

        $this->forge->addForeignKey('fk_id_curso', 'tb_cursos_aperfeicoamentos', 'pk_id_curso', '', 'CASCADE', 'fk_elegivel_aperfeicoamento_curso');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE', 'fk_elegivel_aperfeicoamento_cargo');

        $this->forge->createTable('tb_aperfeicoamentos_cargos_elegiveis');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_aperfeicoamentos_cargos_elegiveis');
    }
}
