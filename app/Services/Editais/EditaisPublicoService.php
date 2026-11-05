<?php

declare(strict_types=1);

namespace App\Services\Editais;

use App\Enums\Admin\SituacaoProcessosEnum;
use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Models\Admin\SecretariasModel;
use App\Models\CargosModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Editais\Interfaces\IEditaisPublicoService;

/**
 * Service responsável por agregar os dados exibidos na página pública de Editais.
 *
 * Ele consulta tb_processos, tb_processos_documentos, tb_secretarias,
 * tb_processos_cargos e tb_cargos, formatando tudo para a view DadosEditais_view.php.
 */
class EditaisPublicoService implements IEditaisPublicoService
{
    public function __construct(
        private ProcessosSeletivosModel $processosModel,
        private ProcessosDocumentosModel $documentosModel,
        private SecretariasModel $secretariasModel,
        private CargosModel $cargosModel,
    ) {
        helper('edital');
    }

    public function listarEditais(array $filtros = []): array
    {
        $situacaoFiltro = $filtros['situacao'] ?? 'Todas';
        $secretariaFiltro = isset($filtros['secretaria']) && $filtros['secretaria'] !== ''
            ? (int) $filtros['secretaria']
            : null;
        $cargoFiltro = $filtros['cargo'] ?? 'Todos';

        $processos = $this->buscarProcessosComSituacao();
        $editais = [];

        foreach ($processos as $processo) {
            $situacaoId = (int) ($processo->nm_id_situacao ?? 0);

            // Filtro por situação (Vigentes / Encerrados / Todas)
            if (!$this->situacaoPermitida($situacaoId, $situacaoFiltro)) {
                continue;
            }

            // Filtro por secretaria
            if ($secretariaFiltro !== null && (int) ($processo->fk_id_secretaria ?? 0) !== $secretariaFiltro) {
                continue;
            }

            $idProcesso = (int) $processo->pk_id_processo;
            $secretaria = $this->formatarSecretaria($processo);
            $cargos = $this->buscarCargosPorProcesso($idProcesso);

            // Filtro por cargo
            if ($cargoFiltro !== 'Todos' && !$this->processoPossuiCargo($cargos, $cargoFiltro)) {
                continue;
            }

            $documentos = $this->buscarDocumentosPorProcesso($idProcesso);

            $editais[] = $this->montarEdital(
                $processo,
                $secretaria,
                $cargos,
                $documentos
            );
        }

        return $editais;
    }

    public function obterSecretariasFiltro(): array
    {
        return $this->secretariasModel
            ->orderBy('ds_nome_secretaria', 'ASC')
            ->findAll();
    }

    public function obterCargosFiltro(): array
    {
        return $this->cargosModel
            ->orderBy('ds_nome_cargo', 'ASC')
            ->findAll();
    }

    /**
     * Busca todos os processos não deletados, trazendo a situação atual e o nome da secretaria.
     */
    private function buscarProcessosComSituacao(): array
    {
        return $this->processosModel->listarComSituacaoESecretaria();
    }

    /**
     * Busca os cargos vinculados a um processo seletivo.
     */
    private function buscarCargosPorProcesso(int $idProcesso): array
    {
        return $this->processosModel->cargosPorProcesso($idProcesso);
    }

    /**
     * Busca os documentos de um processo e os separa em edital de abertura e retificações.
     */
    private function buscarDocumentosPorProcesso(int $idProcesso): array
    {
        $documentos = $this->documentosModel->porProcessoId($idProcesso);

        $editais = [];
        $retificacoes = [];

        foreach ($documentos as $doc) {
            $tipoId = (int) $doc->nm_tipo;
            $formatado = $this->formatarDocumento($doc);

            if ($tipoId === TiposDocumentosEnum::EDITAL_ABERTURA->value) {
                $editais[] = $formatado;
            } elseif ($tipoId === TiposDocumentosEnum::RETIFICACAO->value) {
                $retificacoes[] = $formatado;
            }
        }

        return [
            'editais'      => $editais,
            'retificacoes' => $retificacoes,
        ];
    }

    /**
     * Formata um documento para exibição na view.
     */
    private function formatarDocumento(object $doc): array
    {
        $tipoEnum = TiposDocumentosEnum::tryFrom((int) $doc->nm_tipo);

        return [
            'id'               => (int) $doc->pk_id_documento,
            'tipo'             => $tipoEnum?->label() ?? 'Documento',
            'tipo_id'          => (int) $doc->nm_tipo,
            'numero'           => (int) $doc->nm_numero_documento,
            'ano'              => (int) $doc->nm_ano_documento,
            'numero_formatado' => formatarNumeroEdital(($doc->nm_numero_documento ?? '') . ($doc->nm_ano_documento ?? '')),
            'codigo'           => $doc->ds_codigo_oficial,
            'arquivo'          => $doc->ds_arquivo,
            'link'             => $doc->ds_link,
            'vigente'          => (bool) $doc->nm_versao_vigente,
            'data_publicacao'  => isset($doc->dt_data_publicacao) ? date('d/m/Y', strtotime($doc->dt_data_publicacao)) : null,
        ];
    }

    /**
     * Monta o array final de um edital/processos seletivo para a view.
     */
    private function montarEdital(
        object $processo,
        object $secretaria,
        array $cargos,
        array $documentos
    ): array {
        $situacaoId = (int) ($processo->nm_id_situacao ?? 0);
        $enum = SituacaoProcessosEnum::tryFrom($situacaoId);

        $inscricaoAberta = in_array($situacaoId, [
            SituacaoProcessosEnum::INSCRICOES_ABERTAS->value,
            SituacaoProcessosEnum::AGENDADO->value,
        ], true);

        $dataInicio = isset($processo->dt_inicio_inscricao)
            ? date('d/m/Y', strtotime($processo->dt_inicio_inscricao))
            : '—';
        $dataFim = isset($processo->dt_fim_inscricao)
            ? date('d/m/Y', strtotime($processo->dt_fim_inscricao))
            : '—';

        return [
            'id'                => (int) $processo->pk_id_processo,
            'numero_formatado'  => formatarNumeroEdital(($processo->nm_numero_processo ?? '') . ($processo->nm_ano_processo ?? '')),
            'numero'            => (int) ($processo->nm_numero_processo ?? 0),
            'ano'               => (int) ($processo->nm_ano_processo ?? 0),
            'objeto'            => $processo->ds_objeto ?? 'Objetivo não informado.',
            'secretaria'        => $secretaria,
            'situacao_id'       => $situacaoId,
            'situacao_label'    => $enum?->label() ?? 'Status não encontrado',
            'tag_class'         => $this->resolverTagClass($situacaoId),
            'inscricao_aberta'  => $inscricaoAberta,
            'data_inicio'       => $dataInicio,
            'data_fim'          => $dataFim,
            'periodo_inscricao' => $dataInicio . ' a ' . $dataFim,
            'data_homologacao'  => isset($processo->dt_data_homologacao)
                ? date('d/m/Y', strtotime($processo->dt_data_homologacao))
                : null,
            'cargos'            => $cargos,
            'total_vagas'       => array_sum(array_column($cargos, 'nm_vagas')),
            'editais'           => $documentos['editais'],
            'retificacoes'      => $documentos['retificacoes'],
        ];
    }

    private function formatarSecretaria(object $processo): object
    {
        return (object) [
            'id'     => (int) ($processo->fk_id_secretaria ?? 0),
            'nome'   => $processo->ds_nome_secretaria ?? 'Secretaria não informada',
            'sigla'  => $processo->ds_sigla_secretaria ?? '',
        ];
    }

    /**
     * Resolve a classe CSS da tag de situação conforme o padrão home-novo.css.
     */
    private function resolverTagClass(int $situacaoId): string
    {
        return match ($situacaoId) {
            SituacaoProcessosEnum::INSCRICOES_ABERTAS->value,
            SituacaoProcessosEnum::AGENDADO->value => 'home-tag--aberto',
            SituacaoProcessosEnum::VIGENTE->value => 'home-tag--vigente',
            SituacaoProcessosEnum::ENCERRADO->value => 'home-tag--encerrado',
            default => 'home-tag--encerrado',
        };
    }

    /**
     * Verifica se o processo deve ser exibido de acordo com o filtro de situação.
     */
    private function situacaoPermitida(int $situacaoId, string $filtro): bool
    {
        return match ($filtro) {
            'Vigentes'   => $situacaoId !== SituacaoProcessosEnum::ENCERRADO->value,
            'Encerrados' => $situacaoId === SituacaoProcessosEnum::ENCERRADO->value,
            default      => true,
        };
    }

    /**
     * Verifica se o processo possui um cargo com o nome selecionado no filtro.
     */
    private function processoPossuiCargo(array $cargos, string $cargoFiltro): bool
    {
        foreach ($cargos as $cargo) {
            $nomeCargo = $cargo->ds_nome_cargo ?? '';
            if ($nomeCargo === $cargoFiltro) {
                return true;
            }
        }

        return false;
    }
}
