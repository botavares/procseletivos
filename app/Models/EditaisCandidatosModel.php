<?php
namespace App\Models;

use CodeIgniter\Model;

class EditaisCandidatosModel extends Model{
    //Atributos
    protected $table = 'tb_editais_candidatos';
    protected $primaryKey = 'fk_id_edital';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_candidato','fk_id_curso','ds_observacao'
    ];

    protected $returnType = 'object';

    public function editaisPorId($id){
        $this->select('ds_modo,ds_numero_edital,ds_data_inicial,ds_data_final');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = tb_editais_candidatos.fk_id_edital');
        $this->where('tb_editais_candidatos.fk_id_candidato', $id);
        $this->where('tb_editais.ds_status', 1);
        return $this->findAll();
    }
    public function verificarVinculos($idCandidato,$idEdital){
        return $this->where('fk_id_candidato', $idCandidato)
                    ->where('fk_id_edital', $idEdital)
                    ->first();
    }
}