<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkIdCargoToTbEscolaridades extends Migration
{
    public function up(): void
    {
        // Remove o vinculo 1:N anterior caso exista
        if ($this->db->fieldExists('fk_id_cargo', 'tb_escolaridades')) {
            $constraints = $this->db->query("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'tb_escolaridades' 
                AND CONSTRAINT_NAME = 'fk_escolaridades_cargo'
            ")->getRow();
            if ($constraints) {
                $this->forge->dropForeignKey('tb_escolaridades', 'fk_escolaridades_cargo');
            }
            $this->forge->dropColumn('tb_escolaridades', 'fk_id_cargo');
        }

        // Cria tabela de elegibilidade N:M escolaridade x cargo
        $this->forge->addField([
            'pk_id_escolaridade_cargo_elegivel' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_id_escolaridade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'fk_id_cargo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        $this->forge->addPrimaryKey('pk_id_escolaridade_cargo_elegivel');
        $this->forge->addUniqueKey(['fk_id_escolaridade', 'fk_id_cargo'], 'uk_escolaridade_cargo');

        $this->forge->addForeignKey('fk_id_escolaridade', 'tb_escolaridades', 'pk_id_escolaridade', '', 'CASCADE', 'fk_elegivel_escolaridade');
        $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE', 'fk_elegivel_cargo');

        $this->forge->createTable('tb_escolaridades_cargos_elegiveis');
    }

    public function down(): void
    {
        $this->forge->dropTable('tb_escolaridades_cargos_elegiveis');

        if (! $this->db->fieldExists('fk_id_cargo', 'tb_escolaridades')) {
            $this->forge->addColumn('tb_escolaridades', [
                'fk_id_cargo' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'pk_id_escolaridade',
                ],
            ]);
            $this->forge->addKey('fk_id_cargo', false, false, 'fk_id_cargo_escolaridades');
            $this->forge->addForeignKey('fk_id_cargo', 'tb_cargos', 'pk_id_cargo', '', 'CASCADE', 'fk_escolaridades_cargo');
        }
    }
}
