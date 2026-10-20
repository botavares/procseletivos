<?php
declare(strict_types=1);

namespace App\Models;

use Config\Database;

class EditaisCargosModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getEditaisAtivosIdCargo($fk_id_cargo)
    {
        return $this->db->table('tb_editais_cargos')
            ->where('fk_id_cargo', $fk_id_cargo)
            ->join('tb_editais', 'tb_editais.pk_id_edital = tb_editais_cargos.fk_id_edital')
            ->where('tb_editais.ds_status', '1')
            ->orderBy('tb_editais.ds_data_inicial', 'DESC')
            ->first();
    }

    public function getEditaisAtivosCargos()
    {
        return $this->db->table('tb_editais_cargos')
            ->select('tb_editais_cargos.*, tb_editais.*, tb_cargos.*')
            ->join('tb_editais', 'tb_editais.pk_id_edital = tb_editais_cargos.fk_id_edital')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo')
            ->where('tb_editais.ds_status', '1')
            ->groupStart()
                ->where("EXISTS (
                    SELECT 1 FROM tb_cargos_experiencias e
                    WHERE e.fk_id_cargo = tb_editais_cargos.fk_id_cargo
                )", null, false)
                ->orWhere("EXISTS (
                    SELECT 1 FROM tb_cargos_aperfeicoamentos a
                    WHERE a.fk_id_cargo = tb_editais_cargos.fk_id_cargo
                )", null, false)
                ->orWhere("EXISTS (
                    SELECT 1 FROM tb_cargos_escolaridades s
                    WHERE s.fk_id_cargo = tb_editais_cargos.fk_id_cargo
                )", null, false)
                ->orWhere("EXISTS (
                    SELECT 1 FROM tb_cargos_criterios_adicionais c
                    WHERE c.fk_id_cargo = tb_editais_cargos.fk_id_cargo
                )", null, false)
            ->groupEnd()
            ->orderBy('tb_editais.ds_data_inicial', 'DESC')
            ->get()
            ->getResult();
    }
}
