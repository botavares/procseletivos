<?php
namespace App\Models;

use CodeIgniter\Model;

class RecursosModel extends Model{
    protected $table = 'tb_historico_candidatos';
    protected $primaryKey = 'pk_id_historico';
    protected $returnType = 'object';
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
        'ds_hora_alteracao'
        ];
         protected $validationRules = [
            'fk_id_candidato' => 'required',
            'ds_campo_alterado' => 'required',
            'fk_id_campo_alterado' => 'required',
            'ds_tipo' => 'required',
            'ds_valor_antigo' => 'required',
            'ds_valor_novo' => 'required',
            'ds_numero_protocolo' => 'required',
            'ds_usuario_responsavel' => 'required',
            'ds_data_alteracao' => 'required',
            'ds_hora_alteracao' => 'required'
        ];
}