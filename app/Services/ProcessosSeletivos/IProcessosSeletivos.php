<?php

declare(strict_types=1);

namespace App\Services\ProcessosSeletivos;

use App\DTOs\Admin\ProcessosSeletivosDTO;

interface IProcessosSeletivos
{
    /**
     * Cria um novo processo seletivo.
     */
    public function criar(ProcessosSeletivosDTO $dto): int;

    /**
     * Atualiza um processo seletivo existente.
     */
    public function atualizar(int $id, ProcessosSeletivosDTO $dto): bool;

    /**
     * Realiza soft delete de um processo seletivo.
     */
    public function deletar(int $id): bool;

    /**
     * Busca um processo seletivo pelo ID.
     */
    public function buscarPorId(int $id): ?object;

    /**
     * Lista todos os processos seletivos não deletados.
     */
    public function listarTodos(): array;

    public function listarTodosPorSecretaria(int $idSecretaria): array;

    /**
     * Lista processos seletivos por secretaria.
     */
    public function listarPorSecretaria(int $idSecretaria): array;

    /**
     * Lista processos seletivos por ano.
     */
    public function listarPorAno(int $ano): array;

    /**
     * Busca processo seletivo por número e ano.
     */
    public function buscarPorNumeroEAno(int $numero, int $ano): ?object;

    /**
     * Lista processos seletivos por situação.
     */
    public function listarPorSituacao(int $situacao): array;

    public function listarAtivosComSituacaoPorSecretaria(int $idSecretaria): array;

    /**
     * Lista processos seletivos por faixa de tempo de inscrição.
     */
    public function listarPorFaixaDeTempo(string $dataInicio, string $dataFim): array;

    /**
     * Lista processos seletivos com situação, excluindo vigentes e encerrados.
     * Útil para exibição na home pública.
     */
    public function listarAtivosComSituacao(): array;

    /**
     * Lista processos seletivos com inscrições abertas aptos a aparecer na home
     * pública, garantindo que possuam edital de abertura, cargos vinculados,
     * dados classificatórios e critérios de desempate configurados.
     */
    public function listarAbertosParaHome(): array;

    /**
     * Retorna os alertas de aptidão de um processo para exibição no admin.
     *
     * @param object $processo
     * @return string[]
     */
    public function obterAlertasAptidaoProcesso(object $processo): array;

    /**
     * Busca cargos vinculados a um processo seletivo.
     */
    public function buscarCargosPorProcesso(int $processoId, ?int $idSecretaria = null): array;

    /**
     * Lista processos seletivos visiveis para transparencia publica,
     * considerando situacao de resultado publicado ou vigente.
     */
    public function listarProcessosTransparencia(?int $idSecretaria = null): array;

    /**
     * Retorna a lista de processos seletivos formatada para o portal público,
     * aplicando filtros de situação, secretaria e cargo.
     *
     * @param array<string, string> $filtros
     * @return array<int, array<string, mixed>>
     */
    public function listarEditais(array $filtros = []): array;

    /**
     * Retorna as secretarias disponíveis para o filtro da página pública.
     *
     * @return array<int, object>
     */
    public function obterSecretariasFiltro(): array;

    /**
     * Retorna os cargos disponíveis para o filtro da página pública.
     *
     * @return array<int, object>
     */
    public function obterCargosFiltro(): array;

    /**
     * Retorna os cargos dos processos seletivos ativos para exibição na área logada.
     *
     * @return array<int, object>
     */
    public function buscarCargosAtivos(): array;

    /**
     * Verifica se o processo seletivo está dentro do período de inscrição.
     *
     * @throws \App\Exceptions\Domain\EditalForaDoPrazoException
     */
    public function estaAtivoPorProcesso(int $idProcesso): bool;

    /**
     * Retorna o ID do edital legado associado ao processo seletivo, se existir.
     */
    public function buscarEditalIdPorProcesso(int $idProcesso): ?int;
}
