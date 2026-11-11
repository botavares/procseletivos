<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDtDataSubstituicaoProcessosDocumentos extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('tb_processos_documentos', [
            'dt_data_substituicao' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'Data e hora em que o edital foi substituido por uma retificacao',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('tb_processos_documentos', 'dt_data_substituicao');
    }
}
