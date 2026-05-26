<?php
namespace App\Models;

use CodeIgniter\Model;

class EditaisModel extends Model{
    //Atributos
    protected $table = 'tb_editais';
    protected $primaryKey = 'pk_id_edital';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_secretaria',
        'ds_numero_edital',
        'ds_data_inicial',
        'ds_data_termino',
        'ds_status'
    ];

    protected $returnType = 'object';

    public function candidatosPorEdital($id) {
        $this->select('*');
        $this->join('tb_editais_candidatos', 'tb_editais.pk_id_edital = tb_editais_candidatos.fk_id_edital');
        $this->join('tb_candidatos', 'tb_candidatos.pk_id_candidato = tb_editais_candidatos.fk_id_candidato');
        $this->where('tb_editais.pk_id_edital', $id);
        $this->where('tb_editais.ds_status', 1);
        return $this->findAll();
    }
    public function editaisPorCargo($fk_id_curso){
        return $this->where('ds_status', '1')
                    ->join('tb_editais_cursos', 'tb_editais.pk_id_edital = tb_editais_cursos.fk_id_edital')
                    ->where('tb_editais_cursos.fk_id_curso', $fk_id_curso)
                    ->orderBy('ds_data_inicial', 'DESC')
                    ->first();
    }

}