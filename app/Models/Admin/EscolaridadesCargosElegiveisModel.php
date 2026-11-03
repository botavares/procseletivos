<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class EscolaridadesCargosElegiveisModel extends Model{
    protected $table = 'tb_escolaridades_cargos_elegiveis';
    protected $primaryKey = 'pk_id_escolaridade_cargo_elegivel';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_escolaridade',
        'fk_id_cargo',
    ];

    protected $validationRules = [
        'fk_id_escolaridade' => 'required|integer',
        'fk_id_cargo' => 'required|integer',
    ];

    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;

    public function listarCargosPorEscolaridade(int $idEscolaridade){
        return $this->db->table('tb_cargos c')
            ->select('c.*')
            ->join('tb_escolaridades_cargos_elegiveis ele', 'ele.fk_id_cargo = c.pk_id_cargo')
            ->where('ele.fk_id_escolaridade', $idEscolaridade)
            ->orderBy('c.ds_nome_cargo', 'ASC')
            ->get()
            ->getResult();
    }

    public function sincronizarCargos(int $idEscolaridade, array $idsCargos): void
    {
        // Se nenhum cargo selecionado, limpa vínculos = disponível para todos
        $this->where('fk_id_escolaridade', $idEscolaridade)->delete();

        foreach ($idsCargos as $idCargo) {
            if (empty($idCargo)) {
                continue;
            }
            $this->insert([
                'fk_id_escolaridade' => $idEscolaridade,
                'fk_id_cargo'        => (int) $idCargo,
            ]);
        }
    }

    /**
     * Retorna escolaridades elegíveis para um cargo.
     * Se a escolaridade não tiver vínculos, ela é considerada elegível para todos.
     */
    public function listarEscolaridadesPorCargo(int $idCargo)
    {
        $builder = $this->db->table('tb_escolaridades e')
            ->select('e.*')
            ->orderBy('e.ds_nome_escolaridade', 'ASC');

        $subQuery = $this->db->table('tb_escolaridades_cargos_elegiveis')
            ->select('fk_id_escolaridade')
            ->where('fk_id_cargo', $idCargo)
            ->getCompiledSelect();

        $builder->where("(
            e.pk_id_escolaridade IN ({$subQuery})
            OR NOT EXISTS (
                SELECT 1 FROM tb_escolaridades_cargos_elegiveis ele2
                WHERE ele2.fk_id_escolaridade = e.pk_id_escolaridade
            )
        )", null, false);

        return $builder->get()->getResult();
    }
}
