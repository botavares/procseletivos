<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Admin\SituacaoProcessosEnum;
use App\Models\Admin\LogsModel;
use App\Models\ProcessosSeletivosModel;
use App\Models\SituacaoProcessosModel;
use DateTime;
use Exception;

class SituacaoProcessosService
{
    protected ProcessosSeletivosModel $processosModel;
    protected SituacaoProcessosModel $situacaoModel;
    protected LogsModel $logsModel;

    public function __construct()
    {
        $this->processosModel = new ProcessosSeletivosModel();
        $this->situacaoModel   = new SituacaoProcessosModel();
        $this->logsModel       = new LogsModel();
    }

    /**
     * Calcula a situação de um processo seletivo com base nas regras do enum.
     */
    public function calcularSituacao(object $processo): SituacaoProcessosEnum
    {
        $hoje   = new DateTime('today');
        $inicio = new DateTime($processo->dt_inicio_inscricao);
        $fim    = new DateTime($processo->dt_fim_inscricao);

        // Regra 1: Agendado — inscrições ainda não começaram
        if ($hoje < $inicio) {
            return SituacaoProcessosEnum::AGENDADO;
        }

        // Regra 2 & 3: Período de inscrições (dt_divulgacao_resultado é null)
        if (empty($processo->dt_divulga_resultado)) {
            if ($hoje <= $fim) {
                return SituacaoProcessosEnum::INSCRICOES_ABERTAS;
            }
            return SituacaoProcessosEnum::INSCRICOES_ENCERRADAS;
        }

        // Regra 4: Resultado publicado — há dt_divulgacao_resultado, mas sem homologação
        if (empty($processo->dt_data_homologacao)) {
            return SituacaoProcessosEnum::RESULTADO_PUBLICADO;
        }

        // Regra 5 & 6: Vigente ou Encerrado (baseado em homologação + meses de validade)
        $homologacao = new DateTime($processo->dt_data_homologacao);
        $mesesValidade = (int) $processo->nm_meses_validade;

        if (!empty($processo->nm_prorrogado) && (int) $processo->nm_prorrogado === 1) {
            $mesesValidade *= 2;
        }

        $dataLimite = clone $homologacao;
        $dataLimite->modify("+{$mesesValidade} months");

        if ($hoje <= $dataLimite) {
            return SituacaoProcessosEnum::VIGENTE;
        }

        return SituacaoProcessosEnum::ENCERRADO;
    }

    /**
     * Verifica a situação de todos os processos seletivos ativos.
     * Retorna estatísticas da execução.
     */
    public function verificarSituacaoGlobal(): array
    {
        $builder = $this->processosModel->builder();

        $processos = $builder
            ->where('is_deletado IS NULL')
            ->orWhere("is_deletado = '0000-00-00 00:00:00'")
            ->get()
            ->getResult();

        $atualizados = 0;
        $inseridos   = 0;
        $erros       = [];

        foreach ($processos as $processo) {
            try {
                $resultado = $this->processarProcesso($processo);
                if ($resultado['acao'] === 'insert') {
                    $inseridos++;
                } else {
                    $atualizados++;
                }
            } catch (Exception $e) {
                $erros[] = [
                    'processo_id' => $processo->pk_id_processo,
                    'erro'        => $e->getMessage(),
                ];
            }
        }

        return [
            'total_processados' => count($processos),
            'inseridos'         => $inseridos,
            'atualizados'       => $atualizados,
            'erros'             => $erros,
        ];
    }

    /**
     * Verifica a situação de um processo seletivo específico.
     * Insere ou atualiza tb_situacao_processos e tb_processos.nm_situacao.
     * Retorna o registro da situação ou null se o processo não existir.
     */
    public function verificarSituacaoPorId(int $processoId): ?object
    {
        $processo = $this->processosModel->find($processoId);

        if (!$processo) {
            return null;
        }

        $this->processarProcesso($processo);

        return $this->situacaoModel->porProcesso($processoId);
    }

    /**
     * Processa um único objeto de processo:
     * calcula situação, persiste em tb_situacao_processos,
     * atualiza tb_processos.nm_situacao e registra log se houver mudança.
     */
    protected function processarProcesso(object $processo): array
    {
        $situacaoCalculada = $this->calcularSituacao($processo);
        $novoStatus        = $situacaoCalculada->value;
        $processoId        = (int) $processo->pk_id_processo;

        $registroAtual = $this->situacaoModel->porProcesso($processoId);
        $statusAnterior = $registroAtual ? (int) $registroAtual->nm_id_situacao : null;

        $dados = [
            'fk_id_processo'      => $processoId,
            'nm_id_situacao'      => $novoStatus,
            'dt_ultima_alteracao' => date('Y-m-d H:i:s'),
        ];

        if ($registroAtual) {
            $this->situacaoModel->update($registroAtual->pk_id_situacao_processo, $dados);
            $acao = 'update';
        } else {
            $this->situacaoModel->insert($dados);
            $acao = 'insert';
        }

        // Sincroniza tb_processos.nm_situacao para manter consistência
        if ((int) $processo->nm_situacao !== $novoStatus) {
            $this->processosModel->update($processoId, ['nm_situacao' => $novoStatus]);
        }

        // Registra log apenas quando houve mudança real de situação
        if ($statusAnterior !== null && $statusAnterior !== $novoStatus) {
            $labelAnterior = SituacaoProcessosEnum::from($statusAnterior)->label();
            $labelNovo     = $situacaoCalculada->label();

            $this->registrarLog(
                'ALTERAÇÃO DE SITUAÇÃO',
                "Processo {$processoId}: {$labelAnterior} → {$labelNovo}",
                'tb_situacao_processos'
            );
        } elseif ($statusAnterior === null) {
            $this->registrarLog(
                'CRIAÇÃO DE SITUAÇÃO',
                "Processo {$processoId}: situacao inicial definida como {$situacaoCalculada->label()}",
                'tb_situacao_processos'
            );
        }

        return ['acao' => $acao, 'situacao' => $novoStatus];
    }

    /**
     * Registra log em tb_logs.
     */
    protected function registrarLog(string $acao, string $mensagem, string $tabela): void
    {
        $idUsuario   = session('id') ?: 0;
        $nomeUsuario = session('nome') ?: 'SISTEMA';

        $this->logsModel->insert([
            'id_usuario'    => $idUsuario,
            'ds_usuario'    => $nomeUsuario,
            'ds_acao'       => $acao,
            'ds_tabela'     => $tabela,
            'ds_mensagem'   => $mensagem,
            'ds_data'       => date('Y-m-d'),
            'ds_hora'       => date('H:i:s'),
        ]);
    }
}
