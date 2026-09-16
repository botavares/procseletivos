<?php

namespace App\Models;

use CodeIgniter\Model;

class RecursosHistoricoModel extends Model
{
    protected $table = 'tb_cadastrados_recursos';
    protected $primaryKey = 'pk_id_historico';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_candidato',
        'ds_campo_alterado',
        'fk_id_campo_alterado',
        'ds_tipo',
        'ds_valor_antigo',
        'ds_valor_novo',
        'ds_observacao',
        'ds_numero_protocolo',
        'ds_usuario_responsavel',
        'ds_data_alteracao',
        'ds_hora_alteracao',
    ];

    protected $returnType = 'object';

    /**
     * Lista o histórico de recursos com filtros opcionais
     */
    public function listarHistorico(array $filtros = [])
    {
        $builder = $this->db->table('tb_cadastrados_recursos hr')
            ->select([
                'hr.*',
                'c.ds_nome as ds_nome_candidato',
                'e.ds_numero_edital',
                'cg.ds_nome_cargo',
                'COALESCE(ex.ds_nome_experiencia, esc.ds_nome_escolaridade, ap.ds_nome_curso, cr.ds_nome_criterio) as ds_nome_campo',
            ])
            ->join('tb_cadastrados c', 'c.pk_id_cadastrado = hr.fk_id_candidato', 'left')
            ->join('tb_editais e', 'e.pk_id_edital = hr.fk_id_edital', 'left')
            ->join('tb_cargos cg', 'cg.pk_id_cargo = hr.fk_id_cargo', 'left')
            ->join('tb_experiencias ex', 'ex.pk_id_experiencia = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'experiencias\'', 'left')
            ->join('tb_escolaridades esc', 'esc.pk_id_escolaridade = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'escolaridades\'', 'left')
            ->join('tb_cursos_aperfeicoamentos ap', 'ap.pk_id_curso = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'aperfeicoamentos\'', 'left')
            ->join('tb_criterios_adicionais cr', 'cr.pk_id_criterio = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'criterios\'', 'left');

        if (!empty($filtros['protocolo'])) {
            $builder->where('hr.ds_numero_protocolo', $filtros['protocolo']);
        }

        if (!empty($filtros['candidato'])) {
            $builder->groupStart()
                ->like('c.ds_nome', $filtros['candidato'])
                ->orLike('c.ds_cpf', $filtros['candidato'])
                ->groupEnd();
        }

        if (!empty($filtros['data_inicio'])) {
            $builder->where('hr.ds_data_alteracao >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $builder->where('hr.ds_data_alteracao <=', $filtros['data_fim']);
        }

        if (!empty($filtros['edital'])) {
            $builder->where('hr.fk_id_edital', (int)$filtros['edital']);
        }

        if (!empty($filtros['cargo'])) {
            $builder->where('hr.fk_id_cargo', (int)$filtros['cargo']);
        }

        return $builder->orderBy('hr.ds_data_alteracao', 'DESC')
            ->orderBy('hr.ds_hora_alteracao', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Obtém detalhes de um recurso específico
     */
    public function obterRecurso(int $id)
    {
        return $this->db->table('tb_cadastrados_recursos hr')
            ->select([
                'hr.*',
                'c.ds_nome as ds_nome_candidato',
                'c.ds_cpf',
                'e.ds_numero_edital',
                'cg.ds_nome_cargo',
                'COALESCE(ex.ds_nome_experiencia, esc.ds_nome_escolaridade, ap.ds_nome_curso, cr.ds_nome_criterio) as ds_nome_campo',
            ])
            ->join('tb_cadastrados c', 'c.pk_id_cadastrado = hr.fk_id_candidato', 'left')
            ->join('tb_editais e', 'e.pk_id_edital = hr.fk_id_edital', 'left')
            ->join('tb_cargos cg', 'cg.pk_id_cargo = hr.fk_id_cargo', 'left')
            ->join('tb_experiencias ex', 'ex.pk_id_experiencia = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'experiencias\'', 'left')
            ->join('tb_escolaridades esc', 'esc.pk_id_escolaridade = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'escolaridades\'', 'left')
            ->join('tb_cursos_aperfeicoamentos ap', 'ap.pk_id_curso = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'aperfeicoamentos\'', 'left')
            ->join('tb_criterios_adicionais cr', 'cr.pk_id_criterio = hr.fk_id_campo_alterado AND hr.ds_campo_alterado = \'criterios\'', 'left')
            ->where('hr.pk_id_historico', $id)
            ->get()
            ->getRow();
    }

    /**
     * Agrupa recursos por protocolo
     */
    public function listarPorProtocolo(string $protocolo)
    {
        return $this->listarHistorico(['protocolo' => $protocolo]);
    }
}
