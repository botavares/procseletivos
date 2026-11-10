<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdSecretariaToTbCargosAperfeicoamentos extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_secretaria', 'tb_cargos_aperfeicoamentos')) {
            $this->forge->addColumn('tb_cargos_aperfeicoamentos', [
                'fk_id_secretaria' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'pk_id_cargo_aperfeicoamento',
                ],
            ]);
        }

        $this->forge->addForeignKey('fk_id_secretaria', 'tb_secretarias', 'pk_id_secretaria', 'CASCADE', 'CASCADE', 'fk_cargos_ape_secretaria');
        $this->forge->addKey(['fk_id_secretaria', 'fk_id_cargo', 'fk_id_curso'], false, false, 'idx_cargos_ape_secretaria');
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fk_id_secretaria', 'tb_cargos_aperfeicoamentos')) {
            $this->forge->dropForeignKey('tb_cargos_aperfeicoamentos', 'fk_cargos_ape_secretaria');
            $this->forge->dropColumn('tb_cargos_aperfeicoamentos', 'fk_id_secretaria');
        }
    }
}
