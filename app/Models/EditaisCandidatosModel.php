<?php
declare(strict_types=1);

namespace App\Models;

use Config\Database;

class EditaisCandidatosModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function editaisPorId($id)
    {
        return $this->db->table('tb_editais_candidatos')
            ->select('ds_modo,ds_numero_edital,ds_data_inicial,ds_data_final')
            ->join('tb_editais', 'tb_editais.pk_id_edital = tb_editais_candidatos.fk_id_edital')
            ->where('tb_editais_candidatos.fk_id_candidato', $id)
            ->where('tb_editais.ds_status', 1)
            ->findAll();
    }

    public function verificarVinculos($idCandidato, $idEdital)
    {
        return $this->db->table('tb_editais_candidatos')
            ->where('fk_id_candidato', $idCandidato)
            ->where('fk_id_edital', $idEdital)
            ->first();
    }
}
