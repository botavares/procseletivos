<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkProcessoSeletivoToTbCargosDesempatesConfig extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('fk_id_processo_seletivo', 'tb_classificacao_desempates_config')) {
            $this->forge->addColumn('tb_classificacao_desempates_config', [
                'fk_id_processo_seletivo' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'fk_id_cargo',
                ],
            ]);
        }

        $this->forge->addForeignKey('fk_id_processo_seletivo', 'tb_processos', 'pk_id_processo', 'CASCADE', 'CASCADE', 'fk_processo_classificacao_desempates_config');
        $this->forge->addKey(['fk_id_processo_seletivo', 'fk_id_cargo', 'ds_ordem']);
    }

    public function down()
    {
        if ($this->db->fieldExists('fk_id_processo_seletivo', 'tb_classificacao_desempates_config')) {
            $this->forge->dropForeignKey('tb_classificacao_desempates_config', 'fk_processo_classificacao_desempates_config');
            $this->forge->dropColumn('tb_classificacao_desempates_config', 'fk_id_processo_seletivo');
        }
    }
}
