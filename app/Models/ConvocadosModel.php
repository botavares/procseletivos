<?php
namespace App\Models;

use CodeIgniter\Model;

class ConvocadosModel extends Model{
    //Atributos
    protected $table = 'tb_dados_convocacao';
    protected $primaryKey = 'pk_id_convocacao';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
       'fk_id_candidato','fk_id_edital','fk_id_curso','ds_comparecimento','ds_mensagem','ds_data','ds_hora','ds_interesse','ds_status'
    ];	

    protected $validationRules = [
        'fk_id_candidato'   => 'required',
        'fk_id_edital'      => 'required',
        'fk_id_curso'       => 'required',
        'ds_comparecimento' => 'required',
        'ds_status'         => 'required',
        'ds_data'           => 'required',
        'ds_hora'           => 'required',
    ];

    protected $validationMessages   = [
        'fk_id_candidato'   => [
            'required'      => 'O campo Candidato é obrigatório.',
        ],
        'fk_id_edital'      => [
            'required'      => 'O campo Edital é obrigatório.',
        ],
        'fk_id_curso'       => [
            'required'      => 'O campo Curso é obrigatório.',
        ],
        'ds_comparecimento'         => [
            'required'      => 'O campo Status é obrigatório.',
        ],
        'ds_data'           => [
            'required'      => 'O campo Data é obrigatório.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
      // Callbacks
      protected $allowCallbacks = true;
      protected $beforeInsert   = [
         
      ];

      public function getCovocadosPorEditalCurso($idEdital, $idCurso){
       $builder = $this->db->table('tb_dados_academicos a')
    ->select([
        'a.*',
        'p.*',
        'e.*',
        'c.*'
    ])
    ->join(
        'tb_dados_pessoais p',
        'p.pk_id_candidato = a.pk_id_candidato',
        'inner'
    )
    ->join(
        '(SELECT fk_id_candidato, fk_id_edital
          FROM tb_editais_candidatos
          GROUP BY fk_id_candidato, fk_id_edital
        ) e',
        'e.fk_id_candidato = a.pk_id_candidato',
        'inner',
        false
    )
    ->join(
        'tb_dados_convocacao c',
        'c.fk_id_candidato = a.pk_id_candidato
         AND c.fk_id_edital = e.fk_id_edital
         AND c.ds_status = 1',
        'inner'
    )
    ->where('a.fk_id_curso', $idCurso)
    ->where('e.fk_id_edital', $idEdital)
    ->orderBy('a.ds_periodo', 'DESC')
    ->orderBy('a.ds_ensino_medio', 'ASC')
    ->orderBy('p.ds_nascimento', 'ASC');

return $builder->get()->getResult();



      }

      public function getTodosConvocados(){
       $builder = $this->db->table('tb_dados_academicos a')
        ->select(['a.*', 'p.*', 'e.*', 'c.*','u.*'])
        ->join('tb_dados_pessoais p', 'p.pk_id_candidato = a.pk_id_candidato')
        ->join('tb_editais_candidatos e', 'e.fk_id_candidato = a.pk_id_candidato')
        ->join('tb_dados_convocacao c', 'c.fk_id_candidato = a.pk_id_candidato')
        ->join('tb_cursos u', 'u.pk_id_curso = a.fk_id_curso')
        ->where('c.ds_status', '1')        
        ->orderBy('c.ds_data', 'desc')
        ->orderBy('a.ds_periodo', 'desc')
        ->orderBy('a.ds_ensino_medio', 'asc')
        ->orderBy('p.ds_nascimento', 'asc');
        return $builder->get()->getResult();
      }
    }