<?php

namespace App\Services\Admin\Candidatos;

use App\Services\Admin\Base\TransactionalTrait;

use App\Models\Admin\SituacaoModel;
use CodeIgniter\Database\BaseConnection;

class SituacaoService 
{
    use TransactionalTrait;

    public function __construct(
        private readonly SituacaoModel $situacaoModel,
        ?BaseConnection $db = null
    ){
        $this->db = $db ?? \Config\Database::connect();
    }

    public function getCandidatoCargoEdital(
        int $edital,
        int $cargo,
        int $candidato
    ){
        return $this->situacaoModel
            ->getSituacaoCandidatoCargoEdital(
                $edital,
                $cargo,
                $candidato
            );
    }

    public function salvar(array $data): int
    {
        return $this->transactional(function () use ($data) {

            return $this->situacaoModel
                ->salvar($data);
        });
    }

    public function atualizar(
        int $id,
        array $data
    ): bool {

        return $this->transactional(function () use ($id, $data) {

            return $this->situacaoModel
                ->atualizar($id, $data);
        });
    }

    public function excluir(int $id): bool
    {
        return $this->transactional(function () use ($id) {

            return $this->situacaoModel
                ->excluir($id);
        });
    }
}