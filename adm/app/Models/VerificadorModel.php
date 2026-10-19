<?php

namespace App\Models;

use App\Common\Models\VerificadorModel as BaseVerificadorModel;

class VerificadorModel extends BaseVerificadorModel
{
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'pk_id_contrato',
        'ds_verificador',
    ];

    protected $validationRules = [
        'ds_verificador' => 'required',
    ];
}
