<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IEditalRepository;
use App\Models\EditaisCargosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\EditaisModel;

class EditalRepository implements IEditalRepository
{
    protected EditaisCargosModel $editaisCargos;
    protected EditaisCandidatosModel $editaisCandidatos;
    protected EditaisModel $editais;

    public function __construct(
        EditaisCargosModel $editaisCargos,
        EditaisCandidatosModel $editaisCandidatos,
        EditaisModel $editais
    ) {
        $this->editaisCargos = $editaisCargos;
        $this->editaisCandidatos = $editaisCandidatos;
        $this->editais = $editais;
    }
    public function listarEditaisAtivos(): array
    {
        return $this->editais->getEditaisAtivos();
    }

    public function listarEditaisVigentes(): array
    {
        return $this->editais->getEditaisVigentes();
    }

    public function buscarEditaisAtivosPorCargo(int $idCargo): ?object
    {
        return $this->editaisCargos->getEditaisAtivosIdCargo($idCargo);
    }

    public function buscarEditaisAtivosComCargos(): array
    {
        return $this->editaisCargos->getEditaisAtivosCargos();
    }

    public function buscarEditaisPorCandidato(int $idCandidato): array
    {
        return $this->editaisCandidatos->editaisPorId($idCandidato);
    }

    public function verificarVinculoCandidatoEdital(int $idCandidato, int $idEdital): ?object
    {
        return $this->editaisCandidatos->verificarVinculos($idCandidato, $idEdital);
    }
}
