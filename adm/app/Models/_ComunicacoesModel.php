<?php

namespace App\Models;

use CodeIgniter\Model;

class ComunicacoesModel extends Model
{
    protected $table = 'tb_comunicacoes';
    protected $primaryKey = 'pk_id_comunicacao';
    protected $allowedFields = [
        'ds_assunto',
        'ds_destinatario',
        'ds_email',
        'ds_data',
        'ds_hora',
    ];

    protected $returnType = 'object';
}
