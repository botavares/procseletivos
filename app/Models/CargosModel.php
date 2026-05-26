<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosModel extends Model{
    //Atributos
    protected $table = 'tb_cargos';
    protected $primaryKey = 'pk_id_cargo';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_secretaria',
        'ds_nome_cargo',
        'ds_carga_horaria',
        'ds_nivel'
    ];

    protected $returnType = 'object';

    public function cargosEditaisAtivos()
    {
        return $this->select('tb_cargos.*')
            ->join('tb_editais_cargos', 'tb_editais_cargos.fk_id_cargo = tb_cargos.pk_id_cargo')
            ->join('tb_editais', 'tb_editais.pk_id_edital = tb_editais_cargos.fk_id_edital')
            ->where('tb_editais.ds_status', '1')
            ->groupBy('tb_cargos.pk_id_cargo')
            ->orderBy('tb_cargos.ds_nome_cargo', 'ASC')
            ->findAll();
    }

}