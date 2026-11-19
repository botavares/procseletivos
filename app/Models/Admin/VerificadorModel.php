<?php

namespace App\Models\Admin;

use App\Models\Common\VerificadorModel as BaseVerificadorModel;

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
