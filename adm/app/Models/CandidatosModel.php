<?php
namespace App\Models;

use CodeIgniter\Model;

class CandidatosModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados';
    protected $primaryKey = 'pk_id_cadastrado';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
       'pk_id_cadastrado','fk_id_gov','ds_nome','ds_cpf','ds_nascimento','ds_nome_mae','fk_id_pne','ds_outra_pne','ds_cep','ds_rua','ds_numero',
       'ds_complemento','ds_nome_bairro','ds_cidade','ds_uf','ds_celular','ds_fixo','ds_email','ds_data_cadastro','ds_hora_cadastro',
       'ds_data_alteracao','ds_hora_alteracao','ds_ip_cadastro'
    ];	

    protected $validationRules = [
        'ds_nome' => 'required',
        'ds_cpf' => 'required',
        'ds_nascimento' => 'required',
        //'ds_nome_mae' => 'required',
        'fk_id_pne' => 'required',
        'ds_cep' => 'required',
        'ds_rua' => 'required',
        'ds_numero' => 'required',
        'ds_nome_bairro' => 'required',
        'ds_celular' => 'required',
        'ds_email' => 'required',
    ];

    protected $validationMessages   = [
        'ds_nome' => [
            'required' => 'O campo Nome é obrigatório.',
        ],
        'ds_cpf' => [
            'required' => 'O campo CPF é obrigatório.',
        ],
        'ds_nascimento' => [
            'required' => 'O campo Data de Nascimento é obrigatório.',
        ],
        'ds_nome_mae' => [
            
        ],
        'fk_id_pne' => [
            'required' => 'O campo PNE é obrigatório.',
        ],
        'ds_cep' => [
            'required' => 'O campo CEP é obrigatório.',
        ],
        'ds_rua' => [
            'required' => 'O campo Rua é obrigatório.',
        ],
        'ds_numero' => [
            'required' => 'O campo Número é obrigatório.',
        ],
        'ds_nome_bairro' => [
            'required' => 'O campo Bairro é obrigatório.',
        ],
        'ds_celular' => [
            'required' => 'O campo Celular é obrigatório.',
        ],
        'ds_email' => [
            'required' => 'O campo Email é obrigatório.',
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
      // Callbacks
      protected $allowCallbacks = true;
      protected $beforeInsert   = [
         
      ];


    
    public function getCandidatosPorEditalCargo($idEdital, $idCargo){
        return $this->db->table('tb_cadastrados AS c')
        ->select([
            'c.pk_id_cadastrado',
            'c.ds_nome',
            'c.ds_cpf',
            'c.ds_data_cadastro',
            'c.ds_hora_cadastro',
            'c.ds_nascimento',
            'c.ds_email',
            'c.ds_celular',
            'p.fk_id_edital',
            'p.fk_id_cargo',
            'p.ds_protocolo',
            'e.ds_numero_edital',
            'cg.pk_id_cargo',
            'cg.ds_nome_cargo'
        ])
        ->join(
            'tb_cadastrados_protocolo AS p',
            'c.pk_id_cadastrado = p.fk_id_cadastrado',
            'inner'
        )
        ->join(
            'tb_cargos AS cg',
            'cg.pk_id_cargo = p.fk_id_cargo',
            'inner'
        )
        ->join('tb_editais as e', 'e.pk_id_edital = p.fk_id_edital')

        ->where('p.fk_id_edital', $idEdital)
        ->where('p.fk_id_cargo', $idCargo)
        ->orderBy('c.ds_nome', 'ASC')
        ->get()
        ->getResult();
    }




      public function listarCandidatoCpf($cpf){
        $this->select('tb_cadastrados.*, tb_dados_academicos.*');
        $this->join('tb_dados_academicos', 'tb_cadastrados.pk_id_cadastrado = tb_dados_academicos.pk_id_cadastrado');
        $this->where('tb_cadastrados.ds_cpf', $cpf);
        $this->orderBy('tb_cadastrados.ds_cpf', 'asc');
        return $this->first();
    }
    public function listarCandidatoId($edital,$cargo,$id){
        $this->select('tb_cadastrados.*');
        $this->where('tb_cadastrados.pk_id_cadastrado', $id);
        return $this->first();
    }
    public function listarExperienciaCandidato($edital,$cargo,$id){
        return $this->db->table('tb_cadastrados_experiencias exp')
        ->select("
            exp.fk_id_experiencia,
            exp.ds_quantidade,
            exp.ds_multiplicador,
            e.ds_nome_experiencia,
            e.ds_tipo_experiencia
        ")
        ->join(
            'tb_experiencias e',
            'e.pk_id_experiencia = exp.fk_id_experiencia'
        )
        ->where('exp.fk_id_cadastrado', $id)
        ->where('exp.fk_id_edital', $edital)
        ->where('exp.fk_id_cargo', $cargo)
        ->get()
        ->getResult();
    }

    public function listarEscolaridadeCandidato($edital,$cargo,$id){
        return $this->db->table('tb_cadastrados_escolaridades ce')
        ->select("
            ce.fk_id_escolaridade,
            ce.ds_quantidade,
            ce.ds_multiplicador,
            e.ds_nome_escolaridade
        ")
        ->join(
            'tb_escolaridades e',
            'e.pk_id_escolaridade = ce.fk_id_escolaridade'
        )
        ->where('ce.fk_id_cadastrado', $id)
        ->where('ce.fk_id_edital', $edital)
        ->where('ce.fk_id_cargo', $cargo)
        ->get()
        ->getResult();
    }
    public function listarAperfeicoamentoCandidato($edital,$cargo,$id){
        return $this->db->table('tb_cadastrados_aperfeicoamentos ap')
       ->select("
            ap.fk_id_curso,
            ap.ds_quantidade,
            ap.ds_multiplicador,
            ca.ds_nome_curso
        ")
        ->join(
            'tb_cursos_aperfeicoamentos ca',
            'ca.pk_id_curso = ap.fk_id_curso'
        )
        ->where('ap.fk_id_cadastrado', $id)
        ->where('ap.fk_id_edital', $edital)
        ->where('ap.fk_id_cargo', $cargo)
        ->get()
        ->getResult();
    }
    public function contarCandidatos($ano){
        $sql = "SELECT COUNT(*) AS total FROM tb_cadastrados WHERE YEAR(ds_datacadastro) = $ano";
        $query = $this->db->query($sql);
        $resultado = $query->getRow();
        return $resultado->total;
    }

    
}