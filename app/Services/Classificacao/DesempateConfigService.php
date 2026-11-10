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
     * @param int|null $processo
     * @param int|null $secretaria
     * @return DesempateConfigDTO[]
     */
    public function buscarConfiguracao(int $cargo, ?int $processo = null, ?int $secretaria = null): array
    {
        $rows = $this->model->listarPorCargo($cargo, $processo, $secretaria);
        $config = [];

        foreach ($rows as $row) {
            $config[] = new DesempateConfigDTO(
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
     * @param int|null $processo
     */
    public function cargoPossuiConfiguracao(int $cargo, ?int $processo = null, ?int $secretaria = null): bool
    {
        $builder = $this->model->where('fk_id_cargo', $cargo);

        if ($secretaria !== null) {
            $builder->where('fk_id_secretaria', $secretaria);
        }

        if ($processo !== null) {
            $builder->where('fk_id_processo_seletivo', $processo);
        }

        return $builder->countAllResults() > 0;
    }
}
