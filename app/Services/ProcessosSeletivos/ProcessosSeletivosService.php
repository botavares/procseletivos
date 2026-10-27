<?php

declare(strict_types=1);

namespace App\Services\ProcessosSeletivos;

use App\DTOs\Admin\ProcessosSeletivosDTO;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;

class ProcessosSeletivosService implements IProcessosSeletivos
{
    public function __construct(
        protected ProcessosSeletivosModel $model,
        protected LogsService $logsService,
    ) {
    }

    public function criar(ProcessosSeletivosDTO $dto): int
    {
        $id = $this->model->insert($dto->toArray());

        if ($id) {
            $this->logsService->inserirLog(
                'Criar',
                'Processo Seletivo criado: ' . $dto->numeroProcesso . '/' . $dto->anoProcesso,
                'tb_processos'
            );
        }

        return (int) $id;
    }

    public function atualizar(int $id, ProcessosSeletivosDTO $dto): bool
    {
        $anterior = $this->model->find($id);
        if (!$anterior) {
            return false;
        }

        $atual = $dto->toArray();
        $resultado = $this->model->update($id, $atual);

        if ($resultado) {
            $camposAlterados = [];
            foreach ($atual as $campo => $valor) {
                if (!property_exists($anterior, $campo)) {
                    continue;
                }
                $valorAnterior = $anterior->{$campo};
                $valorNovo     = $valor;

                $a = $valorAnterior === null ? null : (string) $valorAnterior;
                $b = $valorNovo === null ? null : (string) $valorNovo;

                if ($a !== $b) {
                    $camposAlterados[] = $campo;
                }
            }

            $this->logsService->inserirLog(
                'Alterar',
                'Processo Seletivo ID ' . $id . ' alterado. Campos: ' . implode(', ', $camposAlterados),
                'tb_processos'
            );
        }

        return (bool) $resultado;
    }

    public function deletar(int $id): bool
    {
        $resultado = $this->model->delete($id);

        if ($resultado) {
            $this->logsService->inserirLog(
                'Deletar',
                'Processo Seletivo ID ' . $id . ' soft deleted',
                'tb_processos'
            );
        }

        return (bool) $resultado;
    }

    public function buscarPorId(int $id): ?object
    {
        return $this->model->find($id) ?: null;
    }

    public function listarTodos(): array
    {
        return $this->model->listarTodos();
    }

    public function listarPorSecretaria(int $idSecretaria): array
    {
        return $this->model->porSecretaria($idSecretaria);
    }

    public function listarPorAno(int $ano): array
    {
        return $this->model->porAno($ano);
    }

    public function buscarPorNumeroEAno(int $numero, int $ano): ?object
    {
        return $this->model->porNumeroEAno($numero, $ano);
    }

    public function listarPorSituacao(int $situacao): array
    {
        return $this->model->porSituacao($situacao);
    }

    public function listarPorFaixaDeTempo(string $dataInicio, string $dataFim): array
    {
        return $this->model->porFaixaDeTempo($dataInicio, $dataFim);
    }

    /**
     * Lista processos seletivos com situação, excluindo vigentes e encerrados.
     * Útil para exibição na home pública.
     */
    public function listarAtivosComSituacao(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_processos');

        return $builder
            ->select(
                'tb_processos.*,' .
                ' tb_situacao_processos.nm_id_situacao,' .
                ' tb_situacao_processos.dt_ultima_alteracao,' .
                ' tb_secretarias.ds_nome_secretaria,' .
                ' GROUP_CONCAT(DISTINCT tb_cargos.ds_nome_cargo ORDER BY tb_cargos.ds_nome_cargo SEPARATOR ", ") AS ds_cargos'
            )
            ->join('tb_situacao_processos', 'tb_situacao_processos.fk_id_processo = tb_processos.pk_id_processo', 'left')
            ->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_processos.fk_id_secretaria', 'left')
            ->join('tb_processos_cargos', 'tb_processos_cargos.fk_id_processo = tb_processos.pk_id_processo', 'left')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_cargos.fk_id_cargo', 'left')
            ->where("(tb_processos.is_deletado IS NULL OR tb_processos.is_deletado = '0000-00-00 00:00:00')")
            ->whereNotIn('tb_situacao_processos.nm_id_situacao', [4, 5]) // exclui VIGENTE e ENCERRADO
            ->groupBy('tb_processos.pk_id_processo')
            ->orderBy('tb_processos.pk_id_processo', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Busca cargos vinculados a um processo seletivo.
     */
    public function buscarCargosPorProcesso(int $processoId): array
    {
        return $this->model->cargosPorProcesso($processoId);
    }
}
