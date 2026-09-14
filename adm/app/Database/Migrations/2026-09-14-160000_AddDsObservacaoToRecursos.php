<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDsObservacaoToRecursos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_cadastrados_recursos', [
            'ds_observacao' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'ds_valor_novo',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_cadastrados_recursos', 'ds_observacao');
    }
}
