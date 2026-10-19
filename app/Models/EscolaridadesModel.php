<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class EscolaridadesModel extends Model
{
    protected $table = 'tb_escolaridades';
    protected $primaryKey = 'pk_id_escolaridade';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'ds_nome_escolaridade',
        'ds_instrucao',
        'fk_id_nivel',
    ];

    protected $validationRules = [
        'ds_nome_escolaridade' => 'required|string',
        'ds_instrucao' => 'required|string',
        'fk_id_nivel' => 'required|integer',
    ];

    /**
     * Retorna os requisitos de escolaridades vinculados a um edital específico.
     */
    public function listarRequisitosEscolaridades($cargo = null)
    {
        return $this->db->table('tb_cargos_escolaridades')
            ->select([
                'tb_cargos_escolaridades.fk_id_escolaridade',
                'tb_cargos_escolaridades.fk_id_cargo',
                'tb_escolaridades.ds_nome_escolaridade',
                'tb_escolaridades.ds_instrucao',
                'tb_cargos_escolaridades.ds_pontuacao_minima',
                'tb_cargos_escolaridades.ds_pontuacao_maxima',
                'tb_cargos_escolaridades.ds_tipo_campo',
            ])
            ->join('tb_escolaridades', 'tb_cargos_escolaridades.fk_id_escolaridade = tb_escolaridades.pk_id_escolaridade')
            ->join('tb_cargos', 'tb_cargos_escolaridades.fk_id_cargo = tb_cargos.pk_id_cargo')
            ->where('tb_cargos.pk_id_cargo', $cargo)
            ->orderBy('tb_escolaridades.fk_id_nivel', 'asc')
            ->orderBy('tb_escolaridades.pk_id_escolaridade', 'desc')
            ->get()
            ->getResult();
    }
}
