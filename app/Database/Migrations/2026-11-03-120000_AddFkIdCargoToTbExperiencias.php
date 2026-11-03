<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdCargoToTbExperiencias extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_cargo', 'tb_experiencias')) {
            $this->forge->addColumn('tb_experiencias', [
                'fk_id_cargo' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'pk_id_experiencia',
                ],
            ]);
        }

        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo_experiencias');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE', 'fk_experiencias_cargo');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('tb_experiencias', 'fk_experiencias_cargo');
        $this->forge->dropColumn('tb_experiencias', 'fk_id_cargo');
    }
}
