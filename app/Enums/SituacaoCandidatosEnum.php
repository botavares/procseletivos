<?php
Namespace App\Enums;

enum SituacaoCandidatosEnum: int{
    case AGUARDANDO = 0;
    case CONVOCADO = 1;
    case CONTRATADO = 2;
    case DESCLASSIFICADO = 3;
    public function label(): string{
        return match ($this) {
            self::AGUARDANDO => 'Aguardando',
            self::CONVOCADO => 'Convocado',
            self::CONTRATADO => 'Contratado',
            self::DESCLASSIFICADO => 'Desclassificado',
        };
    }
}