<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\SecretariasModel;
use App\Models\SetoresModel;

class Seguranca extends BaseController{
    public function getCsrf(){
    return $this->response->setJSON([
        csrf_token() => csrf_hash()
    ]);
}
}