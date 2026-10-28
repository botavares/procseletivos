<?php

declare(strict_types=1);

namespace App\Enums\Admin;

enum TiposDocumentosEnum: int
{
    case EDITAL_ABERTURA    = 1;
    case RETIFICACAO        = 2;
    case RESULTADO_PARCIAL  = 3;
    case HOMOLOGACAO        = 4;
    case EDITAL_CONVOCACAO  = 5;
    case COMUNICADO         = 6;
    case REPUBLICACAO       = 7;

    public function label(): string
    {
        return match ($this) {
            self::EDITAL_ABERTURA   => 'Edital de Abertura',
            self::RETIFICACAO       => 'Retificação',
            self::RESULTADO_PARCIAL => 'Resultado Parcial',
            self::HOMOLOGACAO       => 'Homologação',
            self::EDITAL_CONVOCACAO => 'Edital de Convocação',
            self::COMUNICADO        => 'Comunicado',
            self::REPUBLICACAO      => 'Republicação Consolidada',
        };
    }
}
