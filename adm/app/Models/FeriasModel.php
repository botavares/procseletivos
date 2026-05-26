<?php
namespace App\Models;

use CodeIgniter\Model;

class FeriasModel extends Model{
    //Atributos
    protected $table = 'tb_ferias';
    protected $primaryKey = 'pk_id_ferias';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_ano_referente','fk_id_contrato','fk_id_estagiario','ds_dias_ferias','ds_data_inicio','ds_data_final','ds_status'
    ];

    protected $validationRules = [
        'ds_ano_referente' => 'required',
        'fk_id_contrato' => 'required',
        'fk_id_estagiario' => 'required',
        'ds_dias_ferias' => 'required',
        'ds_data_inicio' => 'required',
        'ds_data_final' => 'required'
        
    ];

    protected $returnType = 'object';

    public function getFerias(){
        $this->select('tb_ferias.*,tb_dados_pessoais.*');
        $this->join('tb_dados_pessoais', 'tb_dados_pessoais.pk_id_candidato = tb_ferias.fk_id_estagiario');
        $this->orderBy('tb_dados_pessoais.ds_nome', 'asc','tb_ferias.ds_data_inicio', 'asc');
        return $this->findAll();
    }
    public function getFeriasId($id){
        return $this->where('pk_id_ferias', $id)->first();
    }

    public function getFeriasContrato($idContrato){
        return $this->where('fk_id_contrato', $idContrato)->findAll();
    }
    public function getFeriasEstagiario($idEstagiario){
        return $this->where('fk_id_estagiario', $idEstagiario)->findAll();
    }


    

}