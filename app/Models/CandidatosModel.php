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
       'fk_id_gov','ds_cpf','ds_rg','ds_emissorrg','ds_nome','ds_nascimento','fk_id_pne','ds_outra_pne','ds_cep','ds_rua','ds_numero',
       'ds_complemento','ds_nome_bairro','ds_cidade','ds_uf','ds_celular','ds_fixo','ds_email','ds_data_cadastro','ds_hora_cadastro',
       'ds_data_alteracao','ds_hora_alteracao','ds_ip_cadastro'
    ];	

    protected $validationRules = [
        'ds_nome' => 'required',
        'ds_cpf' => 'required',
        'ds_nascimento' => 'required',
        'fk_id_pne' => 'required',
        'ds_cep' => 'required',
        'ds_rua' => 'required',
        'ds_numero' => 'required',
        'ds_nome_bairro' => 'required',
        'ds_celular' => 'required',
        'ds_email' => 'required',
    ]

    ;

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
            'required' => 'O campo Bairro é obrigatório.',
        ],
        'ds_celular' => [
            'required' => 'O campo Celular é obrigatório.',
        ],
        'ds_email' => [
            'required' => 'O campo Email é obrigatório.',
        ]
    ];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;
      // Callbacks
      protected $allowCallbacks = true;
      protected $beforeInsert   = [
         
      ];


    



      public function listarCadastradoCpf($cpf){
        $this->select('tb_cadastrados.*, tb_dados_academicos.*');
        $this->join('tb_dados_academicos', 'tb_cadastrados.pk_id_cadastrado = tb_dados_academicos.pk_id_cadastrado');
        $this->where('tb_cadastrados.ds_cpf', $cpf);
        $this->orderBy('tb_cadastrados.ds_cpf', 'asc');
        return $this->first();
    }

    public function listarCadastradoId($id){
        $this->select('tb_cadastrados.*, tb_dados_academicos.*');
        //$this->join('tb_dados_academicos', 'tb_cadastrados.pk_id_cadastrado = tb_dados_academicos.pk_id_cadastrado');
        $this->where('tb_cadastrados.pk_id_cadastrado', $id);
        $this->orderBy('tb_cadastrados.ds_nome', 'asc');
        return $this->first();
    }
    public function contarCadastrados($periodo){
        $sql = "SELECT COUNT(*) AS total FROM tb_cadastrados WHERE YEAR(ds_data_cadastro) = $periodo";
        $query = $this->db->query($sql);
        $resultado = $query->getRow();
        return $resultado->total;
    }
}