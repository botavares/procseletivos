<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTbCargosDesempatesConfigToTbClassificacaoDesempatesConfig extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tb_cargos_desempates_config')) {
            $this->forge->renameTable('tb_cargos_desempates_config', 'tb_classificacao_desempates_config');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('tb_classificacao_desempates_config')) {
            $this->forge->renameTable('tb_classificacao_desempates_config', 'tb_cargos_desempates_config');
        }
    }
}
