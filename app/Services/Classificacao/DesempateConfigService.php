<?php

namespace App\Services\Classificacao;

use App\Models\CargosDesempateConfigModel;
use App\Services\Classificacao\DTO\DesempateConfigDTO;

class DesempateConfigService
{
    private CargosDesempateConfigModel $model;

    public function __construct()
    {
        $this->model = new CargosDesempateConfigModel();
    }

    /**
     * Busca a configuracao de desempate ordenada para um cargo.
     *
     * @param int $cargo
     * @return DesempateConfigDTO[]
     */
    public function buscarConfiguracao(int $cargo): array
    {
        $rows = $this->model->listarPorCargo($cargo);
        $config = [];
        foreach ($rows as $row) {
            $config[] = new DesempateConfigDTO(
                (int) $row->ds_ordem,
                $row->ds_tipo_criterio,
                $row->fk_id_referencia ? (int) $row->fk_id_referencia : null,
                $row->ds_direcao,
                $row->ds_parametro_extra ?? null,
                $row->ds_descricao ?? ''
            );
        }
        return $config;
    }

    /**
     * Verifica se um cargo possui configuracao de desempate customizada.
     *
     * @param int $cargo
     * @return bool
     */
    public function cargoPossuiConfiguracao(int $cargo): bool
    {
        return $this->model->where('fk_id_cargo', $cargo)->countAllResults() > 0;
    }
}
