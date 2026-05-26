<?php
namespace App\Models;

use CodeIgniter\Model;

class DadosContratosModel extends Model{
    //Atributos
    protected $table = 'tb_dados_contratos';
    protected $primaryKey = 'pk_id_contrato';
    protected $useAutoIncrement = true;
    protected $protectFields = true;
    protected $allowedFields = [
       'fk_id_candidato',
       'ds_numero_termo',
       'ds_ano_termo',
       'fk_id_setor',
       'ds_data_ingresso',
       'ds_data_encerramento',
       'ds_status',
       'ds_data_baixa',
       'fk_id_edital',
       'fk_id_curso',
       'ds_notificado',
       'ds_prorrogado',
       'ds_supervisor',
       'ds_cargo_supervisor',
       'fk_id_instituicao',
       'ds_orientador',
       'ds_representante_faculdade',
       'fk_id_auxilio',
       'ds_matricula',
       'ds_turno',
       'fk_id_seguro'
    ];

    protected $validationRules = [
        'ds_numero_termo'=> 'required',
        'ds_ano_termo'=> 'required',
        'fk_id_candidato' => 'required',
        'fk_id_setor' => 'required',
        'fk_id_edital' => 'required',
        'fk_id_curso' => 'required',
        'ds_data_ingresso' => 'required',
        'ds_data_encerramento' => 'required',
        'ds_status' => 'required',
        'ds_supervisor' => 'required',
        'fk_id_instituicao' => 'required',
        'ds_orientador' => 'required',
        'ds_representante_faculdade' => 'required',
        'ds_cargo_supervisor' => 'required',
        'fk_id_auxilio' => 'required',
        'ds_turno' => 'required',
        'fk_id_seguro' => 'required'
    ];
    //mensagens
    protected $validationMessages = [
        'ds_numero_termo'=> [
            'required' => 'O campo número do termo é obrigatório'
        ],
        'ds_ano_termo'=> [
            'required' => 'O campo ano do termo é obrigatório'
        ],
        'fk_id_candidato' => [
            'required' => 'O campo candidato é obrigatório'
        ],
        'fk_id_setor' => [
            'required' => 'O campo setor é obrigatório'
        ],
        'fk_id_edital' => [
            'required' => 'O campo edital é obrigatório'
        ],
        'fk_id_curso' => [
            'required' => 'O campo curso é obrigatório'
        ],
        'ds_data_ingresso' => [
            'required' => 'O campo data de ingresso é obrigatório'    
        ],
        'ds_data_encerramento' => [
            'required' => 'O campo data de encerramento é obrigatório'
        ],
        'ds_status' => [
            'required' => 'O campo status é obrigatório'
        ],
        'ds_supervisor' => [
            'required' => 'O campo supervisor é obrigatório'
        ],
        'fk_id_instituicao' => [
            'required' => 'O campo instituição é obrigatório'
        ],
        'ds_orientador' => [
            'required' => 'O campo orientador é obrigatório'
        ],
        'ds_representante_faculdade' => [
            'required' => 'O campo representante da faculdade é obrigatório'
        ],
        'ds_cargo_supervisor' => [
            'required' => 'O campo cargo do supervisor é obrigatório'
        ],
        'fk_id_auxilio' => [
            'required' => 'O campo auxílio é obrigatório'
        ],
        'ds_turno' => [
            'required' => 'O campo turno é obrigatório'
        ],
        'fk_id_seguro' => [
            'required' => 'O campo seguro é obrigatório'
        ]
    ];
      

    protected $returnType = 'object';

    public function getDadosContrato(){
        return $this->findAll();
    }
    public function getDadoContrato($id){
        return $this->where('pk_id_contrato', $id)->first();
    }

    public function getContratosPorId($id){
        $this->select('tb_dados_pessoais.ds_nome,tb_dados_pessoais.ds_nascimento,tb_dados_pessoais.ds_cpf,tb_dados_pessoais.ds_email,ds_numero_termo,ds_ano_termo,
        tb_dados_pessoais.ds_celular,tb_dados_pessoais.ds_rua,tb_dados_pessoais.ds_numero,tb_dados_pessoais.ds_complemento,tb_dados_pessoais.ds_nome_bairro,
        tb_dados_pessoais.ds_cidade,tb_dados_pessoais.ds_cep,tb_dados_pessoais.ds_uf,tb_dados_academicos.pk_id_candidato,tb_dados_academicos.ds_periodo,tb_dados_academicos.ds_registro_academico,
        tb_dados_contratos.ds_matricula,tb_editais.ds_numero_edital,tb_cursos.pk_id_curso,tb_cursos.ds_nome_curso, tb_setores.pk_id_setor, tb_setores.ds_nome_setor,tb_secretarias.ds_nome_secretaria,
        tb_secretarias.pk_id_secretaria,tb_secretarias.ds_secretario_diretor,tb_dados_pessoais.ds_celular,tb_dados_contratos.ds_turno,tb_dados_contratos.fk_id_auxilio,fk_id_seguro,
        tb_dados_contratos.pk_id_contrato, tb_dados_contratos.ds_data_ingresso, tb_dados_contratos.ds_data_encerramento, tb_dados_contratos.ds_status,ds_notificado,ds_prorrogado,tb_dados_pessoais.ds_email,
        tb_dados_contratos.ds_supervisor,ds_cargo_supervisor,tb_dados_contratos.fk_id_instituicao,tb_dados_contratos.ds_orientador,tb_verificador_contratos.ds_verificador,ds_representante_faculdade');
        $this->join('tb_dados_pessoais', 'tb_dados_pessoais.pk_id_candidato = tb_dados_contratos.fk_id_candidato');
        $this->join('tb_dados_academicos', 'tb_dados_academicos.pk_id_candidato = tb_dados_pessoais.pk_id_candidato');
        $this->join('tb_cursos', 'tb_cursos.pk_id_curso = tb_dados_academicos.fk_id_curso');
        //$this->join('tb_bairros', 'tb_bairros.pk_id_bairro = tb_dados_pessoais.fk_id_bairro');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = tb_dados_contratos.fk_id_edital');
        $this->join('tb_verificador_contratos', 'tb_verificador_contratos.pk_id_contrato = tb_dados_contratos.pk_id_contrato');
        $this->join('tb_setores', 'tb_setores.pk_id_setor = tb_dados_contratos.fk_id_setor');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this->where('tb_dados_contratos.pk_id_contrato', $id);
        return $this->first();
    }

    public function getContratosExpirando($prazoDias){
        $dataLimite = date('Y-m-d', strtotime("+$prazoDias days"));
        $this->select('tb_dados_pessoais.ds_nome, tb_dados_pessoais.ds_email,tb_dados_academicos.pk_id_candidato,tb_dados_pessoais.ds_celular,ds_numero_termo,ds_ano_termo,
        tb_dados_contratos.fk_id_candidato,fk_id_setor,
        tb_cursos.pk_id_curso, tb_cursos.ds_nome_curso, tb_setores.pk_id_setor, tb_setores.ds_nome_setor,tb_secretarias.ds_nome_secretaria,tb_secretarias.pk_id_secretaria,tb_dados_contratos.ds_matricula,
        tb_dados_contratos.pk_id_contrato, tb_dados_contratos.ds_data_ingresso, tb_dados_contratos.ds_data_encerramento, tb_dados_contratos.ds_status,ds_notificado,ds_prorrogado,
        tb_dados_contratos.ds_supervisor,tb_dados_contratos.fk_id_instituicao,tb_dados_contratos.ds_orientador');
        $this->join('tb_dados_pessoais', 'tb_dados_pessoais.pk_id_candidato = tb_dados_contratos.fk_id_candidato');
        $this->join('tb_dados_academicos', 'tb_dados_academicos.pk_id_candidato = tb_dados_pessoais.pk_id_candidato');
        $this->join('tb_cursos', 'tb_cursos.pk_id_curso = tb_dados_academicos.fk_id_curso');
        $this->join('tb_setores', 'tb_setores.pk_id_setor = tb_dados_contratos.fk_id_setor');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this->where('ds_data_encerramento <=', $dataLimite);
        $this->where('tb_dados_contratos.ds_notificado', '0');
        $this->where('tb_dados_contratos.ds_status', '1');
        $this->orderBy('tb_dados_contratos.ds_notificado', 'asc');
        $this->orderBy('tb_dados_contratos.ds_prorrogado', 'asc');

        return $this->findAll();
    }

    public function getContratosAtivos(){
        $this->select('tb_dados_contratos.*,tb_dados_pessoais.ds_nome,tb_setores.ds_nome_setor,tb_cursos.pk_id_curso,tb_cursos.ds_nome_curso,tb_editais.ds_numero_edital');
        $this->join('tb_dados_pessoais', 'tb_dados_pessoais.pk_id_candidato = tb_dados_contratos.fk_id_candidato');
        $this->join('tb_setores', 'tb_setores.pk_id_setor = tb_dados_contratos.fk_id_setor');
        $this->join('tb_cursos', 'tb_cursos.pk_id_curso = tb_dados_contratos.fk_id_curso');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = tb_dados_contratos.fk_id_edital');
        $this->where('tb_dados_contratos.ds_status', '1');
        return $this->findAll();
    }

     public function getContratosPorEditalCurso($idEdital,$idCurso){
        $builder = $this->db->table('tb_dados_academicos a')
        ->select(['a.*','p.*','e.*'])
        ->join('tb_dados_pessoais p', 'p.pk_id_candidato = a.pk_id_candidato')
        ->join('tb_editais_candidatos e', 'e.fk_id_candidato = a.pk_id_candidato')
        ->join('tb_dados_contratos c','c.fk_id_candidato = a.pk_id_candidato AND c.ds_status = 1', 'left')// só contratos ativos
        ->where('a.fk_id_curso', $idCurso)
        ->where('e.fk_id_edital', $idEdital)
        ->where('c.fk_id_candidato IS NULL') // significa que não tem contrato ativo
        ->orderBy('a.ds_periodo', 'desc')
        ->orderBy('a.ds_ensino_medio', 'asc')
        ->orderBy('p.ds_nascimento', 'asc');
        return $builder->get()->getResult();

    }
    public function getContratosPorCandidato($idCandidato = null){
        $this->select('tb_dados_academicos.pk_id_candidato,tb_dados_pessoais.ds_nome,tb_dados_pessoais.ds_nascimento,ds_numero_termo,ds_ano_termo,tb_dados_pessoais.ds_nome_bairro,
        tb_dados_academicos.ds_periodo,tb_dados_contratos.ds_matricula,tb_editais.ds_numero_edital,tb_cursos.pk_id_curso,tb_cursos.ds_nome_curso, tb_setores.pk_id_setor,
        tb_setores.ds_nome_setor,tb_secretarias.ds_nome_secretaria,tb_secretarias.pk_id_secretaria,tb_dados_contratos.ds_turno,
        tb_dados_contratos.fk_id_auxilio,fk_id_seguro,tb_dados_contratos.pk_id_contrato, tb_dados_contratos.ds_data_ingresso, tb_dados_contratos.ds_data_encerramento,
        tb_dados_contratos.ds_status,ds_notificado,ds_prorrogado,tb_dados_contratos.fk_id_instituicao,
        ');
        $this->join('tb_dados_pessoais', 'tb_dados_pessoais.pk_id_candidato = tb_dados_contratos.fk_id_candidato');
        $this->join('tb_dados_academicos', 'tb_dados_academicos.pk_id_candidato = tb_dados_pessoais.pk_id_candidato');
        $this->join('tb_cursos', 'tb_cursos.pk_id_curso = tb_dados_academicos.fk_id_curso');
        //$this->join('tb_bairros', 'tb_bairros.pk_id_bairro = tb_dados_pessoais.fk_id_bairro');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = tb_dados_contratos.fk_id_edital');

        $this->join('tb_setores', 'tb_setores.pk_id_setor = tb_dados_contratos.fk_id_setor');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this->where('tb_dados_contratos.fk_id_candidato', $idCandidato);
        return $this->findAll();
    }
    public function getContratosPorCandidatoCurso($idCandidato = null,$idCurso = null){
        $this->select('tb_dados_academicos.pk_id_candidato,tb_dados_pessoais.ds_nome,tb_dados_pessoais.ds_nascimento,ds_numero_termo,ds_ano_termo,tb_dados_pessoais.ds_nome_bairro,
        tb_dados_academicos.ds_periodo,tb_dados_contratos.ds_matricula,tb_editais.ds_numero_edital,tb_cursos.pk_id_curso,tb_cursos.ds_nome_curso, tb_setores.pk_id_setor,
        tb_setores.ds_nome_setor,tb_secretarias.ds_nome_secretaria,tb_secretarias.pk_id_secretaria,tb_dados_contratos.ds_turno,
        tb_dados_contratos.fk_id_auxilio,fk_id_seguro,tb_dados_contratos.pk_id_contrato, tb_dados_contratos.ds_data_ingresso, tb_dados_contratos.ds_data_encerramento,
        tb_dados_contratos.ds_status,ds_notificado,ds_prorrogado,tb_dados_contratos.fk_id_instituicao,
        ');
        $this->join('tb_dados_pessoais', 'tb_dados_pessoais.pk_id_candidato = tb_dados_contratos.fk_id_candidato');
        $this->join('tb_dados_academicos', 'tb_dados_academicos.pk_id_candidato = tb_dados_pessoais.pk_id_candidato');
        $this->join('tb_cursos', 'tb_cursos.pk_id_curso = tb_dados_academicos.fk_id_curso');
        //$this->join('tb_bairros', 'tb_bairros.pk_id_bairro = tb_dados_pessoais.fk_id_bairro');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = tb_dados_contratos.fk_id_edital');

        $this->join('tb_setores', 'tb_setores.pk_id_setor = tb_dados_contratos.fk_id_setor');
        $this->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this->where('tb_dados_contratos.fk_id_candidato', $idCandidato);
        $this->where('tb_dados_contratos.fk_id_curso', $idCurso);
        return $this->findAll();
    }
}