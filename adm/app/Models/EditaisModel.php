<?php
namespace App\Models;

use CodeIgniter\Model;

class EditaisModel extends Model{
    //Atributos
    protected $table = 'tb_editais';
    protected $primaryKey = 'pk_id_edital';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_numero_edital','ds_data_inicial','ds_data_termino','ds_status','ds_arquivo_edital'
    ];

    protected $validationRules = [
        'ds_numero_edital'=>'required',
        'ds_data_inicial'=>'required',
        'ds_data_termino'=>'required',
        'ds_status'=>'required',
        
    ];
    //mensagens de validação
    protected $validationMessages = [
        'ds_numero_edital' => [
            'required' => 'O campo Número do Edital é obrigatório.'
        ],
        'ds_data_inicial' => [
            'required' => 'O campo Data Inicial é obrigatório.'
        ],
        'ds_data_termino' => [
            'required' => 'O campo Data Término é obrigatório.'
        ],
        'ds_status' => [
            'required' => 'O campo Status é obrigatório.'
        ]
    ];

    protected $returnType = 'object';

    public function getEditais(){
        return $this->findAll();
    }
    public function getEdital($id){
        return $this->find($id);
    }

   public function getCargosByEdital(int $idEdital): array
{
    return $this->db->table('tb_editais_cargos')
        ->select('tb_cargos.*')
        ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo')
        ->where('tb_editais_cargos.fk_id_edital', $idEdital)
        ->get()
        ->getResult() ?? [];
}

    

}