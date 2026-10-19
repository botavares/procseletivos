<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IConvocadoRepository;
use App\Models\ConvocadosModel;

class ConvocadoRepository implements IConvocadoRepository
{
    protected ConvocadosModel $model;

    public function __construct(ConvocadosModel $model)
    {
        $this->model = $model;
    }

    public function buscarConvocadosPorEditalCurso(int $idEdital, int $idCurso): array
    {
        $builder = $this->model->db->table('tb_dados_academicos a')
            ->select([
                'a.*',
                'p.*',
                'e.*',
                'c.*'
            ])
            ->join('tb_dados_pessoais p', 'p.pk_id_candidato = a.pk_id_candidato', 'inner')
            ->join(
                '(SELECT fk_id_candidato, fk_id_edital FROM tb_editais_candidatos GROUP BY fk_id_candidato, fk_id_edital) e',
                'e.fk_id_candidato = a.pk_id_candidato',
                'inner',
                false
            )
            ->join(
                'tb_dados_convocacao c',
                'c.fk_id_candidato = a.pk_id_candidato AND c.fk_id_edital = e.fk_id_edital AND c.ds_status = 1',
                'inner'
            )
            ->where('a.fk_id_curso', $idCurso)
            ->where('e.fk_id_edital', $idEdital)
            ->orderBy('a.ds_periodo', 'DESC')
            ->orderBy('a.ds_ensino_medio', 'ASC')
            ->orderBy('p.ds_nascimento', 'ASC');

        return $builder->get()->getResult();
    }

    public function buscarTodosConvocados(): array
    {
        $builder = $this->model->db->table('tb_dados_academicos a')
            ->select(['a.*', 'p.*', 'e.*', 'c.*', 'u.*'])
            ->join('tb_dados_pessoais p', 'p.pk_id_candidato = a.pk_id_candidato')
            ->join('tb_editais_candidatos e', 'e.fk_id_candidato = a.pk_id_candidato')
            ->join('tb_dados_convocacao c', 'c.fk_id_candidato = a.pk_id_candidato')
            ->join('tb_cursos u', 'u.pk_id_curso = a.fk_id_curso')
            ->where('c.ds_status', '1')
            ->orderBy('c.ds_data', 'desc')
            ->orderBy('a.ds_periodo', 'desc')
            ->orderBy('a.ds_ensino_medio', 'asc')
            ->orderBy('p.ds_nascimento', 'asc');

        return $builder->get()->getResult();
    }
}
