<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdSecretariaToTbClassificacaoDesempatesConfig extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_secretaria', 'tb_classificacao_desempates_config')) {
            $this->forge->addColumn('tb_classificacao_desempates_config', [
                'fk_id_secretaria' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'pk_id_desempate',
                ],
            ]);
        }

        $this->forge->addForeignKey('fk_id_secretaria', 'tb_secretarias', 'pk_id_secretaria', 'CASCADE', 'CASCADE', 'fk_desempate_secretaria');
        $this->forge->addKey(['fk_id_secretaria', 'fk_id_cargo', 'fk_id_processo_seletivo'], false, false, 'idx_desempate_secretaria_cargo_processo');
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fk_id_secretaria', 'tb_classificacao_desempates_config')) {
            $this->forge->dropForeignKey('tb_classificacao_desempates_config', 'fk_desempate_secretaria');
            $this->forge->dropColumn('tb_classificacao_desempates_config', 'fk_id_secretaria');
        }
    }
}
