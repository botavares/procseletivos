<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdSecretariaToTbProcessosDocumentos extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('fk_id_secretaria', 'tb_processos_documentos')) {
            $this->forge->addColumn('tb_processos_documentos', [
                'fk_id_secretaria' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                    'default'    => 0,
                    'comment'    => 'Chave estrangeira para tb_secretarias.pk_id_secretaria',
                ],
            ]);

            $this->forge->addKey('fk_id_secretaria', false, false, 'idx_proc_doc_secretaria');

            // Atualiza registros legados com a secretaria do processo vinculado
            $sql = 'UPDATE tb_processos_documentos d ';
            $sql .= 'INNER JOIN tb_processos p ON p.pk_id_processo = d.fk_processo_referencia ';
            $sql .= 'SET d.fk_id_secretaria = p.fk_id_secretaria ';
            $sql .= 'WHERE d.fk_id_secretaria = 0 AND p.fk_id_secretaria IS NOT NULL';
            $this->db->query($sql);
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fk_id_secretaria', 'tb_processos_documentos')) {
            $this->forge->dropColumn('tb_processos_documentos', 'fk_id_secretaria');
        }
    }
}
