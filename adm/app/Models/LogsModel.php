<?php
namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model{
    //Atributos
    protected $table = 'tb_logs';
    protected $primaryKey = 'pk_id_log';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'id_usuario','ds_usuario','ds_acao','ds_tabela','ds_mensagem','ds_data','ds_hora'
    ];

    protected $validationRules = [
        'id_usuario' => 'required',
        'ds_usuario' => 'required',
        'ds_acao' => 'required',
        'ds_tabela' => 'required',
        'ds_mensagem' => 'required',
        'ds_data' => 'required',
        'ds_hora' => 'required',
        
    ];

    protected $returnType = 'object';

}