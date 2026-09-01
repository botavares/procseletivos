<?php

namespace App\Enums;

enum EditalStatusDescricaoEnum: string
{
    case INATIVO    = 'Inativo';
    case ATIVO      = 'Ativo';
    case AGUARDANDO = 'Aguardando';
}
