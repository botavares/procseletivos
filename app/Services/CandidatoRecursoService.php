<?php
namespace App\Services;

class CandidatoRecursoService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Busca resumo dos recursos (protocolos distintos) de um candidato
     */
    public function buscarResumoRecursosPorCandidato(int $candidatoId): array
    {
        return $this->db->table('tb_cadastrados_recursos hr')
            ->select([
                'hr.ds_numero_protocolo',
                'hr.fk_id_edital',
                'hr.fk_id_cargo',
                'e.ds_numero_edital',
                'cg.ds_nome_cargo',
            ])
            ->join('tb_editais e', 'e.pk_id_edital = hr.fk_id_edital', 'left')
            ->join('tb_cargos cg', 'cg.pk_id_cargo = hr.fk_id_cargo', 'left')
            ->where('hr.fk_id_candidato', $candidatoId)
            ->groupBy('hr.ds_numero_protocolo')
            ->orderBy('hr.ds_numero_protocolo', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Busca detalhes completos de um recurso pelo protocolo
     */
    public function buscarDetalhesRecurso(string $protocolo): array
    {
        $recursos = $this->db->table('tb_cadastrados_recursos hr')
            ->select([
                'hr.*',
                'e.ds_numero_edital',
                'cg.ds_nome_cargo',
            ])
            ->join('tb_editais e', 'e.pk_id_edital = hr.fk_id_edital', 'left')
            ->join('tb_cargos cg', 'cg.pk_id_cargo = hr.fk_id_cargo', 'left')
            ->where('hr.ds_numero_protocolo', $protocolo)
            ->get()
            ->getResult();

        if (empty($recursos)) {
            return [];
        }

        $primeiro = $recursos[0];
        $numeroEdital = $this->formatarNumeroEdital($primeiro->ds_numero_edital ?? '');

        $campos = [];
        foreach ($recursos as $r) {
            $nomeCampo = $this->buscarNomeCampo($r->ds_campo_alterado, (int) $r->fk_id_campo_alterado);
            $tipoCampo = $this->buscarTipoCampo(
                $r->ds_campo_alterado,
                (int) $r->fk_id_cargo,
                (int) $r->fk_id_campo_alterado
            );
            $status = $this->mapearStatus($r->ds_tipo);

            $campos[] = [
                'id' => $r->pk_id_historico,
                'categoria' => $r->ds_campo_alterado,
                'nome_campo' => $nomeCampo,
                'tipo' => $r->ds_tipo,
                'status' => $status,
                'status_label' => $this->statusLabel($status),
                'valor_antigo' => $this->formatarValor($r->ds_campo_alterado, $tipoCampo, $r->ds_valor_antigo),
                'valor_novo' => $this->formatarValor($r->ds_campo_alterado, $tipoCampo, $r->ds_valor_novo),
                'observacao' => $r->ds_observacao ?? '',
            ];
        }

        return [
            'protocolo' => $protocolo,
            'numero_edital' => $numeroEdital,
            'nome_cargo' => $primeiro->ds_nome_cargo ?? '',
            'fk_id_edital' => $primeiro->fk_id_edital,
            'fk_id_cargo' => $primeiro->fk_id_cargo,
            'campos' => $campos,
        ];
    }

    private function formatarNumeroEdital(string $dsNumeroEdital): string
    {
        if (empty($dsNumeroEdital)) {
            return '';
        }
        $ano = substr($dsNumeroEdital, -4);
        $numero = substr($dsNumeroEdital, 0, -4);
        $numero = ltrim($numero, '0');
        return $numero . '/' . $ano;
    }

    private function mapearStatus(?string $tipo): int
    {
        if ($tipo === 'indeferido') {
            return 0;
        }
        if (in_array($tipo, ['alterado', 'inserido', 'removido'])) {
            return 1;
        }
        return 2;
    }

    private function statusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Indeferido',
            1 => 'Deferido',
            2 => 'Aguardando análise',
            default => 'Desconhecido',
        };
    }

    private function buscarNomeCampo(string $categoria, int $idCampo): string
    {
        $tabela = match ($categoria) {
            'experiencias' => 'tb_experiencias',
            'escolaridades' => 'tb_escolaridades',
            'aperfeicoamentos' => 'tb_cursos_aperfeicoamentos',
            'criterios' => 'tb_criterios_adicionais',
            default => null,
        };

        $campoNome = match ($categoria) {
            'experiencias' => 'ds_nome_experiencia',
            'escolaridades' => 'ds_nome_escolaridade',
            'aperfeicoamentos' => 'ds_nome_curso',
            'criterios' => 'ds_nome_criterio',
            default => null,
        };

        $pk = match ($categoria) {
            'experiencias' => 'pk_id_experiencia',
            'escolaridades' => 'pk_id_escolaridade',
            'aperfeicoamentos' => 'pk_id_curso',
            'criterios' => 'pk_id_criterio',
            default => null,
        };

        if (!$tabela) {
            return 'Campo desconhecido';
        }

        $row = $this->db->table($tabela)
            ->select($campoNome)
            ->where($pk, $idCampo)
            ->get()
            ->getRow();

        return $row ? ($row->{$campoNome} ?? 'Campo não encontrado') : 'Campo não encontrado';
    }

    /**
     * Busca o tipo de campo (CHECK / INPUT) na configuração do cargo
     */
    private function buscarTipoCampo(string $categoria, int $idCargo, int $idCampo): string
    {
        $config = match ($categoria) {
            'experiencias' => [
                'tabela' => 'tb_cargos_experiencias',
                'fk' => 'fk_id_experiencia',
            ],
            'escolaridades' => [
                'tabela' => 'tb_cargos_escolaridades',
                'fk' => 'fk_id_escolaridade',
            ],
            'aperfeicoamentos' => [
                'tabela' => 'tb_cargos_aperfeicoamentos',
                'fk' => 'fk_id_curso',
            ],
            'criterios' => [
                'tabela' => 'tb_cargos_criterios_adicionais',
                'fk' => 'fk_id_criterio',
            ],
            default => null,
        };

        if (!$config) {
            return 'INPUT';
        }

        $row = $this->db->table($config['tabela'])
            ->select('ds_tipo_campo')
            ->where('fk_id_cargo', $idCargo)
            ->where($config['fk'], $idCampo)
            ->get()
            ->getRow();

        return $row ? ($row->ds_tipo_campo ?? 'INPUT') : 'INPUT';
    }

    /**
     * Formata o valor de acordo com a categoria e tipo de campo
     */
    private function formatarValor(string $categoria, string $tipoCampo, $valor): string
    {
        $v = $valor === null || $valor === '' ? null : (int) $valor;

        if ($tipoCampo === 'CHECK') {
            if ($v === null || $v === 0) {
                return 'Não';
            }
            return 'Sim';
        }

        // INPUT
        if ($categoria === 'experiencias') {
            if ($v === null) {
                return 'Não informado';
            }
            return $v . ' ano' . ($v > 1 ? 's' : '') . ' de experiência';
        }

        if ($v === null) {
            return 'Não informado';
        }

        return (string) $v;
    }
}
