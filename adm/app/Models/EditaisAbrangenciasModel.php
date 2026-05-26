<?php
namespace App\Models;

use CodeIgniter\Model;

class EditaisAbrangenciasModel extends Model{
    //Atributos
    protected $table = 'tb_editais_abrangencias';
    protected $primaryKey = 'fk_id_edital';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_abrangencia',
    ];

    protected $validationRules = [
        'fk_id_edital' => 'required',
        'fk_id_abrangencia' => 'required',
        
    ];

    protected $returnType = 'object';

     /**
     * Método para atualizar dados com base em duas condições.
     *
     * @param int $idCurso ID do curso
     * @param int $idEdital ID do edital
     * @param array $data Dados a serem atualizados
     * @return bool Retorna true se a atualização for bem-sucedida, ou false caso contrário
     */
    public function updateWithConditions($idEdital, $idCurso, $data)
    {
        return $this->db->table($this->table)  // Usa a tabela definida na propriedade
            ->set($data)  // Define os dados a serem atualizados
            ->where('fk_id_edtial', $idSetor)  // Condição 1: primeiro campo
            ->where('fk_id_abrangencia', $idCurso)  // Condição 2: segundo campo
            ->update();  // Realiza o update
    }
    

}