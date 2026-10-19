<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface ICandidatoRepository
{
    /**
     * Busca candidato por CPF com dados acadêmicos (JOIN).
     * @return object|null
     */
    public function buscarPorCpf(string $cpf): ?object;

    /**
     * Busca candidato por ID.
     * @return object|null
     */
    public function buscarPorId(int $id): ?object;

    /**
     * Conta candidatos cadastrados no período (ano).
     * Substitui YEAR() por intervalo de datas para não quebrar índices.
     */
    public function contarCadastradosNoAno(int $ano): int;
}
