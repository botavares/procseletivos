<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdSecretariaToTbCargosExperiencias extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_secretaria', 'tb_cargos_experiencias')) {
            $this->forge->addColumn('tb_cargos_experiencias', [
                'fk_id_secretaria' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'pk_id_cargos_experiencia',
                ],
            ]);
        }

        $this->forge->addForeignKey('fk_id_secretaria', 'tb_secretarias', 'pk_id_secretaria', 'CASCADE', 'CASCADE', 'fk_cargos_exp_secretaria');
        $this->forge->addKey(['fk_id_secretaria', 'fk_id_cargo', 'fk_id_experiencia'], false, false, 'idx_cargos_exp_secretaria');
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fk_id_secretaria', 'tb_cargos_experiencias')) {
            $this->forge->dropForeignKey('tb_cargos_experiencias', 'fk_cargos_exp_secretaria');
            $this->forge->dropColumn('tb_cargos_experiencias', 'fk_id_secretaria');
        }
    }
}
