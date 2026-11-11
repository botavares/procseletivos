<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoClassificacaoToTbClassificacao extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('ds_tipo_classificacao', 'tb_classificacao')) {
            $this->forge->addColumn('tb_classificacao', [
                'ds_tipo_classificacao' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'comment'    => 'preliminar ou homologacao',
                    'after'      => 'fk_id_cargo',
                ],
            ]);
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('ds_tipo_classificacao', 'tb_classificacao')) {
            $this->forge->dropColumn('tb_classificacao', 'ds_tipo_classificacao');
        }
    }
}
