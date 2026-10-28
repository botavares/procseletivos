<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDtDataPublicacaoProcessosDocumentos extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('tb_processos_documentos', [
            'dt_data_publicacao' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'Data e hora da publicacao do documento',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('tb_processos_documentos', 'dt_data_publicacao');
    }
}
