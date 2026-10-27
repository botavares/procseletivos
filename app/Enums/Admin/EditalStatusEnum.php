<?php

namespace App\Enums\Admin;

enum EditalStatusEnum: int
{
    case INATIVO    = 0;
    case ATIVO      = 1;
    case AGUARDANDO = 2;
    case VIGENTE    = 3;
    case PUBLICADO  = 4;
    case ENCERRADO  = 5;
}
