<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdCargoToTbCriteriosAdicionais extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_cargo', 'tb_criterios_adicionais')) {
            $this->forge->addColumn('tb_criterios_adicionais', [
                'fk_id_cargo' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'pk_id_criterio',
                ],
            ]);
        }

        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo_criterios');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE', 'fk_criterios_cargo');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('tb_criterios_adicionais', 'fk_criterios_cargo');
        $this->forge->dropColumn('tb_criterios_adicionais', 'fk_id_cargo');
    }
}
