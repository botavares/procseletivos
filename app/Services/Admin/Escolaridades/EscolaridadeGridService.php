<?php

namespace App\Services\Admin\Escolaridades;

use App\Services\Admin\Base\AbstractGridService;
use App\Models\Admin\EscolaridadesModel;

class EscolaridadeGridService extends AbstractGridService
{
    public function __construct()
    {
        parent::__construct(new EscolaridadesModel());
        $this->setColumns(['Nome da Escolaridade', 'Instrução', 'Nível', 'Cargos Elegíveis'])
             ->setOrder('ds_nome_escolaridade', 'asc');
    }

    public function escolaridades(): array
    {
        $resultados = $this->model
            ->select('tb_escolaridades.*, GROUP_CONCAT(tb_cargos.ds_nome_cargo ORDER BY tb_cargos.ds_nome_cargo SEPARATOR ", ") AS ds_nome_cargo')
            ->join('tb_escolaridades_cargos_elegiveis ele', 'ele.fk_id_escolaridade = tb_escolaridades.pk_id_escolaridade', 'left')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = ele.fk_id_cargo', 'left')
            ->groupBy('tb_escolaridades.pk_id_escolaridade')
            ->orderBy('tb_escolaridades.ds_nome_escolaridade', 'asc')
            ->findAll();

        return [
            'data'    => $resultados,
            'columns' => $this->columns,
        ];
    }
}
