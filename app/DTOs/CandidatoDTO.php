<?php
namespace App\DTOs;

use App\Utils\Formatador;

class CandidatoDTO
{
    public ?int $id = null;
    public ?int $fkIdGov = null;
    public string $nome;
    public string $cpf;
    public ?string $email = null;
    public ?string $nascimento = null;
    public ?string $nomeMae = null;
    public ?int $fkIdPne = null;
    public ?string $outraPne = null;
    public ?string $cep = null;
    public ?string $rua = null;
    public ?string $numero = null;
    public ?string $complemento = null;
    public ?string $bairro = null;
    public ?string $cidade = null;
    public ?string $uf = null;
    public ?string $celular = null;
    public ?string $telefoneFixo = null;

    /**
     * Cria DTO a partir de array (formulário).
     * Responsabilidade: transportar e formatar dados do formulário.
     * NÃO acessa $_SERVER e NÃO gerencia timestamps.
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();

        $dto->id          = !empty($data['pk_id_cadastrado']) ? (int) $data['pk_id_cadastrado'] : null;
        $dto->fkIdGov     = $data['fk_id_gov'] ?? null;
        $dto->nome        = $data['ds_nome'] ?? '';
        $dto->cpf         = Formatador::limparCpf($data['ds_cpf'] ?? '');
        $dto->nascimento  = Formatador::formatarDataBrParaIso($data['ds_nascimento'] ?? null);
        $dto->nomeMae     = $data['ds_nome_mae'] ?? null;
        $dto->fkIdPne     = $data['fk_id_pne'] ?? null;
        $dto->outraPne    = $data['ds_outra_pne'] ?? null;
        $dto->cep         = Formatador::limparCep($data['ds_cep'] ?? null);
        $dto->rua         = $data['ds_rua'] ?? null;
        $dto->numero      = $data['ds_numero'] ?? null;
        $dto->complemento = $data['ds_complemento'] ?? null;
        $dto->bairro      = $data['ds_nome_bairro'] ?? null;
        $dto->cidade      = $data['ds_cidade'] ?? null;
        $dto->uf          = $data['ds_uf'] ?? null;
        $dto->celular     = Formatador::limparTelefone($data['ds_celular'] ?? null);
        $dto->telefoneFixo= Formatador::limparTelefone($data['ds_fixo'] ?? null);
        $dto->email       = $data['ds_email'] ?? null;

        return $dto;
    }

    /**
     * Converte o DTO para array.
     * Responsabilidade: apenas converter propriedades para array.
     * NÃO adiciona timestamps nem IP.
     */
    public function toArray(): array
    {
        return [
            'fk_id_gov'         => $this->fkIdGov,
            'ds_nome'           => $this->nome,
            'ds_cpf'            => $this->cpf,
            'ds_nascimento'     => $this->nascimento,
            'ds_nome_mae'       => $this->nomeMae,
            'fk_id_pne'         => $this->fkIdPne,
            'ds_outra_pne'      => $this->outraPne,
            'ds_cep'            => $this->cep,
            'ds_rua'            => $this->rua,
            'ds_numero'         => $this->numero,
            'ds_complemento'    => $this->complemento,
            'ds_nome_bairro'    => $this->bairro,
            'ds_cidade'         => $this->cidade,
            'ds_uf'             => $this->uf,
            'ds_celular'        => $this->celular,
            'ds_fixo'           => $this->telefoneFixo,
            'ds_email'          => $this->email,
        ];
    }
}
