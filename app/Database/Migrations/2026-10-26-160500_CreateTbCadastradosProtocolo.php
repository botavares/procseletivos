<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCadastradosProtocolo extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_processo' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_cadastrado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_secretaria' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
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
            'ds_cpf_cadastrado' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => '',
            ],
            'ds_protocolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => '',
            ],
            'ds_data_protocolo' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'ds_hora_protocolo' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'ds_ip_protocolo' => [
                'type'       => 'CHAR',
                'constraint' => 50,
                'null'       => false,
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_processo');
        $this->forge->addKey('fk_id_cadastrado', false, false, 'fk_protocolo_cadastrado');
        $this->forge->addKey('fk_id_edital', false, false, 'fk_id_edital');
        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo');
        $this->forge->addKey('fk_id_secretaria', false, false, 'fk_cad_prot_secretaria');
        $this->forge->addKey('ds_cpf_cadastrado', false, false, 'ds_cpf_cadastrado');
        $this->forge->addKey('ds_protocolo', false, false, 'ds_protocolo');
        $this->forge->addKey('ds_data_protocolo', false, false, 'ds_data_protocolo');

        $this->forge->addForeignKey('fk_id_cadastrado', 'tb_cadastrados', 'pk_id_cadastrado', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_secretaria', 'tb_secretarias', 'pk_id_secretaria', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_edital', 'tb_editais', 'pk_id_edital', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE');

        $this->forge->createTable('tb_cadastrados_protocolo');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cadastrados_protocolo');
    }
}
