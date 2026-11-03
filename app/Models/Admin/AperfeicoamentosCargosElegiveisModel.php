<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class AperfeicoamentosCargosElegiveisModel extends Model{
    protected $table = 'tb_aperfeicoamentos_cargos_elegiveis';
    protected $primaryKey = 'pk_id_aperfeicoamento_cargo_elegivel';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_curso',
        'fk_id_cargo',
    ];

    protected $validationRules = [
        'fk_id_curso' => 'required|integer',
        'fk_id_cargo' => 'required|integer',
    ];

    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;

    public function listarCargosPorAperfeicoamento(int $idCurso){
        return $this->db->table('tb_cargos c')
            ->select('c.*')
            ->join('tb_aperfeicoamentos_cargos_elegiveis ele', 'ele.fk_id_cargo = c.pk_id_cargo')
            ->where('ele.fk_id_curso', $idCurso)
            ->orderBy('c.ds_nome_cargo', 'ASC')
            ->get()
            ->getResult();
    }

    public function sincronizarCargos(int $idCurso, array $idsCargos): void
    {
        $this->where('fk_id_curso', $idCurso)->delete();

        foreach ($idsCargos as $idCargo) {
            if (empty($idCargo)) {
                continue;
            }
            $this->insert([
                'fk_id_curso' => $idCurso,
                'fk_id_cargo' => (int) $idCargo,
            ]);
        }
    }

    /**
     * Retorna cursos de aperfeiçoamento elegíveis para um cargo.
     * Se o curso não tiver vínculos, é considerado elegível para todos.
     */
    public function listarAperfeicoamentosPorCargo(int $idCargo)
    {
        $builder = $this->db->table('tb_cursos_aperfeicoamentos c')
            ->select('c.*')
            ->orderBy('c.ds_nome_curso', 'ASC');

        $subQuery = $this->db->table('tb_aperfeicoamentos_cargos_elegiveis')
            ->select('fk_id_curso')
            ->where('fk_id_cargo', $idCargo)
            ->getCompiledSelect();

        $builder->where("(
            c.pk_id_curso IN ({$subQuery})
            OR NOT EXISTS (
                SELECT 1 FROM tb_aperfeicoamentos_cargos_elegiveis ele2
                WHERE ele2.fk_id_curso = c.pk_id_curso
            )
        )", null, false);

        return $builder->get()->getResult();
    }
}
