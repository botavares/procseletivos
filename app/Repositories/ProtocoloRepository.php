<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IProtocoloRepository;
use App\Models\Admin\ProtocolosModel;

class ProtocoloRepository implements IProtocoloRepository
{
    protected ProtocolosModel $model;

    public function __construct(ProtocolosModel $model)
    {
        $this->model = $model;
    }

    public function buscarProtocolosDoCandidato(int $idCandidato): array
    {
        try {
            $resultado = $this->model
                ->select('tb_cadastrados_protocolo.*, tb_editais.ds_numero_edital, tb_cargos.ds_nome_cargo, tb_cargos.fk_id_secretaria, tb_processos.pk_id_processo AS fk_id_processo')
                ->join('tb_editais', 'tb_editais.pk_id_edital = tb_cadastrados_protocolo.fk_id_edital')
                ->join(
                    'tb_processos',
                    "tb_editais.ds_numero_edital = (tb_processos.nm_numero_processo * 10000 + tb_processos.nm_ano_processo) AND (tb_processos.is_deletado IS NULL OR tb_processos.is_deletado = '0000-00-00 00:00:00')",
                    'left'
                )
                ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_cadastrados_protocolo.fk_id_cargo')
                ->where('tb_cadastrados_protocolo.fk_id_cadastrado', $idCandidato)
                ->findAll();

            return is_array($resultado) ? $resultado : [];
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar protocolos do candidato ' . $idCandidato . ': ' . $e->getMessage());
            return [];
        }
    }
}
