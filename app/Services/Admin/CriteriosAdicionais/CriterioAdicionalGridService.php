<?php

namespace App\Services\Admin\CriteriosAdicionais;

use App\Services\Admin\Base\AbstractGridService;
use App\Models\Admin\CriteriosAdicionaisModel;

class CriterioAdicionalGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new CriteriosAdicionaisModel());
        $this->setColumns(['Nome do Critério', 'Cargo Vinculado'])
             ->setOrder('ds_nome_criterio', 'asc');
    }

    public function criterios(): array
    {
        $resultados = $this->model
            ->select('tb_criterios_adicionais.*, tb_cargos.ds_nome_cargo')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_criterios_adicionais.fk_id_cargo', 'left')
            ->orderBy('tb_criterios_adicionais.ds_nome_criterio', 'asc')
            ->findAll();

        return [
            'data'    => $resultados,
            'columns' => $this->columns,
        ];
    }
}
