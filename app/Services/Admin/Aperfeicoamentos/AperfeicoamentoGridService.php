<?php

namespace App\Services\Admin\Aperfeicoamentos;

use App\Services\Admin\Base\AbstractGridService;
use App\Models\Admin\AperfeicoamentosModel;

class AperfeicoamentoGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new AperfeicoamentosModel());
        $this->setColumns(['Nome do Curso', 'Cargos Elegíveis'])
             ->setOrder('ds_nome_curso', 'asc');
    }

    public function aperfeicoamentos(): array
    {
        $resultados = $this->model
            ->select('tb_cursos_aperfeicoamentos.*, GROUP_CONCAT(tb_cargos.ds_nome_cargo ORDER BY tb_cargos.ds_nome_cargo SEPARATOR ", ") AS ds_nome_cargo')
            ->join('tb_aperfeicoamentos_cargos_elegiveis ele', 'ele.fk_id_curso = tb_cursos_aperfeicoamentos.pk_id_curso', 'left')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = ele.fk_id_cargo', 'left')
            ->groupBy('tb_cursos_aperfeicoamentos.pk_id_curso')
            ->orderBy('tb_cursos_aperfeicoamentos.ds_nome_curso', 'asc')
            ->findAll();

        return [
            'data'    => $resultados,
            'columns' => $this->columns,
        ];
    }
}
