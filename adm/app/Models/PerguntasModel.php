<?php
namespace App\Models;

use CodeIgniter\Model;

class PerguntasModel extends Model{
    //Atributos
    protected $table = 'tb_perguntas_frequentes';
    protected $primaryKey = 'pk_id_pergunta';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
       'ds_pergunta','ds_resposta'
    ];

    protected $validationRules = [
        'ds_pergunta' => 'required',
        'ds_resposta' => 'required',
    ];

    public function getPerguntas(){
        return $this->findAll();
    }
    public function getPergunta($id){
        return $this->where('pk_id_pergunta', $id)->first();
    }

}