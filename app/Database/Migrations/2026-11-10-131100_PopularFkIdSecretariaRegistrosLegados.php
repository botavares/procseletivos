<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Popula fk_id_secretaria em registros legados das tabelas classificatorias
 * e de configuracao de desempate, usando a secretaria do cargo ou do protocolo.
 */
class PopularFkIdSecretariaRegistrosLegados extends Migration
{
    public function up(): void
    {
        $db = $this->db;

        // 1) Configuracoes de cargos: fk_id_secretaria vem do cargo
        $tabelasCargos = [
            'tb_cargos_experiencias',
            'tb_cargos_escolaridades',
            'tb_cargos_aperfeicoamentos',
            'tb_cargos_criterios_adicionais',
        ];

        foreach ($tabelasCargos as $tabela) {
            if (! $db->fieldExists('fk_id_secretaria', $tabela)) {
                continue;
            }

            $db->query("
                UPDATE {$tabela} t
                JOIN tb_cargos c ON c.pk_id_cargo = t.fk_id_cargo
                SET t.fk_id_secretaria = c.fk_id_secretaria
                WHERE t.fk_id_secretaria IS NULL
                  AND c.fk_id_secretaria IS NOT NULL
            ");
        }

        // 2) Dados classificatorios dos candidatos: fk_id_secretaria vem do protocolo
        $tabelasCandidatos = [
            'tb_cadastrados_experiencias',
            'tb_cadastrados_escolaridades',
            'tb_cadastrados_aperfeicoamentos',
            'tb_cadastrados_criterios',
        ];

        foreach ($tabelasCandidatos as $tabela) {
            if (! $db->fieldExists('fk_id_secretaria', $tabela)) {
                continue;
            }

            // Primeiro tenta pelo protocolo
            $db->query("
                UPDATE {$tabela} t
                JOIN tb_cadastrados_protocolo p
                  ON p.fk_id_cadastrado = t.fk_id_cadastrado
                 AND p.fk_id_processo    = t.fk_id_processo
                 AND p.fk_id_cargo       = t.fk_id_cargo
                SET t.fk_id_secretaria = p.fk_id_secretaria
                WHERE t.fk_id_secretaria IS NULL
                  AND p.fk_id_secretaria IS NOT NULL
            ");

            // Fallback: secretaria do cargo
            $db->query("
                UPDATE {$tabela} t
                JOIN tb_cargos c ON c.pk_id_cargo = t.fk_id_cargo
                SET t.fk_id_secretaria = c.fk_id_secretaria
                WHERE t.fk_id_secretaria IS NULL
                  AND c.fk_id_secretaria IS NOT NULL
            ");

            // Fallback final: secretaria do processo
            $db->query("
                UPDATE {$tabela} t
                JOIN tb_processos pr ON pr.pk_id_processo = t.fk_id_processo
                SET t.fk_id_secretaria = pr.fk_id_secretaria
                WHERE t.fk_id_secretaria IS NULL
                  AND pr.fk_id_secretaria IS NOT NULL
            ");
        }

        // 3) Configuracao de criterios de desempate: fk_id_secretaria vem do cargo
        if ($db->fieldExists('fk_id_secretaria', 'tb_classificacao_desempates_config')) {
            $db->query("
                UPDATE tb_classificacao_desempates_config t
                JOIN tb_cargos c ON c.pk_id_cargo = t.fk_id_cargo
                SET t.fk_id_secretaria = c.fk_id_secretaria
                WHERE t.fk_id_secretaria IS NULL
                  AND c.fk_id_secretaria IS NOT NULL
            ");

            $db->query("
                UPDATE tb_classificacao_desempates_config t
                JOIN tb_processos pr ON pr.pk_id_processo = t.fk_id_processo_seletivo
                SET t.fk_id_secretaria = pr.fk_id_secretaria
                WHERE t.fk_id_secretaria IS NULL
                  AND t.fk_id_processo_seletivo IS NOT NULL
                  AND pr.fk_id_secretaria IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        // Nao ha rollback seguro para dados legados sem backup.
        // Em caso de reversao, as colunas serao removidas pelas migrations anteriores.
    }
}
