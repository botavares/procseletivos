<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EditaisModel;
use App\Models\CargosModel;
use App\Repositories\Interfaces\IEditalRepository;
use App\Services\Interfaces\IEditaisService;
use CodeIgniter\Database\BaseConnection;

class EditaisService implements IEditaisService
{
    protected BaseConnection $db;

    public function __construct(
        private EditaisModel $modelEditais,
        private CargosModel $modelCargos,
        private IEditalRepository $editalRepository,
    ) {
        $this->db = \Config\Database::connect();
    }

    public function listarTodosEditais(): array
    {
        return $this->modelEditais->listarTodosEditais()->getResultArray();
    }

    public function buscarEditaisAtivosCargos(): array
    {
        return $this->editalRepository->buscarEditaisAtivosComCargos();
    }

    public function listarEditaisAtivos(): array
    {
        return $this->modelEditais->where('ds_status', '1')->orderBy('ds_data_inicial', 'DESC')->findAll();
    }

    public function listarEditaisEncerrados(): array
    {
        return $this->modelEditais->where('ds_status', '0')->orderBy('ds_data_inicial', 'DESC')->findAll();
    }

    public function listarTodosCargos(): array
    {
        return $this->modelCargos->orderBy('ds_nome_cargo', 'asc')->findAll();
    }

    public function listarCargosPorEditais(array $editais): array
    {
        $cargosPorEdital = [];
        foreach ($editais as $edital) {
            $cargosVinculados = $this->db->table('tb_editais_cargos')
                ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo')
                ->where('tb_editais_cargos.fk_id_edital', $edital->pk_id_edital)
                ->get()
                ->getResult();
            $cargosPorEdital[$edital->pk_id_edital] = $cargosVinculados;
        }
        return $cargosPorEdital;
    }

    public function listarCargosPorEdital(int $editalId): array
    {
        return $this->modelCargos->cargosPorEdital($editalId);
    }

    public function listarCargosEditaisAtivos(): array
    {
        return $this->modelCargos->cargosEditaisAtivos();
    }

    public function autenticarEditalPorToken(string $token): ?object
    {
        return $this->modelEditais->where('ds_token_autenticacao', $token)->first();
    }

    public function estaAtivo($idEdital)
    {
        $dadosEdital = $this->modelEditais->where('pk_id_edital', $idEdital)->first();

        $dataInicialEdital = date('d/m/Y', strtotime($dadosEdital->ds_data_inicial));
        $dataFinalEdital = date('d/m/Y', strtotime($dadosEdital->ds_data_termino));

        $hoje = date('Y-m-d');
        if ($hoje < $dadosEdital->ds_data_inicial || $hoje > $dadosEdital->ds_data_termino) {
            return [
                'response' => false,
                'mensagemError' => "O período de cadastro para este edital é de {$dataInicialEdital} a {$dataFinalEdital}."
            ];
        }

        return true;
    }
}
