<?php
namespace App\Models;

use CodeIgniter\Model;

class AcademicosModel extends Model{
    //Atributos
    protected $table = 'tb_dados_academicos';
    protected $primaryKey = 'pk_id_candidato';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $allowedFields = [
       'pk_id_candidato','fk_id_curso','ds_ensino_medio','ds_categoria','ds_periodo','ds_faculdade','ds_manha','ds_tarde','ds_noite',
       'ds_data_cadastro','ds_hora_cadastro','ds_data_alteracao','ds_hora_alteracao','ds_ip_cadastro'
    ];	
    

    protected $validationRules = [
        
        'fk_id_curso'       => 'required',
        //'ds_ensino_medio'   => 'required',
        'ds_categoria'      => 'required',
        'ds_periodo'        => 'required',
        'ds_faculdade'      => 'required',
        
    ];

   

   
}