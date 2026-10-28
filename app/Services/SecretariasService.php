<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Admin\SecretariasModel;
use App\Services\Interfaces\ISecretariasService;

class SecretariasService implements ISecretariasService{
    public function __construct() {

    }

    public function getSecretarias(): array{
        $secretarias = (new SecretariasModel())->findAll();
        return array_map(function ($secretaria) {
            return (object) [
                'id' => $secretaria->pk_id_secretaria,
                'nome' => $secretaria->ds_nome_secretaria,
                'sigla' => $secretaria->ds_sigla_secretaria
            ];
        }, $secretarias);
    }

    public function getSecretariaById(int $id): ?object{
        $secretaria = (new SecretariasModel())->find($id);
        if (!$secretaria) {
            return null;
        }
        return (object) [
            'id'              => $secretaria->pk_id_secretaria ?? null,
            'nome'            => $secretaria->ds_nome_secretaria ?? null,
            'sigla'           => $secretaria->ds_sigla_secretaria ?? null,
            'email'           => $secretaria->ds_email_secretaria ?? null,
            'telefone'        => $secretaria->ds_telefone_secretaria ?? null,
            'rua'             => $secretaria->ds_rua ?? null,
            'numero'          => $secretaria->nm_numero ?? null,
            'complemento'     => $secretaria->ds_complemento ?? null,
            'bairro'          => $secretaria->ds_bairro ?? null,
            'emailSecretaria' => $secretaria->ds_email_secretaria ?? null,
            'atendimento'     => $secretaria->ds_atendimento ?? null,
            'responsavel_processo'=> $secretaria->ds_responsavel_processo ?? null
        ];
    }
}