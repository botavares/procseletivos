<?php

declare(strict_types=1);

namespace App\DTOs;

class GovBrUsuarioDTO
{
    public function __construct(
        public readonly bool $sucesso,
        public readonly ?object $dados,
        public readonly ?string $erro,
    ) {}

    public static function sucesso(object $dados): self
    {
        return new self(true, $dados, null);
    }

    public static function erro(string $mensagem): self
    {
        return new self(false, null, $mensagem);
    }
}
