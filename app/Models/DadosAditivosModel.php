<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class DadosAditivosModel extends Model
{
    protected $table = 'tb_dados_aditivos';
    protected $primaryKey = 'pk_id_aditivo';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_contrato',
        'ds_numero_aditivo',
        'ds_ano_aditivo',
        'fk_id_setor',
        'ds_supervisor',
        'ds_carga_horaria',
        'ds_data_aditivo',
        'ds_data_prorrogacao',
        'ds_status',
    ];

    protected $validationRules = [
        'fk_id_contrato' => 'required|integer',
        'ds_numero_aditivo' => 'required|string',
        'ds_data_aditivo' => 'required|string',
        'ds_data_prorrogacao' => 'required|string',
    ];

    public function listarAditivo($idContrato)
    {
        $this->select('tb_dados_aditivos.*, tb_setores.ds_nome_setor');
        $this->join('tb_setores', 'tb_dados_aditivos.fk_id_setor = tb_setores.pk_id_setor');
        $this->where('tb_dados_aditivos.fk_id_contrato', "$idContrato");
        $this->orderBy('tb_dados_aditivos.ds_numero_aditivo', 'asc');
        return $this->findAll();
    }

    public function getAditivoPorContrato($idContrato)
    {
        $this->where('fk_id_contrato', "$idContrato");
        return $this->first();
    }

    public function proximoNumeroPorAno(int $ano): int
    {
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
