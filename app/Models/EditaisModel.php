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
    public function getEditaisAtivos()
    {
        $this->select('tb_editais.*, tb_cargos.ds_nome_cargo, tb_secretarias.ds_nome_secretaria, tb_situacao_processos.nm_id_situacao AS nm_situacao_processo');
        $this->join('tb_editais_cargos', 'tb_editais.pk_id_edital = tb_editais_cargos.fk_id_edital');
        $this->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_editais.fk_id_secretaria');
        $this->join('tb_processos', "tb_editais.ds_numero_edital = (tb_processos.nm_numero_processo * 10000 + tb_processos.nm_ano_processo) AND (tb_processos.is_deletado IS NULL OR tb_processos.is_deletado = '0000-00-00 00:00:00')", 'left');
        $this->join('tb_situacao_processos', 'tb_situacao_processos.fk_id_processo = tb_processos.pk_id_processo', 'left');
        $this->where('tb_editais.ds_status', EditalStatusEnum::ATIVO->value);
        $this->orderBy('tb_editais.ds_data_inicial', 'DESC');
        return $this->findAll();
    }

    public function getEditaisVigentes()
    {
        $this->select('tb_editais.*, tb_cargos.ds_nome_cargo, tb_secretarias.ds_nome_secretaria, tb_situacao_processos.nm_id_situacao AS nm_situacao_processo');
        $this->join('tb_editais_cargos', 'tb_editais.pk_id_edital = tb_editais_cargos.fk_id_edital');
        $this->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_editais.fk_id_secretaria');
        $this->join('tb_processos', "tb_editais.ds_numero_edital = (tb_processos.nm_numero_processo * 10000 + tb_processos.nm_ano_processo) AND (tb_processos.is_deletado IS NULL OR tb_processos.is_deletado = '0000-00-00 00:00:00')", 'left');
        $this->join('tb_situacao_processos', 'tb_situacao_processos.fk_id_processo = tb_processos.pk_id_processo', 'left');
        $this->where('tb_editais.ds_status', EditalStatusEnum::VIGENTE->value);
        $this->orderBy('tb_editais.ds_data_inicial', 'DESC');
        return $this->findAll();
    }








    public function candidatosPorEdital($id)
    {
        $this->select('*');
        $this->join('tb_editais_candidatos', 'tb_editais.pk_id_edital = tb_editais_candidatos.fk_id_edital');
        $this->join('tb_candidatos', 'tb_candidatos.pk_id_candidato = tb_editais_candidatos.fk_id_candidato');
        $this->where('tb_editais.pk_id_edital', $id);
        $this->where('tb_editais.ds_status', EditalStatusEnum::ATIVO->value);
        return $this->findAll();
    }

    public function editaisPorCargo($fk_id_curso)
    {
        return $this->where('ds_status', EditalStatusEnum::ATIVO->value)
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
