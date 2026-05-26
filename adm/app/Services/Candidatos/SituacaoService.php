<?php

namespace App\Services\Candidatos;

use App\Models\SituacaoModel;

use App\DTOs\Domain\Candidatos\SituacaoDTO;

use App\Services\Base\AbstractCrudService;

class SituacaoService extends AbstractCrudService
{
    protected $dto = SituacaoDTO::class;

    public function __construct(
        private readonly SituacaoModel $situacaoModel,
        ?BaseConnection $db = null
    ){parent::__construct($db);}

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

    public function salvar(SituacaoDTO $dto): int
    {
        return $this->transactional(function () use ($dto) {

            return $this->situacaoModel
                ->salvar($dto);
        });
    }

    public function atualizar(
        int $id,
        SituacaoDTO $dto
    ): bool {

        return $this->transactional(function () use ($id, $dto) {

            return $this->situacaoModel
                ->atualizar($id, $dto);
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