<?php
namespace App\Models;

use CodeIgniter\Model;

class EditaisCargosModel extends Model{
    //Atributos
    protected $table = 'tb_editais_cargos';
    //colocar chave compost primary key:
    
    protected $primaryKey = ['fk_id_edital', 'fk_id_cargo'];
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        
    ];

    protected $returnType = 'object';

    public function getEditaisAtivosIdCargo($fk_id_cargo){
        return $this->where('fk_id_cargo', $fk_id_cargo)
            ->join('tb_editais', 'tb_editais.pk_id_edital = tb_editais_cargos.fk_id_edital')
            ->where('tb_editais.ds_status', '1')
            ->orderBy('tb_editais.ds_data_inicial', 'DESC')
            ->first();
    }

    public function getEditaisAtivosCargos()
{
    return $this->select('tb_editais_cargos.*, tb_editais.*, tb_cargos.*')
        ->join('tb_editais', 'tb_editais.pk_id_edital = tb_editais_cargos.fk_id_edital')
        ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo')

        ->where('tb_editais.ds_status', '1')

        ->groupStart()
            ->where("EXISTS (
                SELECT 1 FROM tb_cargos_experiencias_editais e
                WHERE e.fk_id_cargo = tb_editais_cargos.fk_id_cargo
            )", null, false)

            ->orWhere("EXISTS (
                SELECT 1 FROM tb_cargos_aperfeicoamentos_editais a
                WHERE a.fk_id_cargo = tb_editais_cargos.fk_id_cargo
            )", null, false)

            ->orWhere("EXISTS (
                SELECT 1 FROM tb_cargos_escolaridades_editais s
                WHERE s.fk_id_cargo = tb_editais_cargos.fk_id_cargo
            )", null, false)
        ->groupEnd()

        ->orderBy('tb_editais.ds_data_inicial', 'DESC')
        ->findAll();
}

}