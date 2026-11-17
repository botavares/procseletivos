<?php

declare(strict_types=1);

namespace App\Services\Admin\Convocados;

use App\Enums\SituacaoCandidatosEnum;
use App\Exceptions\Admin\ConvocacaoInvalidaException;
use App\Models\Admin\ConvocadosModel;
use App\Models\Admin\ProcessosConvocacaoModel;
use App\Models\Admin\SituacaoModel;
use App\Services\Admin\Base\TransactionalTrait;
use CodeIgniter\Database\BaseConnection;

class ConvocadosService
{
    use TransactionalTrait;

    public function __construct(
        private readonly ConvocadosModel $convocadosModel,
        private readonly ProcessosConvocacaoModel $processosConvocacaoModel,
        private readonly SituacaoModel $situacaoModel,
        ?BaseConnection $db = null
    ) {
        $this->db = $db ?? \Config\Database::connect();
    }

    /**
     * Busca a convocação atual (última não deletada) para o processo/cargo.
     */
    public function buscarConvocacaoAtual(int $processoId, int $cargoId): ?object
    {
        return $this->processosConvocacaoModel
            ->where('fk_id_processo', $processoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('is_deletado IS NULL')
            ->orderBy('nm_ano_convocacao', 'DESC')
            ->orderBy('nm_numero_convocacao', 'DESC')
            ->first();
    }

    /**
     * Busca a convocação anterior à convocação informada.
     */
    public function buscarConvocacaoAnterior(int $processoId, int $cargoId, int $convocacaoAtualId): ?object
    {
        $atual = $this->processosConvocacaoModel->find($convocacaoAtualId);
        if (!$atual) {
            return null;
        }

        return $this->processosConvocacaoModel
            ->where('fk_id_processo', $processoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('pk_id_convocacao !=', $convocacaoAtualId)
            ->where('is_deletado IS NULL')
            ->groupStart()
                ->where('nm_ano_convocacao <', $atual->nm_ano_convocacao)
                ->orGroupStart()
                    ->where('nm_ano_convocacao', $atual->nm_ano_convocacao)
                    ->where('nm_numero_convocacao <', $atual->nm_numero_convocacao)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('nm_ano_convocacao', 'DESC')
            ->orderBy('nm_numero_convocacao', 'DESC')
            ->first();
    }

    /**
     * Busca convocados de uma convocação.
     *
     * @return array<int, object>
     */
    public function buscarConvocadosPorConvocacao(int $convocacaoId): array
    {
        $resultado = $this->convocadosModel
            ->where('fk_id_convocacao', $convocacaoId)
            ->findAll();

        return is_array($resultado) ? $resultado : [];
    }

    /**
     * Processa titulares/suplentes da convocação anterior conforme regras de negócio.
     */
    public function processarConvocacaoAnterior(int $processoId, int $cargoId, int $convocacaoAtualId): void
    {
        $anterior = $this->buscarConvocacaoAnterior($processoId, $cargoId, $convocacaoAtualId);
        if (!$anterior) {
            return;
        }

        $convocadosAnterior = $this->buscarConvocadosPorConvocacao((int) $anterior->pk_id_convocacao);
        if (empty($convocadosAnterior)) {
            return;
        }

        $convocadosAtual    = $this->buscarConvocadosPorConvocacao($convocacaoAtualId);
        $idsConvocadosAtual = array_column($convocadosAtual, 'fk_id_candidato');

        foreach ($convocadosAnterior as $convocado) {
            $idCandidato = (int) $convocado->fk_id_candidato;
            $ehTitular   = (int) $convocado->nm_tipo === 1;
            $situacao    = $this->buscarSituacao($processoId, $cargoId, $idCandidato);

            if ($ehTitular) {
                if (!$this->estaContratado($situacao)) {
                    $this->atualizarOuCriarSituacao(
                        $processoId,
                        $cargoId,
                        $idCandidato,
                        SituacaoCandidatosEnum::DESCLASSIFICADO->value
                    );
                }
                continue;
            }

            if (!$this->estaContratado($situacao) && !in_array($idCandidato, $idsConvocadosAtual, true)) {
                $this->atualizarOuCriarSituacao(
                    $processoId,
                    $cargoId,
                    $idCandidato,
                    SituacaoCandidatosEnum::AGUARDANDO->value
                );
            }
        }
    }

    /**
     * Salva os convocados da convocação atual e sincroniza a situação dos candidatos.
     *
     * Regras aplicadas:
     * - Candidatos selecionados (titular ou suplente) recebem situação CONVOCADO.
     * - Candidatos desmarcados na convocação atual voltam para situação AGUARDANDO.
     * - Candidatos já CONTRATADOS não podem ser convocados novamente.
     * - Candidatos que foram TITULARES na convocação anterior não podem ser SUPLENTS na atual.
     * - Não realiza soft-delete: registros de convocações anteriores permanecem
     *   preservados. Apenas sincroniza os convocados desta convocação.
     *
     * @param int[] $titulares IDs dos candidatos titulares
     * @param int[] $suplentes IDs dos candidatos suplentes
     *
     * @throws ConvocacaoInvalidaException
     */
    public function salvarConvocados(int $convocacaoId, array $titulares, array $suplentes, int $processoId, int $cargoId): void
    {
        $this->transactional(function () use ($convocacaoId, $titulares, $suplentes, $processoId, $cargoId) {
            $existentes = $this->buscarConvocadosPorConvocacao($convocacaoId);
            $mapaExistentes = [];
            foreach ($existentes as $row) {
                $mapaExistentes[(int) $row->fk_id_candidato] = (int) $row->pk_id_convocado;
            }

            // Garante prioridade a titular caso o mesmo ID venha em ambos os arrays
            $idsTitulares = [];
            foreach ($titulares as $idCandidato) {
                $id = (int) $idCandidato;
                if ($id > 0) {
                    $idsTitulares[$id] = true;
                }
            }

            $idsSuplentes = [];
            foreach ($suplentes as $idCandidato) {
                $id = (int) $idCandidato;
                if ($id > 0 && !isset($idsTitulares[$id])) {
                    $idsSuplentes[$id] = true;
                }
            }

            $this->validarRegrasDeNegocio(
                $processoId,
                $cargoId,
                $convocacaoId,
                array_keys($idsTitulares),
                array_keys($idsSuplentes)
            );

            $idsSelecionados = array_merge(array_keys($idsTitulares), array_keys($idsSuplentes));
            $idsParaRemover = array_diff(array_keys($mapaExistentes), $idsSelecionados);

            if (!empty($idsParaRemover)) {
                $this->convocadosModel
                    ->where('fk_id_convocacao', $convocacaoId)
                    ->whereIn('fk_id_candidato', $idsParaRemover)
                    ->delete(null, true);

                foreach ($idsParaRemover as $idCandidato) {
                    $this->atualizarOuCriarSituacao($processoId, $cargoId, $idCandidato, SituacaoCandidatosEnum::AGUARDANDO->value);
                }
            }

            foreach ($idsTitulares as $idCandidato => $_) {
                $this->inserirOuAtualizar($convocacaoId, $idCandidato, 1, $mapaExistentes);
                $this->atualizarOuCriarSituacao($processoId, $cargoId, $idCandidato, SituacaoCandidatosEnum::CONVOCADO->value);
            }

            foreach ($idsSuplentes as $idCandidato => $_) {
                $this->inserirOuAtualizar($convocacaoId, $idCandidato, 0, $mapaExistentes);
                $this->atualizarOuCriarSituacao($processoId, $cargoId, $idCandidato, SituacaoCandidatosEnum::CONVOCADO->value);
            }
        });
    }

    /**
     * Registra a contratação de um candidato para um processo/cargo.
     *
     * Atualiza a situação para CONTRATADO e o remove da convocação atual,
     * garantindo que ele não apareça em futuras convocações (a tela já filtra
     * situações bloqueantes).
     */
    public function contratarCandidato(int $processoId, int $cargoId, int $candidatoId): void
    {
        $this->transactional(function () use ($processoId, $cargoId, $candidatoId) {
            $convocacaoAtual = $this->buscarConvocacaoAtual($processoId, $cargoId);

            if ($convocacaoAtual !== null) {
                $this->convocadosModel
                    ->where('fk_id_convocacao', (int) $convocacaoAtual->pk_id_convocacao)
                    ->where('fk_id_candidato', $candidatoId)
                    ->delete(null, true);
            }

            $this->atualizarOuCriarSituacao(
                $processoId,
                $cargoId,
                $candidatoId,
                SituacaoCandidatosEnum::CONTRATADO->value
            );
        });
    }

    /**
     * Valida regras de negócio antes de persistir os convocados.
     *
     * @param int[] $titulares IDs dos candidatos titulares
     * @param int[] $suplentes IDs dos candidatos suplentes
     *
     * @throws ConvocacaoInvalidaException
     */
    private function validarRegrasDeNegocio(
        int $processoId,
        int $cargoId,
        int $convocacaoAtualId,
        array $titulares,
        array $suplentes
    ): void {
        $situacoes = $this->buscarSituacoes($processoId, $cargoId, array_merge($titulares, $suplentes));

        foreach ($titulares as $idCandidato) {
            if ($this->estaBloqueadoParaConvocacao($situacoes[$idCandidato] ?? null)) {
                throw new ConvocacaoInvalidaException(
                    "Candidato {$idCandidato} já contratado ou desclassificado não pode ser convocado."
                );
            }
        }

        foreach ($suplentes as $idCandidato) {
            if ($this->estaBloqueadoParaConvocacao($situacoes[$idCandidato] ?? null)) {
                throw new ConvocacaoInvalidaException(
                    "Candidato {$idCandidato} já contratado ou desclassificado não pode ser convocado."
                );
            }

            if ($this->candidatoFoiTitularEmConvocacaoAnterior($processoId, $cargoId, $convocacaoAtualId, $idCandidato)) {
                throw new ConvocacaoInvalidaException(
                    "Candidato {$idCandidato} foi titular na convocação anterior e não pode ser suplente."
                );
            }
        }
    }

    /**
     * Verifica se o candidato foi titular em qualquer convocação anterior
     * ao processo/cargo informado.
     */
    private function candidatoFoiTitularEmConvocacaoAnterior(
        int $processoId,
        int $cargoId,
        int $convocacaoAtualId,
        int $candidatoId
    ): bool {
        $anterior = $this->buscarConvocacaoAnterior($processoId, $cargoId, $convocacaoAtualId);
        if (!$anterior) {
            return false;
        }

        return $this->convocadosModel
            ->where('fk_id_convocacao', (int) $anterior->pk_id_convocacao)
            ->where('fk_id_candidato', $candidatoId)
            ->where('nm_tipo', 1)
            ->countAllResults() > 0;
    }

    /**
     * Insere um novo convocado ou atualiza o tipo de um registro existente
     * para a mesma convocação/candidato.
     */
    private function inserirOuAtualizar(int $convocacaoId, int $candidatoId, int $tipo, array $mapaExistentes): void
    {
        if (isset($mapaExistentes[$candidatoId])) {
            $this->convocadosModel->update($mapaExistentes[$candidatoId], [
                'nm_tipo' => $tipo,
            ]);
            return;
        }

        $this->convocadosModel->insert([
            'fk_id_convocacao' => $convocacaoId,
            'fk_id_candidato'  => $candidatoId,
            'nm_tipo'          => $tipo,
        ]);
    }

    private function buscarSituacao(int $processoId, int $cargoId, int $candidatoId): ?array
    {
        return $this->situacaoModel
            ->getSituacaoCandidatoCargoProcesso($processoId, $cargoId, $candidatoId);
    }

    /**
     * @param int[] $candidatosIds
     * @return array<int, array>
     */
    private function buscarSituacoes(int $processoId, int $cargoId, array $candidatosIds): array
    {
        if (empty($candidatosIds)) {
            return [];
        }

        $rows = $this->situacaoModel
            ->where('fk_id_processo', $processoId)
            ->where('fk_id_cargo', $cargoId)
            ->whereIn('fk_id_candidato', $candidatosIds)
            ->findAll();

        $mapa = [];
        foreach ($rows as $row) {
            $mapa[(int) $row['fk_id_candidato']] = $row;
        }

        return $mapa;
    }

    private function estaContratado(?array $situacao): bool
    {
        if ($situacao === null) {
            return false;
        }

        return (int) ($situacao['nm_id_situacao'] ?? 0) === SituacaoCandidatosEnum::CONTRATADO->value;
    }

    private function estaDesclassificado(?array $situacao): bool
    {
        if ($situacao === null) {
            return false;
        }

        return (int) ($situacao['nm_id_situacao'] ?? 0) === SituacaoCandidatosEnum::DESCLASSIFICADO->value;
    }

    private function estaBloqueadoParaConvocacao(?array $situacao): bool
    {
        return $this->estaContratado($situacao) || $this->estaDesclassificado($situacao);
    }

    private function atualizarOuCriarSituacao(int $processoId, int $cargoId, int $candidatoId, int $situacao): void
    {
        $existente = $this->buscarSituacao($processoId, $cargoId, $candidatoId);

        $data = [
            'fk_id_candidato' => $candidatoId,
            'fk_id_cargo'     => $cargoId,
            'fk_id_processo'  => $processoId,
            'nm_id_situacao'  => $situacao,
        ];

        if ($existente !== null) {
            $id = (int) ($existente['pk_id_situacao'] ?? $existente['id']);
            $this->situacaoModel->atualizar($id, $data);
            return;
        }

        $this->situacaoModel->salvar($data);
    }
}
