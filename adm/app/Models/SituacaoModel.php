<?php

namespace App\Models;

use CodeIgniter\Model;

use App\DTOs\Domain\Candidatos\SituacaoDTO;
use App\DTOs\Domain\Candidatos\SituacaoDetalhadaDTO;

class SituacaoModel extends Model
{
    protected $table = 'tb_situacao_candidato';

    protected $primaryKey = 'pk_id_situacao';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'fk_id_candidato',
        'fk_id_cargo',
        'fk_id_edital',
        'situacao'
    ];

    protected $returnType = 'array';

    public function getSituacaoCandidatoCargoEdital(
        int $edital,
        int $cargo,
        int $candidato
    ): ?SituacaoDTO {

        $result = $this
            ->where('fk_id_candidato', $candidato)
            ->where('fk_id_edital', $edital)
            ->where('fk_id_cargo', $cargo)
            ->first();

        if (!$result) {
            return null;
        }

        return SituacaoDTO::fromArray($result);
    }

    public function salvar(SituacaoDTO $dto): int
    {
        $this->insert($dto->toDatabaseArray());

        return (int) $this->getInsertID();
    }

    public function atualizar(
        int $id,
        SituacaoDTO $dto
    ): bool {

        return $this->update(
            $id,
            $dto->toDatabaseArray()
        );
    }

    public function excluir(int $id): bool
    {
        return $this->delete($id);
    }
}