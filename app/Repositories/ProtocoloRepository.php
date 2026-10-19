<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IProtocoloRepository;
use App\Models\ProtocolosModel;

class ProtocoloRepository implements IProtocoloRepository
{
    protected ProtocolosModel $model;

    public function __construct(ProtocolosModel $model)
    {
        $this->model = $model;
    }

    public function buscarProtocolosDoCandidato(int $idCandidato): array
    {
        return $this->model
            ->select('tb_cadastrados_protocolo.*, tb_editais.ds_numero_edital, tb_cargos.ds_nome_cargo')
            ->join('tb_editais', 'tb_editais.pk_id_edital = tb_cadastrados_protocolo.fk_id_edital')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_cadastrados_protocolo.fk_id_cargo')
            ->where('tb_cadastrados_protocolo.fk_id_cadastrado', $idCandidato)
            ->findAll();
    }
}
