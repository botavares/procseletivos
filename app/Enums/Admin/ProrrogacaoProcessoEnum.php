<?php

declare(strict_types=1);

namespace App\Enums\Admin;

enum ProrrogacaoProcessoEnum: int
{
    case NAO = 0;
    case SIM = 1;

    public function label(): string
    {
        return match ($this) {
            self::NAO => 'Não',
            self::SIM => 'Sim',
        };
    }
}
