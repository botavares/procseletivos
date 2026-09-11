<?php

namespace App\Models;

use CodeIgniter\Model;

class CadastrosRecursosModel extends Model
{
    protected $table = 'tb_cadastrados_recursos';
    protected $primaryKey = 'pk_id_historico';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_candidato',
        'ds_campo_alterado',
        'fk_id_campo_alterado',
        'ds_tipo',
        'ds_valor_antigo',
        'ds_valor_novo',
        'ds_numero_protocolo',
        'ds_usuario_responsavel',
        'ds_data_alteracao',
        'ds_hora_alteracao',
    ];

    protected $returnType = 'object';
}
