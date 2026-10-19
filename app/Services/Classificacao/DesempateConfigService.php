<?php

declare(strict_types=1);

namespace App\Services\Classificacao;

use App\Models\CargosDesempateConfigModel;
use App\Services\Classificacao\DTO\DesempateConfigDTO;
use App\Services\Interfaces\IDesempateConfigService;

class DesempateConfigService implements IDesempateConfigService
{
    public function __construct(
        private CargosDesempateConfigModel $model,
    ) {}

    /**
     * Busca a configuracao de desempate ordenada para um cargo.
     *
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
     */
    public function cargoPossuiConfiguracao(int $cargo): bool
    {
        return $this->model->where('fk_id_cargo', $cargo)->countAllResults() > 0;
    }
}
