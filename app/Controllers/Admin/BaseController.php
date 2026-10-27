<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController as Base;

/**
 * BaseController específico para o painel administrativo.
 * Redireciona o caminho de views para app/Views/Admin/.
 */
abstract class BaseController extends Base
{
    /**
     * Caminho relativo das views dentro de APPPATH/Views/
     */
    protected $viewPath = 'Admin';
}