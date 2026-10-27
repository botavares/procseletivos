<?php

namespace App\Enums\Admin;

enum EditalStatusDescricaoEnum: string
{
    case INATIVO    = 'Inativo';
    case ATIVO      = 'Ativo';
    case AGUARDANDO = 'Aguardando';
}
