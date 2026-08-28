<?php

namespace App\Contracts;

/**
 * Interface ISessaoService
 * Contrato para serviço de gerenciamento de sessão
 * @package App\Contracts
 */
interface ISessaoService
{
    /**
     * Verifica se usuário está logado
     *
     * @return bool
     */
    public function estaLogado(): bool;

    /**
     * Obtém dados do usuário logado
     *
     * @return array|null
     */
    public function obterUsuario(): ?array;

    /**
     * Obtém CPF do usuário logado
     *
     * @return string|null
     */
    public function obterCpf(): ?string;

    /**
     * Obtém ID do usuário logado
     *
     * @return int|null
     */
    public function obterIdUsuario(): ?int;

    /**
     * Define dados do usuário na sessão
     *
     * @param array $dados
     * @return void
     */
    public function definirUsuario(array $dados): void;

    /**
     * Define protocolo autenticado (para visitantes)
     *
     * @param string $protocolo
     * @return void
     */
    public function definirProtocoloAutenticado(string $protocolo): void;

    /**
     * Obtém protocolo autenticado
     *
     * @return string|null
     */
    public function obterProtocoloAutenticado(): ?string;

    /**
     * Verifica se há protocolo autenticado (visitante)
     *
     * @return bool
     */
    public function temProtocoloAutenticado(): bool;

    /**
     * Limpa protocolo autenticado
     *
     * @return void
     */
    public function limparProtocoloAutenticado(): void;

    /**
     * Destroi a sessão
     *
     * @return void
     */
    public function destruir(): void;

    /**
     * Obtém IP do usuário
     *
     * @return string
     */
    public function obterIp(): string;

    /**
     * Registra atividade
     *
     * @param string $acao
     * @return void
     */
    public function registrarAtividade(string $acao): void;
}
