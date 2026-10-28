<?php

declare(strict_types=1);

namespace App\Models\Admin;

use CodeIgniter\Model;

class ProcessosDocumentosModel extends Model
{
    protected $table            = 'tb_processos_documentos';
    protected $primaryKey       = 'pk_id_documento';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useTimestamps    = false;
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'is_deletado';

    protected $allowedFields = [
        'fk_processo_referencia',
        'nm_tipo',
        'nm_numero_documento',
        'nm_ano_documento',
        'ds_codigo_oficial',
        'ds_link',
        'ds_arquivo',
        'nm_versao_vigente',
        'nm_documento_retificado',
        'dt_data_publicacao',
    ];

    protected $validationRules = [
        'fk_processo_referencia'  => 'required|integer',
        'nm_tipo'                 => 'required|integer|in_list[1,2,3,4,5,6,7]',
        'nm_numero_documento'     => 'required|integer',
        'nm_ano_documento'        => 'required|integer',
        'ds_codigo_oficial'       => 'required|string|max_length[255]',
        'ds_link'                 => 'permit_empty|string|max_length[500]',
        'ds_arquivo'              => 'permit_empty|string|max_length[255]',
        'nm_versao_vigente'       => 'required|integer|in_list[0,1]',
        'nm_documento_retificado' => 'required|integer',
    ];

    protected $validationMessages = [
        'fk_processo_referencia' => [
            'required' => 'O campo Processo de Referência é obrigatório.',
            'integer'  => 'O campo Processo de Referência deve ser um número inteiro.',
        ],
        'nm_tipo' => [
            'required' => 'O campo Tipo é obrigatório.',
            'integer'  => 'O campo Tipo deve ser um número inteiro.',
            'in_list'  => 'O campo Tipo deve ser um valor válido.',
        ],
        'nm_numero_documento' => [
            'required' => 'O campo Número do Documento é obrigatório.',
            'integer'  => 'O campo Número do Documento deve ser um número inteiro.',
        ],
        'nm_ano_documento' => [
            'required' => 'O campo Ano do Documento é obrigatório.',
            'integer'  => 'O campo Ano do Documento deve ser um número inteiro.',
        ],
        'ds_codigo_oficial' => [
            'required'   => 'O campo Código Oficial é obrigatório.',
            'max_length' => 'O campo Código Oficial deve ter no máximo 255 caracteres.',
        ],
        'ds_link' => [
            'max_length' => 'O campo Link deve ter no máximo 500 caracteres.',
        ],
        'ds_arquivo' => [
            'max_length' => 'O campo Arquivo deve ter no máximo 255 caracteres.',
        ],
        'nm_versao_vigente' => [
            'required' => 'O campo Versão Vigente é obrigatório.',
            'integer'  => 'O campo Versão Vigente deve ser um número inteiro.',
        ],
        'nm_documento_retificado' => [
            'required' => 'O campo Documento Retificado é obrigatório.',
            'integer'  => 'O campo Documento Retificado deve ser um número inteiro.',
        ],
    ];

    /**
     * Lista documentos filtrando por tipo.
     */
    public function porTipo(int $tipo): array
    {
        return $this->where('nm_tipo', $tipo)
                    ->findAll();
    }

    /**
     * Lista documentos vinculados a um processo pelo número e ano do processo (join com tb_processos).
     */
    public function porNumeroProcesso(int $numero, int $ano): array
    {
        return $this->select('tb_processos_documentos.*')
                    ->join('tb_processos', 'tb_processos.pk_id_processo = tb_processos_documentos.fk_processo_referencia')
                    ->where('tb_processos.nm_numero_processo', $numero)
                    ->where('tb_processos.nm_ano_processo', $ano)
                    ->findAll();
    }

    /**
     * Lista documentos por número e ano do documento.
     */
    public function porNumeroDocumentoEAno(int $numero, int $ano): array
    {
        return $this->where('nm_numero_documento', $numero)
                    ->where('nm_ano_documento', $ano)
                    ->findAll();
    }

    /**
     * Lista documentos por faixa de tempo (baseado no ano do documento).
     */
    public function porFaixaDeTempo(string $dataInicio, string $dataFim): array
    {
        return $this->where("nm_ano_documento >=", (int) date('Y', strtotime($dataInicio)))
                    ->where("nm_ano_documento <=", (int) date('Y', strtotime($dataFim)))
                    ->findAll();
    }

    /**
     * Lista todos os documentos não deletados ordenados pelo ID descendente.
     */
    public function listarTodos(): array
    {
        return $this->orderBy('pk_id_documento', 'DESC')
                    ->findAll();
    }

    public function porSecretaria(int $idSecretaria): array
    {
        return $this->select('tb_processos_documentos.*, tb_processos.nm_numero_processo, tb_processos.nm_ano_processo')
                    ->join('tb_processos', 'tb_processos.pk_id_processo = tb_processos_documentos.fk_processo_referencia')
                    ->where('tb_processos.fk_id_secretaria', $idSecretaria)
                    ->orderBy('tb_processos_documentos.pk_id_documento', 'DESC')
                    ->findAll();
    }

    /**
     * Lista documentos vinculados a um processo pelo ID do processo.
     */
    public function porProcessoId(int $idProcesso): array
    {
        return $this->where('fk_processo_referencia', $idProcesso)
                    ->where('is_deletado IS NULL')
                    ->orderBy('nm_tipo', 'ASC')
                    ->orderBy('nm_numero_documento', 'DESC')
                    ->findAll();
    }

    /**
     * Verifica se j� existe um documento do tipo Edital de Abertura (1) para o processo informado.
     */
    public function existeEditalAbertura(int $idProcesso, ?int $excetoId = null): bool
    {
        $builder = $this->where('fk_processo_referencia', $idProcesso)
                       ->where('nm_tipo', 1)
                       ->where('is_deletado IS NULL');

        if ($excetoId) {
            $builder->where('pk_id_documento !=', $excetoId);
        }

        return $builder->countAllResults() > 0;
    }
}
