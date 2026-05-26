<?php
namespace App\Models;

use CodeIgniter\Model;

class SetoresModel extends Model{
    //Atributos
    protected $table = 'tb_setores';
    protected $primaryKey = 'pk_id_setor';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_nome_setor','ds_status','fk_id_secretaria','ds_telefone'
    ];

    protected $validationRules = [
        'ds_nome_setor' => 'required',
        'ds_status' => 'required',
        'fk_id_secretaria' => 'required',
        'ds_telefone' => 'required'
        
    ];

    protected $returnType = 'object';

    public function getSetores(){
        return $this->findAll();
    }
    public function getSetor($id){
        return $this->where('pk_id_setor', $id)->first();
    }
    public function setoresSecretarias(){
        $this->select('tb_setores.*, tb_secretarias.ds_nome_secretaria');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this->orderBy('tb_setores.ds_nome_setor', 'asc');
        return $this->findAll();

    }
    public function setorSecretaria($idSetor){
        $this->select('tb_setores.*, tb_secretarias.ds_nome_secretaria');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this->where('tb_setores.pk_id_setor', $idSetor);
        $this->orderBy('tb_setores.ds_nome_setor', 'asc');
        return $this->first();
    }

    public function setoresPorEdital($idEdital){
        
    }
    

}