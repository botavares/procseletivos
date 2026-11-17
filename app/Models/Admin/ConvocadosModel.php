<?php

declare(strict_types=1);

namespace App\Models\Admin;

use CodeIgniter\Model;

class ConvocadosModel extends Model
{
    protected $table            = 'tb_convocados';
    protected $primaryKey       = 'pk_id_convocado';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useTimestamps    = false;
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'is_deletado';

    protected $allowedFields = [
        'fk_id_convocacao', //chave primaria da convocação
        'fk_id_candidato', //chave estrangeira referente ao candidato
        'nm_tipo', //tipo do convocado, 0 = suplente, 1 = titular
    ];

    protected $validationRules = [
        'fk_id_convocacao'          => 'required|integer',
        'fk_id_candidato'           => 'required|integer',
        'nm_tipo'                   => 'required|integer|in_list[0,1]',
    ];

    protected $validationMessages = [
        'fk_id_convocacao' => [
            'required' => 'O campo fk_id_convocacao é obrigatório.',
            'integer'  => 'O campo fk_id_convocacao deve ser um número inteiro.',
        ],
        'fk_id_candidato' => [
            'required' => 'O campo fk_id_candidato é obrigatório.',
            'integer'  => 'O campo fk_id_candidato deve ser um número inteiro.',
        ],
        'nm_tipo' => [
            'required' => 'O campo nm_tipo é obrigatório.',
            'integer'  => 'O campo nm_tipo deve ser um número inteiro.',
            'in_list'  => 'O campo nm_tipo deve ser 0 ou 1.',
        ],
    ];
}