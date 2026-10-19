<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IRecursoRepository;
use CodeIgniter\Database\BaseConnection;

class RecursoRepository implements IRecursoRepository
{
    public function __construct(private BaseConnection $db) {}

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

    public function buscarDetalhesRecurso(string $protocolo): array
    {
        return $this->db->table('tb_cadastrados_recursos hr')
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
    }

    public function buscarNomeCampo(string $categoria, int $idCampo): string
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

    public function buscarTipoCampo(string $categoria, int $idCargo, int $idCampo): string
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
}
