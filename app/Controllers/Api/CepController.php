<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\Endereco\CepService;

class CepController extends BaseController
{
    public function buscar()
    {
        $cep = $this->request->getGet('cep');

        $service = new CepService();
        $resultado = $service->consultar($cep);
        
        return $this->response->setJSON($resultado);
    }
}
