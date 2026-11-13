<?php

declare(strict_types=1);

namespace App\Services\ProcessosSeletivos;

use App\DTOs\Admin\ProcessosSeletivosDTO;
use App\Enums\Admin\SituacaoProcessosEnum;
use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Models\Admin\SecretariasModel;
use App\Models\CargosAperfeicoamentosModel;
use App\Models\CargosCriteriosAdicionaisModel;
use App\Models\CargosDesempateConfigModel;
use App\Models\CargosEscolaridadesModel;
use App\Models\CargosExperienciasModel;
use App\Models\CargosModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;
use DateTime;

class ProcessosSeletivosService implements IProcessosSeletivos
{
    public function __construct(
        protected ProcessosSeletivosModel $model,
        protected LogsService $logsService,
        protected ?ProcessosDocumentosModel $documentosModel = null,
        protected ?SecretariasModel $secretariasModel = null,
        protected ?CargosModel $cargosModel = null,
        protected ?CargosEscolaridadesModel $cargosEscolaridadesModel = null,
        protected ?CargosExperienciasModel $cargosExperienciasModel = null,
        protected ?CargosAperfeicoamentosModel $cargosAperfeicoamentosModel = null,
        protected ?CargosCriteriosAdicionaisModel $cargosCriteriosAdicionaisModel = null,
        protected ?CargosDesempateConfigModel $cargosDesempateConfigModel = null,
    ) {
        helper('edital');
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

    public function listarTodosPorSecretaria(int $idSecretaria): array
    {
        return $this->model->listarTodos($idSecretaria);
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
        return $this->listarAtivosComSituacaoPorSecretaria(0);
    }

    /**
     * Lista processos seletivos com inscrições abertas que estão aptos a
     * aparecer na home pública.
     *
     * Regras de exibição:
     * - Situação igual a INSCRICOES_ABERTAS;
     * - Possuir edital de abertura cadastrado;
     * - Possuir ao menos um cargo vinculado;
     * - Todos os cargos vinculados devem possuir ao menos uma tabela de dados classificatórios;
     * - Todos os cargos vinculados devem possuir critérios de desempate configurados.
     */
    public function listarAbertosParaHome(): array
    {
        $processos = $this->listarAtivosComSituacao();
        $abertos   = [];

        foreach ($processos as $processo) {
            if ($this->processoEstaAptoParaHome($processo)) {
                $abertos[] = $processo;
            }
        }

        return $abertos;
    }

    /**
     * Retorna os alertas de aptidão de um processo para exibição no admin.
     *
     * @param object $processo Objeto retornado pelo model de processos.
     * @return string[]
     */
    public function obterAlertasAptidaoProcesso(object $processo): array
    {
        $alertas = [];

        $idProcesso    = (int) ($processo->pk_id_processo ?? 0);
        $idSecretaria  = (int) ($processo->fk_id_secretaria ?? 0);

        if ($idProcesso <= 0) {
            return [];
        }

        if (!$this->possuiEditalAbertura($idProcesso)) {
            $alertas[] = 'Não foi vinculado nenhum edital de abertura';
        }

        $cargos = $this->model->cargosPorProcesso($idProcesso);

        if (empty($cargos)) {
            $alertas[] = 'Não foi vinculado cargo(s) ao processo seletivo';
            return $alertas;
        }

        if (!$this->possuiCargoComDadosClassificatorios($idProcesso, $idSecretaria, $cargos)) {
            $alertas[] = 'Cargo vinculado ao processo não tem dados classificatórios';
        }

        if (!$this->possuiCargoComDesempate($idProcesso, $idSecretaria, $cargos)) {
            $alertas[] = 'Cargo vinculado ao processo sem critérios de desempate';
        }

        return $alertas;
    }

    /**
     * Verifica se um processo está apto para exibição na home pública.
     */
    private function processoEstaAptoParaHome(object $processo): bool
    {
        $situacaoId = (int) ($processo->nm_id_situacao ?? 0);

        if ($situacaoId !== SituacaoProcessosEnum::INSCRICOES_ABERTAS->value) {
            return false;
        }

        return empty($this->obterAlertasAptidaoProcesso($processo));
    }

    /**
     * Verifica se o processo possui edital de abertura cadastrado.
     */
    private function possuiEditalAbertura(int $idProcesso): bool
    {
        if ($this->documentosModel === null) {
            return false;
        }

        return $this->documentosModel->existeEditalAbertura($idProcesso);
    }

    /**
     * Verifica se todos os cargos vinculados possuem ao menos uma tabela
     * de dados classificatórios preenchida.
     *
     * @param int $idProcesso
     * @param int $idSecretaria
     * @param array $cargos
     * @return bool
     */
    private function possuiCargoComDadosClassificatorios(int $idProcesso, int $idSecretaria, array $cargos): bool
    {
        if ($this->cargosEscolaridadesModel === null
            || $this->cargosExperienciasModel === null
            || $this->cargosAperfeicoamentosModel === null
            || $this->cargosCriteriosAdicionaisModel === null
        ) {
            return false;
        }

        foreach ($cargos as $cargo) {
            $idCargo = (int) (is_array($cargo)
                ? ($cargo['pk_id_cargo'] ?? $cargo['fk_id_cargo'] ?? 0)
                : ($cargo->pk_id_cargo ?? $cargo->fk_id_cargo ?? 0));

            if ($idCargo <= 0) {
                continue;
            }

            if (!$this->cargoPossuiDadosClassificatorios($idProcesso, $idCargo, $idSecretaria)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica se um cargo específico possui ao menos uma tabela de dados
     * classificatórios preenchida.
     */
    private function cargoPossuiDadosClassificatorios(int $idProcesso, int $idCargo, int $idSecretaria): bool
    {
        $escolaridade = $this->cargosEscolaridadesModel->existePorProcessoCargo($idProcesso, $idCargo, $idSecretaria);
        $experiencia  = $this->cargosExperienciasModel->existePorProcessoCargo($idProcesso, $idCargo, $idSecretaria);
        $aperfeicoamento = $this->cargosAperfeicoamentosModel->existePorProcessoCargo($idProcesso, $idCargo, $idSecretaria);
        $criterios    = $this->cargosCriteriosAdicionaisModel->existePorProcessoCargo($idProcesso, $idCargo, $idSecretaria);

        return $escolaridade || $experiencia || $aperfeicoamento || $criterios;
    }

    /**
     * Verifica se todos os cargos vinculados possuem critérios de desempate configurados.
     *
     * @param int $idProcesso
     * @param int $idSecretaria
     * @param array $cargos
     * @return bool
     */
    private function possuiCargoComDesempate(int $idProcesso, int $idSecretaria, array $cargos): bool
    {
        if ($this->cargosDesempateConfigModel === null) {
            return false;
        }

        foreach ($cargos as $cargo) {
            $idCargo = (int) (is_array($cargo)
                ? ($cargo['pk_id_cargo'] ?? $cargo['fk_id_cargo'] ?? 0)
                : ($cargo->pk_id_cargo ?? $cargo->fk_id_cargo ?? 0));

            if ($idCargo <= 0) {
                continue;
            }

            if (!$this->cargosDesempateConfigModel->existePorProcessoCargo($idProcesso, $idCargo, $idSecretaria)) {
                return false;
            }
        }

        return true;
    }

    public function listarAtivosComSituacaoPorSecretaria(int $idSecretaria): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_processos');

        $builder->select(
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
            ->whereNotIn('tb_situacao_processos.nm_id_situacao', [4, 5]); // exclui VIGENTE e ENCERRADO

        if ($idSecretaria > 0) {
            $builder->where('tb_processos.fk_id_secretaria', $idSecretaria);
        }

        return $builder
            ->groupBy('tb_processos.pk_id_processo')
            ->orderBy('tb_processos.pk_id_processo', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Lista processos seletivos visiveis para transparencia publica,
     * considerando situacao de resultado publicado ou vigente.
     */
    public function listarProcessosTransparencia(?int $idSecretaria = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_processos');

        $builder->select(
                'tb_processos.*,' .
                ' tb_situacao_processos.nm_id_situacao,' .
                ' tb_situacao_processos.dt_ultima_alteracao,' .
                ' tb_secretarias.ds_nome_secretaria'
            )
            ->join('tb_situacao_processos', 'tb_situacao_processos.fk_id_processo = tb_processos.pk_id_processo', 'left')
            ->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_processos.fk_id_secretaria', 'left')
            ->where("(tb_processos.is_deletado IS NULL OR tb_processos.is_deletado = '0000-00-00 00:00:00')")
            ->whereIn('tb_situacao_processos.nm_id_situacao', [3, 4]); // RESULTADO PUBLICADO ou VIGENTE

        if ($idSecretaria !== null && $idSecretaria > 0) {
            $builder->where('tb_processos.fk_id_secretaria', $idSecretaria);
        }

        return $builder
            ->groupBy('tb_processos.pk_id_processo')
            ->orderBy('tb_processos.pk_id_processo', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Busca cargos vinculados a um processo seletivo.
     */
    public function buscarCargosPorProcesso(int $processoId, ?int $idSecretaria = null): array
    {
        return $this->model->cargosPorProcesso($processoId, $idSecretaria);
    }

    public function calcularValidade(int $idProcesso): ?string
    {
        $processo = $this->model->find($idProcesso);
        if (!$processo) {
            return null;
        }

        // Se não houver data de homologação, não é possível calcular a validade
        if (empty($processo->dt_data_homologacao)) {
            return '—';
        }

        $multiplicador = 1;
        // nm_prorrogado = 1 com prorrogacao e nm_prorrogado = 0 sem prorrogacao
        ($processo->nm_prorrogado == 1) ? $multiplicador = 2 : $multiplicador = 1;
        $mesesValidade = $processo->nm_meses_validade * $multiplicador;
        $dataHomologacao = new DateTime($processo->dt_data_homologacao);
        $dataValidade = $dataHomologacao->modify("+$mesesValidade months");
        return $dataValidade->format('d/m/Y');
    }

    /**
     * Retorna a lista de processos seletivos formatada para o portal público,
     * aplicando filtros de situação, secretaria e cargo.
     *
     * @param array<string, string> $filtros
     * @return array<int, array<string, mixed>>
     */
    public function listarEditais(array $filtros = []): array
    {
        $situacaoFiltro = $filtros['situacao'] ?? 'Todas';
        $secretariaFiltro = isset($filtros['secretaria']) && $filtros['secretaria'] !== ''
            ? (int) $filtros['secretaria']
            : null;
        $cargoFiltro = $filtros['cargo'] ?? 'Todos';

        $processos = $this->model->listarComSituacaoESecretaria();
        $editais = [];

        foreach ($processos as $processo) {
            $situacaoId = (int) ($processo->nm_id_situacao ?? 0);
            $secretariaIdProcesso = (int) ($processo->fk_id_secretaria ?? 0);

            // Filtro por situação (Vigentes / Encerrados / Todas)
            if (!$this->situacaoPermitida($situacaoId, $situacaoFiltro)) {
                continue;
            }

            // Filtro por secretaria
            if ($secretariaFiltro !== null && $secretariaIdProcesso !== $secretariaFiltro) {
                continue;
            }

            $idProcesso = (int) $processo->pk_id_processo;
            $secretaria = $this->formatarSecretaria($processo);
            $cargos = $this->buscarCargosPorProcesso($idProcesso);

            // Filtro por cargo
            if ($cargoFiltro !== 'Todos' && !$this->processoPossuiCargo($cargos, $cargoFiltro)) {
                continue;
            }

            $documentos = $this->buscarDocumentosPorProcesso($idProcesso);

            $editais[] = $this->montarEdital(
                $processo,
                $secretaria,
                $cargos,
                $documentos
            );
        }

        return $editais;
    }

    /**
     * Retorna as secretarias disponíveis para o filtro da página pública.
     *
     * @return array<int, object>
     */
    public function obterSecretariasFiltro(): array
    {
        return $this->secretariasModel
            ->orderBy('ds_nome_secretaria', 'ASC')
            ->findAll();
    }

    /**
     * Retorna os cargos disponíveis para o filtro da página pública.
     *
     * @return array<int, object>
     */
    public function obterCargosFiltro(): array
    {
        return $this->cargosModel
            ->orderBy('ds_nome_cargo', 'ASC')
            ->findAll();
    }

    /**
     * Busca os documentos de um processo e os separa em edital de abertura e retificações.
     */
    private function buscarDocumentosPorProcesso(int $idProcesso): array
    {
        $documentos = $this->documentosModel->porProcessoId($idProcesso);

        $editais = [];
        $retificacoes = [];

        foreach ($documentos as $doc) {
            $tipoId = (int) $doc->nm_tipo;
            $formatado = $this->formatarDocumento($doc);

            if ($tipoId === TiposDocumentosEnum::EDITAL_ABERTURA->value) {
                $editais[] = $formatado;
            } elseif ($tipoId === TiposDocumentosEnum::RETIFICACAO->value) {
                $retificacoes[] = $formatado;
            }
        }

        return [
            'editais'      => $editais,
            'retificacoes' => $retificacoes,
        ];
    }

    /**
     * Formata um documento para exibição na view.
     */
    private function formatarDocumento(object $doc): array
    {
        $tipoEnum = TiposDocumentosEnum::tryFrom((int) $doc->nm_tipo);

        return [
            'id'               => (int) $doc->pk_id_documento,
            'tipo'             => $tipoEnum?->label() ?? 'Documento',
            'tipo_id'          => (int) $doc->nm_tipo,
            'numero'           => (int) $doc->nm_numero_documento,
            'ano'              => (int) $doc->nm_ano_documento,
            'numero_formatado' => formatarNumeroEdital(($doc->nm_numero_documento ?? '') . ($doc->nm_ano_documento ?? '')),
            'codigo'           => $doc->ds_codigo_oficial,
            'arquivo'          => $doc->ds_arquivo,
            'link'             => $doc->ds_link,
            'vigente'          => (bool) $doc->nm_versao_vigente,
            'data_publicacao'  => isset($doc->dt_data_publicacao) ? date('d/m/Y', strtotime($doc->dt_data_publicacao)) : null,
        ];
    }

    /**
     * Monta o array final de um edital/processos seletivo para a view.
     */
    private function montarEdital(
        object $processo,
        object $secretaria,
        array $cargos,
        array $documentos
    ): array {
        $situacaoId = (int) ($processo->nm_id_situacao ?? 0);
        $enum = SituacaoProcessosEnum::tryFrom($situacaoId);

        $inscricaoAberta = in_array($situacaoId, [
            SituacaoProcessosEnum::INSCRICOES_ABERTAS->value,
            SituacaoProcessosEnum::AGENDADO->value,
        ], true);

        $dataInicio = isset($processo->dt_inicio_inscricao)
            ? date('d/m/Y', strtotime($processo->dt_inicio_inscricao))
            : '—';
        $dataFim = isset($processo->dt_fim_inscricao)
            ? date('d/m/Y', strtotime($processo->dt_fim_inscricao))
            : '—';

        return [
            'id'                => (int) $processo->pk_id_processo,
            'numero_formatado'  => formatarNumeroEdital(($processo->nm_numero_processo ?? '') . ($processo->nm_ano_processo ?? '')),
            'numero'            => (int) ($processo->nm_numero_processo ?? 0),
            'ano'               => (int) ($processo->nm_ano_processo ?? 0),
            'objeto'            => $processo->ds_objeto ?? 'Objetivo não informado.',
            'secretaria'        => $secretaria,
            'situacao_id'       => $situacaoId,
            'situacao_label'    => $enum?->label() ?? 'Status não encontrado',
            'tag_class'         => $this->resolverTagClass($situacaoId),
            'inscricao_aberta'  => $inscricaoAberta,
            'data_inicio'       => $dataInicio,
            'data_fim'          => $dataFim,
            'periodo_inscricao' => $dataInicio . ' a ' . $dataFim,
            'data_homologacao'  => isset($processo->dt_data_homologacao)
                ? date('d/m/Y', strtotime($processo->dt_data_homologacao))
                : null,
            'cargos'            => $cargos,
            'total_vagas'       => array_sum(array_column($cargos, 'nm_vagas')),
            'editais'           => $documentos['editais'],
            'retificacoes'      => $documentos['retificacoes'],
        ];
    }

    private function formatarSecretaria(object $processo): object
    {
        return (object) [
            'id'     => (int) ($processo->fk_id_secretaria ?? 0),
            'nome'   => $processo->ds_nome_secretaria ?? 'Secretaria não informada',
            'sigla'  => $processo->ds_sigla_secretaria ?? '',
        ];
    }

    /**
     * Resolve a classe CSS da tag de situação conforme o padrão home-novo.css.
     */
    private function resolverTagClass(int $situacaoId): string
    {
        return match ($situacaoId) {
            SituacaoProcessosEnum::INSCRICOES_ABERTAS->value,
            SituacaoProcessosEnum::AGENDADO->value => 'home-tag--aberto',
            SituacaoProcessosEnum::VIGENTE->value => 'home-tag--vigente',
            SituacaoProcessosEnum::ENCERRADO->value => 'home-tag--encerrado',
            default => 'home-tag--encerrado',
        };
    }

    /**
     * Verifica se o processo deve ser exibido de acordo com o filtro de situação.
     */
    private function situacaoPermitida(int $situacaoId, string $filtro): bool
    {
        return match ($filtro) {
            'Vigentes' => $situacaoId === SituacaoProcessosEnum::VIGENTE->value,
            // Para o portal público, "Encerrados" engloba tanto processos com
            // inscrições encerradas quanto processos formalmente encerrados.
            'Encerrados' => in_array($situacaoId, [
                SituacaoProcessosEnum::INSCRICOES_ENCERRADAS->value,
                SituacaoProcessosEnum::ENCERRADO->value,
            ], true),
            default => true,
        };
    }

    /**
     * Verifica se o processo possui um cargo com o nome selecionado no filtro.
     */
    private function processoPossuiCargo(array $cargos, string $cargoFiltro): bool
    {
        foreach ($cargos as $cargo) {
            $nomeCargo = $cargo->ds_nome_cargo ?? '';
            if ($nomeCargo === $cargoFiltro) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna os cargos dos processos seletivos ativos para exibição na área logada.
     *
     * @return array<int, object>
     */
    public function buscarCargosAtivos(): array
    {
        return $this->model->cargosPorProcessosAtivos();
    }

    /**
     * Verifica se o processo seletivo está dentro do período de inscrição.
     *
     * @throws \App\Exceptions\Domain\EditalForaDoPrazoException
     */
    public function estaAtivoPorProcesso(int $idProcesso): bool
    {
        $dadosProcesso = $this->model->find($idProcesso);

        if (!$dadosProcesso) {
            throw new \App\Exceptions\Domain\EditalForaDoPrazoException('Processo seletivo não encontrado.');
        }

        $dataInicial = date('d/m/Y', strtotime($dadosProcesso->dt_inicio_inscricao));
        $dataFinal   = date('d/m/Y', strtotime($dadosProcesso->dt_fim_inscricao));

        $hoje = date('Y-m-d');
        if ($hoje < $dadosProcesso->dt_inicio_inscricao || $hoje > $dadosProcesso->dt_fim_inscricao) {
            throw new \App\Exceptions\Domain\EditalForaDoPrazoException(
                "O período de cadastro para este processo seletivo é de {$dataInicial} a {$dataFinal}."
            );
        }

        return true;
    }

    /**
     * Retorna o ID do edital legado associado ao processo seletivo, se existir.
     */
    public function buscarEditalIdPorProcesso(int $idProcesso): ?int
    {
        $processo = $this->model->find($idProcesso);

        if (!$processo) {
            return null;
        }

        $numeroCompleto = ((int) $processo->nm_numero_processo * 10000) + (int) $processo->nm_ano_processo;

        $edital = \Config\Database::connect()
            ->table('tb_editais')
            ->where('ds_numero_edital', $numeroCompleto)
            ->get()
            ->getRow();

        return $edital ? (int) $edital->pk_id_edital : null;
    }
}
