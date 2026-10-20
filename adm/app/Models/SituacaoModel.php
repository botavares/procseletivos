<?php

namespace App\Models;

use CodeIgniter\Model;

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
    ): ?array {

        $result = $this
            ->where('fk_id_candidato', $candidato)
            ->where('fk_id_edital', $edital)
            ->where('fk_id_cargo', $cargo)
            ->first();

        if (!$result) {
            return null;
        }

        return $result;
    }

    public function salvar(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    public function atualizar(
        int $id,
        array $data
    ): bool {

        return $this->update(
            $id,
            $data
        );
    }

    public function excluir(int $id): bool
    {
        return $this->delete($id);
    }
}