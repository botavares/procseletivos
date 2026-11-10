<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdSecretariaToTbCadastradosExperiencias extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_secretaria', 'tb_cadastrados_experiencias')) {
            $this->forge->addColumn('tb_cadastrados_experiencias', [
                'fk_id_secretaria' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'pk_id_experiencias',
                ],
            ]);
        }

        $this->forge->addForeignKey('fk_id_secretaria', 'tb_secretarias', 'pk_id_secretaria', 'CASCADE', 'CASCADE', 'fk_cad_exp_secretaria');
        $this->forge->addKey(['fk_id_secretaria', 'fk_id_cadastrado', 'fk_id_processo', 'fk_id_cargo'], false, false, 'idx_cad_exp_secretaria');
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fk_id_secretaria', 'tb_cadastrados_experiencias')) {
            $this->forge->dropForeignKey('tb_cadastrados_experiencias', 'fk_cad_exp_secretaria');
            $this->forge->dropColumn('tb_cadastrados_experiencias', 'fk_id_secretaria');
        }
    }
}
