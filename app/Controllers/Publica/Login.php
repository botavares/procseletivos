<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;
use App\Services\Base\SessaoService;

class Login extends BaseController
{
    protected SessaoService $sessaoService;

    public function __construct()
    {
        $this->sessaoService = service('sessao');
    }

    public function index()
    {
        //
    }

    public function acesso()
    {
        //
    }

    public function destroy()
    {
        $this->sessaoService->destruir();
        return redirect()->to('Home');
    }
}
