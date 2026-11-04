<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTbCadastradosExperienciasFkProcesso extends Migration
{
    public function up(): void
    {
        // 1) Remove FK antiga (nome pode variar) da coluna fk_id_edital
        $fkAntiga = $this->db->query("
            SELECT tc.CONSTRAINT_NAME, rc.REFERENCED_TABLE_NAME 
            FROM information_schema.REFERENTIAL_CONSTRAINTS rc
            JOIN information_schema.TABLE_CONSTRAINTS tc 
                ON rc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME 
                AND rc.CONSTRAINT_SCHEMA = tc.TABLE_SCHEMA
            WHERE tc.TABLE_SCHEMA = DATABASE() 
            AND tc.TABLE_NAME = 'tb_cadastrados_experiencias' 
            AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND rc.REFERENCED_TABLE_NAME = 'tb_editais'
        ")->getRow();
        if ($fkAntiga) {
            $this->forge->dropForeignKey('tb_cadastrados_experiencias', $fkAntiga->CONSTRAINT_NAME);
        }

        // 2) Renomeia coluna fk_id_edital -> fk_id_processo, renomeia índices e cria nova FK
        $this->db->query("
            ALTER TABLE `tb_cadastrados_experiencias`
            CHANGE `fk_id_edital` `fk_id_processo` INT(11) NOT NULL,
            DROP INDEX `fk_id_cadastrado_fk_id_edital_fk_id_cargo_fk_id_experiencia`,
            DROP INDEX `fk_id_edital`,
            ADD UNIQUE KEY `uk_cadastrado_processo_cargo_experiencia` (`fk_id_cadastrado`, `fk_id_processo`, `fk_id_cargo`, `fk_id_experiencia`),
            ADD KEY `fk_id_processo` (`fk_id_processo`),
            ADD CONSTRAINT `fk_cad_exp_processo` FOREIGN KEY (`fk_id_processo`) REFERENCES `tb_processos` (`pk_id_processo`) ON DELETE CASCADE ON UPDATE CASCADE
        ");
    }

    public function down(): void
    {
        $this->db->query("
            ALTER TABLE `tb_cadastrados_experiencias`
            DROP FOREIGN KEY `fk_cad_exp_processo`,
            CHANGE `fk_id_processo` `fk_id_edital` INT(11) NOT NULL,
            DROP INDEX `uk_cadastrado_processo_cargo_experiencia`,
            ADD UNIQUE KEY `fk_id_cadastrado_fk_id_edital_fk_id_cargo_fk_id_experiencia` (`fk_id_cadastrado`, `fk_id_edital`, `fk_id_cargo`, `fk_id_experiencia`),
            DROP INDEX `fk_id_processo`,
            ADD KEY `fk_id_edital` (`fk_id_edital`),
            ADD CONSTRAINT `fk_cad_exp_edital` FOREIGN KEY (`fk_id_edital`) REFERENCES `tb_editais` (`pk_id_edital`) ON DELETE CASCADE ON UPDATE CASCADE
        ");
    }
}
