<?php

declare(strict_types=1);

namespace App\Services\Admin\Classificacao;

use App\Enums\Admin\SituacaoProcessosEnum;
use App\Models\Admin\UsuarioModel;
use App\Models\SituacaoProcessosModel;
use App\Models\ProcessosSeletivosModel;
use App\Models\Admin\LogsModel;


/**
 * Servico responsavel pelo disparo controlado de classificacao.
 *
 * - Verifica se o usuario e administrador.
 * - Atualiza a situacao do processo em tb_situacao_processos.
 * - Aciona o reprocessamento da classificacao.
 * - Registra log da acao em tb_logs.
 */
class DisparoClassificacaoService
{
    public function __construct(
        protected ?UsuarioModel $usuarioModel = null,
        protected ?SituacaoProcessosModel $situacaoProcessosModel = null,
        protected ?ProcessosSeletivosModel $processosModel = null,
        protected ?LogsModel $logsModel = null,
        protected ?ClassificacaoService $classificacaoService = null,
    ) {
        $this->usuarioModel            ??= new UsuarioModel();
        $this->situacaoProcessosModel  ??= new SituacaoProcessosModel();
        $this->processosModel          ??= new ProcessosSeletivosModel();
        $this->logsModel               ??= new LogsModel();
        $this->classificacaoService   ??= service('classificacaoService');
    }

    /**
     * Dispara a classificacao para um processo/cargo especifico.
     *
     * @param int    $processo   ID do processo seletivo.
     * @param int    $cargo      ID do cargo.
     * @param string $tipo       Tipo de classificacao: 'preliminar' ou 'homologacao'.
     * @param int    $usuarioId  ID do usuario logado.
     *
     * @throws RuntimeException quando o usuario nao e administrador ou o tipo e invalido.
     */
    public function disparar(int $processo, int $cargo, string $tipo, int $usuarioId): void
    {
        if (!in_array($tipo, ['preliminar', 'homologacao'], true)) {
            throw new \RuntimeException('Tipo de classificacao invalido. Use preliminar ou homologacao.');
        }

        $usuario = $this->usuarioModel->find($usuarioId);
        if (!$usuario || (string) $usuario->ds_administrador !== '1') {
            throw new \RuntimeException('Apenas administradores podem disparar a classificacao.');
        }

        $novaSituacao = $tipo === 'preliminar'
            ? SituacaoProcessosEnum::RESULTADO_PUBLICADO->value
            : SituacaoProcessosEnum::VIGENTE->value;

        $this->atualizarSituacaoProcesso($processo, $novaSituacao);

        $this->classificacaoService->reprocessar($processo, $cargo);

        $this->registrarLog(
            $processo,
            $cargo,
            $tipo,
            $usuarioId,
            (string) ($usuario->ds_nome_user ?? 'ADMINISTRADOR')
        );
    }

    /**
     * Atualiza ou insere a situacao do processo em tb_situacao_processos,
     * mantendo tb_processos.nm_situacao sincronizado.
     */
    private function atualizarSituacaoProcesso(int $processo, int $novaSituacao): void
    {
        $registro = $this->situacaoProcessosModel->porProcesso($processo);

        $dados = [
            'fk_id_processo'      => $processo,
            'nm_id_situacao'      => $novaSituacao,
            'dt_ultima_alteracao' => date('Y-m-d H:i:s'),
        ];

        if ($registro) {
            $this->situacaoProcessosModel->update($registro->pk_id_situacao_processo, $dados);
        } else {
            $this->situacaoProcessosModel->insert($dados);
        }

        $processoRow = $this->processosModel->find($processo);
        if ($processoRow && (int) $processoRow->nm_situacao !== $novaSituacao) {
            $this->processosModel->update($processo, ['nm_situacao' => $novaSituacao]);
        }
    }

    /**
     * Registra log da acao de disparo de classificacao.
     */
    private function registrarLog(
        int $processo,
        int $cargo,
        string $tipo,
        int $usuarioId,
        string $nomeUsuario
    ): void {
        $labelTipo = $tipo === 'preliminar' ? 'PRELIMINAR' : 'HOMOLOGACAO';

        $this->logsModel->insert([
            'id_usuario'  => $usuarioId,
            'ds_usuario'  => $nomeUsuario,
            'ds_acao'     => 'DISPARO DE CLASSIFICACAO',
            'ds_tabela'   => 'tb_situacao_processos',
            'ds_mensagem' => "Classificacao {$labelTipo} disparada para o processo {$processo} e cargo {$cargo}.",
            'ds_data'     => date('Y-m-d'),
            'ds_hora'     => date('H:i:s'),
        ]);
    }
}
