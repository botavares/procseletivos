<?php
namespace App\Enums\Escolaridades;
enum EnumEscolaridades: int{
    case FUNDAMENTAL = 1;
    case ENSINOMEDIOTECNICO = 2;
    case GRADUACAO = 3;
    case LATUSENSO = 4;
    case MESTRADO = 5;
    case DOUTORADO = 6;
}