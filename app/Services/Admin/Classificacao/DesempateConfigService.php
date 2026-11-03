<?php

namespace App\Services\Admin\Classificacao;

use App\Models\Admin\CargosDesempateConfigModel;
use App\Services\Admin\Classificacao\DTO\DesempateConfigDTO;

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
     * @param int|null $processo
     * @return DesempateConfigDTO[]
     */
    public function buscarConfiguracao(int $cargo, ?int $processo = null): array
    {
        $rows = $this->model->listarPorCargo($cargo, $processo);
        $config = [];
        foreach ($rows as $row) {
            $config[] = new DesempateConfigDTO(
                (int) $row->pk_id_desempate,
                (int) $row->ds_ordem,
                $row->ds_tipo_criterio,
                $row->fk_id_referencia ? (int) $row->fk_id_referencia : null,
                $row->ds_direcao,
                $row->ds_parametro_extra ?? null,
                $row->ds_descricao ?? '',
                $row->fk_id_processo_seletivo ? (int) $row->fk_id_processo_seletivo : null
            );
        }
        return $config;
    }

    /**
     * Verifica se um cargo possui configuracao de desempate customizada.
     *
     * @param int $cargo
     * @param int|null $processo
     * @return bool
     */
    public function cargoPossuiConfiguracao(int $cargo, ?int $processo = null): bool
    {
        $builder = $this->model->where('fk_id_cargo', $cargo);

        if ($processo !== null) {
            $builder->where('fk_id_processo_seletivo', $processo);
        }

        return $builder->countAllResults() > 0;
    }
}
