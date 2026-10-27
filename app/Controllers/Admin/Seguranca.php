<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\Admin\SecretariasModel;
use App\Models\Admin\SetoresModel;

class Seguranca extends BaseController{
    public function getCsrf(){
    return $this->response->setJSON([
        csrf_token() => csrf_hash()
    ]);
}
}