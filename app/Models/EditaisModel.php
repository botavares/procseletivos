<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EditalStatusEnum;
use CodeIgniter\Model;

class EditaisModel extends Model
{
    protected $table = 'tb_editais';
    protected $primaryKey = 'pk_id_edital';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_secretaria',
        'ds_numero_edital',
        'ds_data_inicial',
        'ds_data_termino',
        'ds_status',
    ];

    protected $validationRules = [
        'fk_id_secretaria' => 'required|integer',
        'ds_numero_edital' => 'required|string',
        'ds_data_inicial' => 'required|string',
        'ds_data_termino' => 'required|string',
        'ds_status' => 'required|integer',
    ];

    public function candidatosPorEdital($id)
    {
        $this->select('*');
        $this->join('tb_editais_candidatos', 'tb_editais.pk_id_edital = tb_editais_candidatos.fk_id_edital');
        $this->join('tb_candidatos', 'tb_candidatos.pk_id_candidato = tb_editais_candidatos.fk_id_candidato');
        $this->where('tb_editais.pk_id_edital', $id);
        $this->where('tb_editais.ds_status', 1);
        return $this->findAll();
    }

    public function editaisPorCargo($fk_id_curso)
    {
        return $this->where('ds_status', '1')
                    ->join('tb_editais_cursos', 'tb_editais.pk_id_edital = tb_editais_cursos.fk_id_edital')
                    ->where('tb_editais_cursos.fk_id_curso', $fk_id_curso)
                    ->orderBy('ds_data_inicial', 'DESC')
                    ->first();
    }

    /**
     * Retorna todos os editais (ativos e inativos) ordenados pelo ID descendente.
     */
    public function listarTodosEditais()
    {
        return $this->orderBy('pk_id_edital', 'DESC')->findAll();
    }

    /**
     * Retorna editais em que o ds_status seja INATIVO ou ATIVO (0 ou 1),
     * excluindo AGUARDANDO (2), ordenados pelo ID descendente.
     */
    public function listarEditaisVisiveis()
    {
        return $this
            ->whereIn('ds_status', [
                EditalStatusEnum::INATIVO->value,
                EditalStatusEnum::ATIVO->value,
            ])
            ->orderBy('pk_id_edital', 'DESC')
            ->findAll();
    }
}
