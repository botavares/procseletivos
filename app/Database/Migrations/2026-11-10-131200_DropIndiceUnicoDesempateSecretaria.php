<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Remove o índice único acidental criado em tb_classificacao_desempates_config
 * e recria como índice comum, pois um cargo/process pode ter vários critérios.
 */
class DropIndiceUnicoDesempateSecretaria extends Migration
{
    public function up(): void
    {
        // 1) Remove a FK que depende do índice único
        $this->forge->dropForeignKey('tb_classificacao_desempates_config', 'tb_cargos_desempates_config_fk_id_secretaria_foreign');

        // 2) Remove o índice problemático
        $this->forge->dropKey('tb_classificacao_desempates_config', 'fk_id_secretaria_fk_id_processo_seletivo_fk_id_cargo');

        // 3) Cria índice não único para consultas
        $this->forge->addKey(
            ['fk_id_secretaria', 'fk_id_processo_seletivo', 'fk_id_cargo'],
            false,
            false,
            'idx_desempate_secretaria_processo_cargo'
        );

        // 4) Recria a FK via SQL raw (CodeIgniter nao aplica addForeignKey em alteracoes automaticamente)
        $this->db->query("
            ALTER TABLE `tb_classificacao_desempates_config`
            ADD CONSTRAINT `fk_desempate_secretaria`
            FOREIGN KEY (`fk_id_secretaria`) REFERENCES `tb_secretarias` (`pk_id_secretaria`)
            ON DELETE CASCADE ON UPDATE CASCADE
        ");
    }

    public function down(): void
    {
        $this->db->query("ALTER TABLE `tb_classificacao_desempates_config` DROP FOREIGN KEY `fk_desempate_secretaria`");
        $this->forge->dropKey('tb_classificacao_desempates_config', 'idx_desempate_secretaria_processo_cargo');
        $this->forge->addKey(
            ['fk_id_secretaria', 'fk_id_processo_seletivo', 'fk_id_cargo'],
            false,
            true,
            'fk_id_secretaria_fk_id_processo_seletivo_fk_id_cargo'
        );
        $this->db->query("
            ALTER TABLE `tb_classificacao_desempates_config`
            ADD CONSTRAINT `fk_desempate_secretaria`
            FOREIGN KEY (`fk_id_secretaria`) REFERENCES `tb_secretarias` (`pk_id_secretaria`)
            ON DELETE CASCADE ON UPDATE CASCADE
        ");
    }
}
