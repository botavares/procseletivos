<?php
namespace App\Models;

use CodeIgniter\Model;

class EditaisModel extends Model{
    //Atributos
    protected $table = 'tb_editais';
    protected $primaryKey = 'pk_id_edital';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_numero_edital','ds_data_inicial','ds_data_termino','ds_status','ds_arquivo_edital'
    ];

    protected $validationRules = [
        'ds_numero_edital'=>'required',
        'ds_data_inicial'=>'required',
        'ds_data_termino'=>'required',
        'ds_status'=>'required',
        
    ];
    //mensagens de validação
    protected $validationMessages = [
        'ds_numero_edital' => [
            'required' => 'O campo Número do Edital é obrigatório.'
        ],
        'ds_data_inicial' => [
            'required' => 'O campo Data Inicial é obrigatório.'
        ],
        'ds_data_termino' => [
            'required' => 'O campo Data Término é obrigatório.'
        ],
        'ds_status' => [
            'required' => 'O campo Status é obrigatório.'
        ]
    ];

    protected $returnType = 'object';

    public function getEditais(){
        return $this->findAll();
    }
    public function getEdital($id){
        return $this->find($id);
    }

    public function getEditaisAtivos()
    {
        return $this->where('ds_status', 1)
            ->orderBy('ds_numero_edital', 'ASC')
            ->findAll();
    }

    /**
     * Retorna cargos dos editais ativos com a contagem de candidatos inscritos
     */
    public function getCargosComContagemCandidatosPorEditalAtivo(): array
    {
        return $this->db->table('tb_editais AS e')
            ->select([
                'e.pk_id_edital',
                'e.ds_numero_edital',
                'c.pk_id_cargo',
                'c.ds_nome_cargo',
                'COUNT(cp.fk_id_cadastrado) AS total_candidatos'
            ])
            ->join('tb_editais_cargos ec', 'ec.fk_id_edital = e.pk_id_edital', 'inner')
            ->join('tb_cargos c', 'c.pk_id_cargo = ec.fk_id_cargo', 'inner')
            ->join('tb_cadastrados_protocolo cp', 'cp.fk_id_edital = e.pk_id_edital AND cp.fk_id_cargo = c.pk_id_cargo', 'left')
            ->where('e.ds_status', 1)
            ->groupBy('e.pk_id_edital, c.pk_id_cargo')
            ->orderBy('e.ds_numero_edital', 'ASC')
            ->orderBy('c.ds_nome_cargo', 'ASC')
            ->get()
            ->getResult();
    }

    

}