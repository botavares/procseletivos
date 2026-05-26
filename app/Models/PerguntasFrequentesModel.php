<?php
namespace App\Models;

use CodeIgniter\Model;

class PerguntasFrequentesModel extends Model{
    //Atributos
    protected $table = 'tb_perguntas_frequentes';
    protected $primaryKey = 'pk_id_pergunta';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_pergunta','ds_resposta'
    ];

    protected $validationRules = [
        'ds_pergunta' => 'required',
        'ds_resposta' => 'required',
     
    ];

    protected $returnType = 'object';
 

}