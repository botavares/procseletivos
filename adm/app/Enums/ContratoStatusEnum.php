<?php

namespace App\Enums;

enum ContratoStatusEnum: int
{
    case ATIVO      = 1;
    case RESCINDIDO = 2;
    case ADITIVADO  = 3;
}
