<?php

namespace App\Enums;

enum EditalStatusEnum: int{
    case INATIVO    = 0;
    case ATIVO      = 1;
    case PUBLICADO = 2;
    case ENCERRADO = 3;
}
