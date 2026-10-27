<?php

declare(strict_types=1);

namespace App\Enums\Admin;

enum SituacaoProcessosEnum: int
{
    case INSCRICOES_ABERTAS    = 1; // Processo seletivo em que a o periodo de inscrições está aberto, mas a dt_divulgacao_resultado é null.
    case INSCRICOES_ENCERRADAS = 2; // Processo seletivo em que a o periodo de inscrições já se encerrou, mas a dt_divulgacao_resultado é null.
    case RESULTADO_PUBLICADO   = 3; // Processo seletivo em que a dt_divulgacao_resultado é diferente de null e a dt_data_homologacao é null.
    case VIGENTE               = 4; // Processo seletivo em que a data atual é <= que a data de homologação + nm_meses_validade. Caso prorrogado nm_meses_validade = nm_meses_validade * 2
    case ENCERRADO             = 5; // Processo seletivo em que a data atual é > que a data de homologação + nm_meses_validade. Caso prorrogado nm_meses_validade = nm_meses_validade * 2
    case AGENDADO              = 6; // Processo seletivo cadastrado, mas a data de inicio das inscrições ainda não chegou

    public function label(): string
    {
        return match ($this) {
            self::INSCRICOES_ABERTAS    => 'Inscrições Abertas',
            self::INSCRICOES_ENCERRADAS => 'Inscrições Encerradas',
            self::RESULTADO_PUBLICADO   => 'Resultado Publicado',
            self::VIGENTE               => 'Vigente',
            self::ENCERRADO             => 'Encerrado',
            self::AGENDADO              => 'Agendado',
        };
    }
}
