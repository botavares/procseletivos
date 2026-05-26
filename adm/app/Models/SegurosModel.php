<?php
namespace App\Models;

use CodeIgniter\Model;

class SegurosModel extends Model{
    //Atributos
    protected $table = 'tb_dados_seguro';
    protected $primaryKey = 'pk_id_seguro';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_seguradora','ds_numero_seguro','ds_apolice','ds_cnpj','ds_status'
    ];

    protected $validationRules = [
        'ds_seguradora' => 'required',
        'ds_numero_seguro' => 'required',
        'ds_apolice' => 'required',
        'ds_cnpj' => 'required',
        'ds_status' => 'required'
    ];

    protected $returnType = 'object';
    
    public function SeguroPorId($id){
        return $this->where('pk_id_seguro', $id)->first();
    }
    public function updateStatusAnteriores(){
        $data = [
            'ds_status' => 0
        ];
        // Atualiza todos os registros com status 1 para 0
        $this->where('ds_status', 1)->set($data)->update();
    }

}