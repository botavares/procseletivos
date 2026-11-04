<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCadastradosProcessos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_cadastrado_processo' => [
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
            'fk_id_processo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_cargo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_escolaridade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_protocolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'nm_situacao' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'null'       => false,
                'default'    => 1,
                'comment'    => '1=Inscrito, 2=Homologado, 3=Desclassificado, 4=Classificado',
            ],
            'dt_inscricao' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'NULL=Ativo, datetime=Soft deleted (CI4)',
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_cadastrado_processo');
        $this->forge->addUniqueKey(['fk_id_cadastrado', 'fk_id_processo', 'fk_id_cargo', 'fk_id_escolaridade'], 'uk_cadastro_processo_cargo_escolaridade');

        $this->forge->addKey('fk_id_cadastrado', false, false, 'idx_cadastrado');
        $this->forge->addKey('fk_id_processo', false, false, 'idx_processo');
        $this->forge->addKey('fk_id_cargo', false, false, 'idx_cargo');
        $this->forge->addKey('fk_id_escolaridade', false, false, 'idx_escolaridade');
        $this->forge->addKey('ds_protocolo', false, false, 'idx_protocolo');
        $this->forge->addKey('nm_situacao', false, false, 'idx_situacao');
        $this->forge->addKey(['fk_id_processo', 'nm_situacao'], false, false, 'idx_processo_situacao');

        $this->forge->addForeignKey('fk_id_cadastrado', 'tb_cadastrados', 'pk_id_cadastrado', 'CASCADE', 'CASCADE', 'fk_cadastrados_processos_cadastrado');
        $this->forge->addForeignKey('fk_id_processo', 'tb_processos', 'pk_id_processo', 'CASCADE', 'CASCADE', 'fk_cadastrados_processos_processo');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', 'CASCADE', 'CASCADE', 'fk_cadastrados_processos_cargo');
        $this->forge->addForeignKey('fk_id_escolaridade', 'tb_escolaridades', 'pk_id_escolaridade', 'CASCADE', 'CASCADE', 'fk_cadastrados_processos_escolaridade');

        $this->forge->createTable('tb_cadastrados_processos');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cadastrados_processos');
    }
}
