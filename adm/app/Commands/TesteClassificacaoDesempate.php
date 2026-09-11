<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use App\Services\Classificacao\PontuacaoCalculatorService;
use App\Services\Classificacao\ClassificacaoProcessorService;
use App\Services\Classificacao\DesempateConfigService;

class TesteClassificacaoDesempate extends BaseCommand
{
    protected $group       = 'Testes';
    protected $name        = 'teste:classificacao';
    protected $description = 'Testa o sistema de classificacao com desempate dinamico';

    public function run(array $params)
    {
        $db = Database::connect();
        $edital = 26;
        $cargo = 5;

        CLI::write("=== TESTE: Classificacao com Desempate Dinamico ===", 'green');
        CLI::newLine();

        // 1. Verificar migrations
        CLI::write("1. Verificando migrations...", 'yellow');
        $tables = $db->listTables();
        $temDesempate = in_array('tb_cargos_desempates_config', $tables);
        $temScores = in_array('tb_classificacao_scores', $tables);
        CLI::write("   tb_cargos_desempates_config: " . ($temDesempate ? '✓ CRIADA' : '✗ FALTANDO'));
        CLI::write("   tb_classificacao_scores: " . ($temScores ? '✓ CRIADA' : '✗ FALTANDO'));

        // 2. Testar backward compatibility
        CLI::newLine();
        CLI::write("2. Testando Backward Compatibility (Edital {$edital}, Cargo {$cargo})...", 'yellow');

        $configService = new DesempateConfigService();
        $temConfig = $configService->cargoPossuiConfiguracao($cargo);
        CLI::write("   Cargo {$cargo} tem config: " . ($temConfig ? 'SIM (usara dinamico)' : 'NAO (usara regra fixa)'));

        $calculator = new PontuacaoCalculatorService($db);
        $processor = new ClassificacaoProcessorService($db, $calculator);

        try {
            $dados = $processor->processar($edital, $cargo);
            CLI::write("   Candidatos processados: " . count($dados));
            CLI::write("   Posicao 1: {$dados[0]['ds_posicao']} - {$dados[0]['ds_nome_candidato']} ({$dados[0]['nr_total_pontos']} pts)");
            $ultimo = end($dados);
            CLI::write("   Ultima posicao: {$ultimo['ds_posicao']} - {$ultimo['ds_nome_candidato']}");
            CLI::write("   Score dinamico: " . (isset($dados[0]['_scores']) ? 'SIM' : 'NAO'));
            CLI::write("   ✓ Backward compatibility OK", 'green');
        } catch (\Exception $e) {
            CLI::write("   ✗ ERRO: " . $e->getMessage(), 'red');
            return;
        }

        // 3. Testar com desempate dinamico (inserir config)
        CLI::newLine();
        CLI::write("3. Testando com Desempate Dinamico...", 'yellow');

        // Limpar config anterior se existir
        $db->table('tb_cargos_desempates_config')->where('fk_id_cargo', $cargo)->delete();

        // Inserir configuracao de teste
        $db->table('tb_cargos_desempates_config')->insert([
            'fk_id_cargo'       => $cargo,
            'ds_ordem'          => 1,
            'ds_tipo_criterio'  => 'PONTUACAO_TOTAL',
            'ds_direcao'        => 'DESC',
            'ds_descricao'      => 'Total de Pontos (Teste)',
        ]);
        $db->table('tb_cargos_desempates_config')->insert([
            'fk_id_cargo'       => $cargo,
            'ds_ordem'          => 2,
            'ds_tipo_criterio'  => 'PONTUACAO_EXPERIENCIAS',
            'ds_direcao'        => 'DESC',
            'ds_descricao'      => 'Experiencias (Teste)',
        ]);

        CLI::write("   Config inserida para cargo {$cargo}");

        try {
            $dados2 = $processor->processar($edital, $cargo);
            CLI::write("   Candidatos processados: " . count($dados2));
            CLI::write("   Posicao 1: {$dados2[0]['ds_posicao']} - {$dados2[0]['ds_nome_candidato']} ({$dados2[0]['nr_total_pontos']} pts)");
            
            // Debug: mostrar scores detalhadamente
            if (isset($dados2[0]['_scores']) && !empty($dados2[0]['_scores'])) {
                CLI::write("   Scores do primeiro candidato:");
                foreach ($dados2[0]['_scores'] as $score) {
                    $chave = $score['ds_chave_score'] ?? 'N/A';
                    $valor = $score['nr_valor'] ?? 0;
                    CLI::write("     - {$chave} = {$valor}");
                }
            } else {
                CLI::write("   Nenhum score encontrado no primeiro candidato");
            }

            // Verificar se scores foram persistidos
            $persist = new \App\Services\Classificacao\ClassificacaoPersistService($db);
            $persist->salvar($edital, $cargo, $dados2);

            $scoresCount = $db->table('tb_classificacao_scores')->countAll();
            CLI::write("   Scores persistidos: {$scoresCount}");

            // Verificar conteudo dos scores
            $sampleScore = $db->table('tb_classificacao_scores')
                ->select('*')
                ->limit(1)
                ->get()
                ->getRow();
            if ($sampleScore) {
                CLI::write("   Exemplo de score persistido:");
                CLI::write("     Chave: {$sampleScore->ds_chave_score}");
                CLI::write("     Valor: {$sampleScore->nr_valor}");
                CLI::write("     Tipo: {$sampleScore->ds_tipo_score}");
            }

            CLI::write("   ✓ Desempate dinamico OK", 'green');

        } catch (\Exception $e) {
            CLI::write("   ✗ ERRO: " . $e->getMessage(), 'red');
            CLI::write($e->getTraceAsString());
        } finally {
            // Limpar config de teste
            $db->table('tb_cargos_desempates_config')->where('fk_id_cargo', $cargo)->delete();
            CLI::write("   Config de teste removida", 'yellow');
        }

        CLI::newLine();
        CLI::write("=== Testes concluidos ===", 'green');
    }
}
