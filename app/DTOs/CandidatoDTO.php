<?php
namespace App\DTOs;
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
    public ?string $dataCadastro = null;
    public ?string $horaCadastro = null;
    public ?string $dataAlteracao = null;
    public ?string $horaAlteracao = null;
    public ?string $ipCadastro = null;
    // Método factory para criar DTO a partir de array (formulário)
    public static function fromArray(array $data): self
    {
        $dto = new self();
        
        // Formatação da data
        if (!empty($data['ds_nascimento'])) {
            list($dia, $mes, $ano) = explode('/', $data['ds_nascimento']);
            $dto->nascimento = "$ano-$mes-$dia";
        }

        $dto->id = !empty($data['pk_id_cadastrado']) ? (int) $data['pk_id_cadastrado'] : null;
        $dto->fkIdGov = $data['fk_id_gov'] ?? null;
        $dto->nome = $data['ds_nome'] ?? '';
        $dto->cpf = preg_replace('/[^0-9]/', '', $data['ds_cpf'] ?? '');
        $dto->nomeMae = $data['ds_nome_mae'] ?? null;
        $dto->fkIdPne = $data['fk_id_pne'] ?? null;
        $dto->outraPne = $data['ds_outra_pne'] ?? null;
        $dto->cep = preg_replace('/[^0-9]/', '', $data['ds_cep'] ?? '');
        $dto->rua = $data['ds_rua'] ?? null;
        $dto->numero = $data['ds_numero'] ?? null;
        $dto->complemento = $data['ds_complemento'] ?? null;
        $dto->bairro = $data['ds_nome_bairro'] ?? null;
        $dto->cidade = $data['ds_cidade'] ?? null;
        $dto->uf = $data['ds_uf'] ?? null;
        $dto->celular = preg_replace('/[^0-9]/', '', $data['ds_celular'] ?? '');
        $dto->telefoneFixo = preg_replace('/[^0-9]/', '', $data['ds_fixo'] ?? '');
        $dto->email = $data['ds_email'] ?? null;
        $dto->ipCadastro = $_SERVER['REMOTE_ADDR'] ?? null;
        return $dto;
    }
    // Método para converter para array (para salvar no banco)
    public function toArray(bool $isCreate = true): array
    {
        $data = [
            'fk_id_gov' => $this->fkIdGov,
            'ds_nome' => $this->nome,
            'ds_cpf' => $this->cpf,
            'ds_nascimento' => $this->nascimento,
            'ds_nome_mae' => $this->nomeMae,
            'fk_id_pne' => $this->fkIdPne,
            'ds_outra_pne' => $this->outraPne,
            'ds_cep' => $this->cep,
            'ds_rua' => $this->rua,
            'ds_numero' => $this->numero,
            'ds_complemento' => $this->complemento,
            'ds_nome_bairro' => $this->bairro,
            'ds_cidade' => $this->cidade,
            'ds_uf' => $this->uf,
            'ds_celular' => $this->celular,
            'ds_fixo' => $this->telefoneFixo,
            'ds_email' => $this->email,
        ];
        if ($isCreate) {
            $data['ds_data_cadastro'] = date('Y-m-d');
            $data['ds_hora_cadastro'] = date('H:i:s');
            $data['ds_ip_cadastro'] = $this->ipCadastro;
        } else {
            $data['ds_data_alteracao'] = date('Y-m-d');
            $data['ds_hora_alteracao'] = date('H:i:s');
        }
        return $data;
    }
}