<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCargosExperiencias extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'pk_id_cargos_experiencia' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_processo_seletivo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'fk_id_cargo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_experiencia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'ds_pontuacao_minima' => [
                'type'       => 'FLOAT',
                'null'       => false,
                'default'    => 0,
            ],
            'ds_pontuacao_maxima' => [
                'type'       => 'FLOAT',
                'null'       => false,
                'default'    => 0,
            ],
            'ds_tipo_campo' => [
                'type'       => 'CHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'INPUT = TEXTO / SELECT = DROPDOWN',
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_cargos_experiencia');
        $this->forge->addUniqueKey(['fk_id_processo_seletivo', 'fk_id_cargo', 'fk_id_experiencia']);
        $this->forge->addKey('fk_id_experiencia', false, false, 'fk_id_experiencia');
        $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo');

        $this->forge->addForeignKey('fk_id_processo_seletivo', 'tb_processos', 'pk_id_processo', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE');
        $this->forge->addForeignKey('fk_id_experiencia', 'tb_experiencias', 'pk_id_experiencia', '', 'CASCADE');

        $this->forge->createTable('tb_cargos_experiencias');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_cargos_experiencias');
    }
}
