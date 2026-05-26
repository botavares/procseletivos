<?php

namespace App\Models;

use CodeIgniter\Model;

class DadosAditivosModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_dados_aditivos';
    protected $primaryKey       = 'pk_id_aditivo';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['fk_id_contrato','ds_numero_aditivo','ds_ano_aditivo','fk_id_setor','ds_supervisor','ds_carga_horaria','ds_data_aditivo',
                                    'ds_data_prorrogacao','ds_status'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'fk_id_contrato' => 'required',
        'ds_numero_aditivo' => 'required',
        'ds_data_aditivo' => 'required',
        'ds_data_prorrogacao' => 'required',
        
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function listarAditivo($idContrato){
        $this->select('tb_dados_aditivos.*, tb_setores.ds_nome_setor');
        $this->join('tb_setores', 'tb_dados_aditivos.fk_id_setor = tb_setores.pk_id_setor');
        $this->where('tb_dados_aditivos.fk_id_contrato', "$idContrato");
        $this->orderBy('tb_dados_aditivos.ds_numero_aditivo', 'asc');
        return $this->findAll();
    }

    public function getAditivoPorContrato($idContrato){
        $this->where('fk_id_contrato', "$idContrato");
        return $this->first();
    }

    public function proximoNumeroPorAno(int $ano): int{
        $row = $this->selectMax('ds_numero_aditivo')
                    ->where('ds_ano_aditivo', $ano)
                    ->first();

        /*
         * Quando não existir nenhum aditivo no ano,
         * o selectMax retorna NULL
         */
        return ((int) ($row->ds_numero_aditivo ?? 0)) + 1;
    }


}
