<?php

namespace App\Enums\Admin;

enum ContratoStatusEnum: int
{
    case ATIVO      = 1;
    case RESCINDIDO = 2;
    case ADITIVADO  = 3;
}
