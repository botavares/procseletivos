<?php
namespace App\Models;

use CodeIgniter\Model;

class DadosRescisaoModel extends Model{
    //Atributos
    protected $table = 'tb_dados_rescisao';
    protected $primaryKey = 'pk_id_rescisao';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_contrato',
        'fk_id_candidato',
        'ds_data_baixa',
        'fk_id_motivo'
    ];

    protected $returnType = 'object';

    public function getRescisaoPorContrato($idContrato){
        return $this->where('fk_id_contrato', $idContrato)->first();
    }

}