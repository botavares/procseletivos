<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbProcessosDocumentos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_documento' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_processo_referencia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Chave estrangeira para tb_processos.pk_id_processo',
            ],
            'nm_tipo' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'null'       => false,
                'default'    => 0,
                'comment'    => '1=Edital Abertura, 2=Retificação, 3=Resultado Parcial, 4=Homologação, 5=Edital Convocação, 6=Comunicado',
            ],
            'nm_numero_documento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'nm_ano_documento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_codigo_oficial' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'ds_link' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
            ],
            'ds_arquivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'nm_versao_vigente' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'null'       => false,
                'default'    => 0,
            ],
            'nm_documento_retificado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
            ],
            'is_deletado' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'NULL=Ativo, datetime=Soft deleted (CI4)',
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_documento');
        $this->forge->addKey('fk_processo_referencia', false, false, 'idx_proc_doc_processo');
        $this->forge->addKey('nm_tipo', false, false, 'idx_proc_doc_tipo');
        $this->forge->addKey(['nm_numero_documento', 'nm_ano_documento'], false, false, 'idx_proc_doc_num_ano');
        $this->forge->addKey('nm_documento_retificado', false, false, 'idx_proc_doc_retificado');

        $this->forge->createTable('tb_processos_documentos');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_processos_documentos');
    }
}
