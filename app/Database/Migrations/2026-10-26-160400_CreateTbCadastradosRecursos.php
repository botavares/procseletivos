<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCadastradosRecursos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_historico' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_edital' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_cargo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_candidato' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_campo_alterado' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'fk_id_campo_alterado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'ds_valor_antigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'ds_valor_novo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'ds_numero_protocolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'ds_usuario_responsavel' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'ds_data_alteracao' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'ds_hora_alteracao' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'ds_observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_historico');
        $this->forge->addKey('fk_id_candidato', false, false, 'candidato_recursos');
        $this->forge->addKey('fk_id_edital', false, false, 'fk_id_edital');
        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo');
        $this->forge->addKey('ds_numero_protocolo', false, false, 'ds_numero_protocolo');
        $this->forge->addKey('ds_data_alteracao', false, false, 'ds_data_alteracao');

        $this->forge->addForeignKey('fk_id_candidato', 'tb_cadastrados', 'pk_id_cadastrado', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_edital', 'tb_editais', 'pk_id_edital', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE');

        $this->forge->createTable('tb_cadastrados_recursos');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cadastrados_recursos');
    }
}
