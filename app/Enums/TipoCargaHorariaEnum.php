<?php

namespace App\Enums;

enum TipoCargaHorariaEnum: int{
    case SEMANAL    = 1;
    case MENSAL     = 2;
    public function label(): string{
        return match ($this) {
            self::SEMANAL => 'Horas semanais',
            self::MENSAL => 'Horas mensais',
        };
    }
}

