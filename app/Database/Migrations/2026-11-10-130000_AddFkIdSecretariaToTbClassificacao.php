<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdSecretariaToTbClassificacao extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_secretaria', 'tb_classificacao')) {
            $this->forge->addColumn('tb_classificacao', [
                'fk_id_secretaria' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'pk_id_classificacao',
                ],
            ]);
        }

        $this->forge->addForeignKey('fk_id_secretaria', 'tb_secretarias', 'pk_id_secretaria', 'CASCADE', 'CASCADE', 'fk_classificacao_secretaria');
        $this->forge->addKey(['fk_id_secretaria', 'fk_id_processo', 'fk_id_cargo'], false, false, 'idx_classificacao_secretaria_processo_cargo');
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fk_id_secretaria', 'tb_classificacao')) {
            $this->forge->dropForeignKey('tb_classificacao', 'fk_classificacao_secretaria');
            $this->forge->dropColumn('tb_classificacao', 'fk_id_secretaria');
        }
    }
}
