<?php
namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Teste extends BaseController
{
    public function index(){
        dd($_SERVER['HTTPS'] ?? null, request()->isSecure());

    }
}