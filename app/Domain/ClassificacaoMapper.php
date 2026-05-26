<?php

namespace App\Domain\Classificacao;

use App\DTO\ClassificacaoDTO;

final class ClassificacaoMapper
{
    public function map(object $row): ClassificacaoDTO
    {
        return new ClassificacaoDTO(
            idCandidato: (int) $row->fk_id_cadastrado,
            nome: $row->ds_nome,
            dataNascimento: $row->ds_nascimento,
            idCargo: (int) $row->fk_id_cargo,
            idEdital: (int) $row->fk_id_edital,
            totalGeral: (float) $row->total_geral,
            tempoExperiencia: (float) $row->tempo_experiencia,
            possuiSuperiorEngenharia: (bool) $row->possui_superior_engenharia,
            quantidadePosLato: (int) $row->qtd_pos_lato
        );
    }
}