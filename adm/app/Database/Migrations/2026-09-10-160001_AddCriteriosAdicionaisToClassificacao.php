<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCriteriosAdicionaisToClassificacao extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_classificacao', [
            'nr_total_criterios_adicionais' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'nr_total_aperfeicoamentos',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_classificacao', 'nr_total_criterios_adicionais');
    }
}
