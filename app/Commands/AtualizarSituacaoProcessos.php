<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\SituacaoProcessosService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;

/**
 * Comando CLI para atualizar a situação dos processos seletivos.
 *
 * Uso:
 *   php spark processos:atualizar-situacao              # Atualiza todos (modo global)
 *   php spark processos:atualizar-situacao --id=123      # Atualiza apenas o processo 123
 *   php spark processos:atualizar-situacao --global      # Mesmo comportamento padrão
 */
class AtualizarSituacaoProcessos extends BaseCommand
{
    protected $group       = 'Processos Seletivos';
    protected $name        = 'processos:atualizar-situacao';
    protected $description = 'Atualiza a situação (status) dos processos seletivos conforme regras do enum.';
    protected $usage       = 'processos:atualizar-situacao [options]';
    protected $arguments   = [];
    protected $options     = [
        '--id'     => 'ID do processo seletivo específico para atualizar.',
        '--global' => 'Atualiza todos os processos ativos (padrão quando --id não é informado).',
    ];

    /**
     * Extrai o valor de uma opção do CLI.
     * Ex: --id=123 ou --id 123
     */
    protected function extrairOpcao(string $nome, array $params): ?string
    {
        foreach ($params as $param) {
            if (strpos($param, "--{$nome}=") === 0) {
                return substr($param, strlen("--{$nome}="));
            }
            if ($param === "--{$nome}" && isset($params[array_search($param, $params) + 1])) {
                return $params[array_search($param, $params) + 1];
            }
        }
        return null;
    }

    public function run(array $params): void
    {
        $service = new SituacaoProcessosService();
        $id      = $this->extrairOpcao('id', $params);

        if ($id !== null) {
            $processoId = filter_var($id, FILTER_VALIDATE_INT);

            if ($processoId === false || $processoId <= 0) {
                CLI::error('ID do processo inválido: ' . $id);
                return;
            }

            CLI::write('Verificando situação do processo ID: ' . $processoId, 'yellow');

            try {
                $registro = $service->verificarSituacaoPorId($processoId);

                if ($registro === null) {
                    CLI::error('Processo não encontrado (ID: ' . $processoId . ').');
                    return;
                }

                $label = \App\Enums\Admin\SituacaoProcessosEnum::from((int) $registro->nm_id_situacao)->label();
                CLI::write('Situação atualizada com sucesso: ' . $label, 'green');
            } catch (Exception $e) {
                CLI::error('Erro ao atualizar situação: ' . $e->getMessage());
            }

            return;
        }

        // Modo global (padrão)
        CLI::write('Iniciando verificação global de situações...', 'yellow');

        try {
            $resultado = $service->verificarSituacaoGlobal();

            CLI::write('----------------------------------------');
            CLI::write('Total processados : ' . $resultado['total_processados']);
            CLI::write('Inseridos         : ' . $resultado['inseridos']);
            CLI::write('Atualizados       : ' . $resultado['atualizados']);
            CLI::write('Erros             : ' . count($resultado['erros']));
            CLI::write('----------------------------------------');

            if (count($resultado['erros']) > 0) {
                CLI::write('Detalhes dos erros:', 'red');
                foreach ($resultado['erros'] as $erro) {
                    CLI::write('  Processo ' . $erro['processo_id'] . ': ' . $erro['erro'], 'red');
                }
            }

            CLI::write('Verificação global concluída.', 'green');
        } catch (Exception $e) {
            CLI::error('Erro durante a verificação global: ' . $e->getMessage());
        }
    }
}
