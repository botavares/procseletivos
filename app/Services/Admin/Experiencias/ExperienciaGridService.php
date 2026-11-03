<?php

namespace App\Services\Admin\Experiencias;

use App\Services\Admin\Base\AbstractGridService;
use App\Models\Admin\ExperienciasModel;

class ExperienciaGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new ExperienciasModel());
        $this->setColumns(['Nome da Experiência', 'Tipo de Experiência', 'Cargo Vinculado'])
             ->setOrder('ds_nome_experiencia', 'asc');
    }

    public function experiencias(): array
    {
        $resultados = $this->model
            ->select('tb_experiencias.*, tb_cargos.ds_nome_cargo')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_experiencias.fk_id_cargo', 'left')
            ->orderBy('tb_experiencias.ds_nome_experiencia', 'asc')
            ->findAll();

        return [
            'data'    => $resultados,
            'columns' => $this->columns,
        ];
    }
}
