<?php
namespace App\Models;

use CodeIgniter\Model;

class EditaisCargosModel extends Model{
    //Atributos
    protected $table = 'tb_editais_cargos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_cargo',
    ];

    protected $validationRules = [
        'fk_id_edital' => 'required',
        'fk_id_cargo' => 'required',
        
    ];

    protected $returnType = 'object';

    
    
    

}