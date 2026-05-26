<?php
namespace App\Models;

use CodeIgniter\Model;

class AdequarModel extends Model{
    //Atributos
    protected $table = 'contratados_2025';
    protected $primaryKey = 'pk_id_contratados';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $allowedFields = [
        'fk_id_setor',
        'zds_cpf',
        'ds_cpf',
        'fk_id_candidato',
        'ds_nome',
        'fk_id_edital',
        'fk_id_curso',
        'ds_numero_termo',
        'ds_ano_termo',
        'ds_data_ingresso',
        'ds_data_encerramento',
        'ds_prorrogado',
        'ds_numero_aditivo',
        'ds_ano_aditivo'
    ];	

    protected $validationRules = [
       
    ];

    public function getMigracoes($cpf){
       $this->select('pk_id_cadastrado,
		migracao.ds_cpf,
        ds_rg,
        ds_emissorrg,
        ds_nome,
        ds_nascimento,
        ds_mae,
        ds_pne,
        ds_outrapne,
        ds_cep,
        ds_rua,
        ds_numero,
        ds_complemento,
        ds_bairro,
        ds_cidade,
        ds_estado,
        ds_telefoneresidencial,
        ds_telefonecelular,
        ds_email,
        ds_ensinomedio,
        migracao.fk_id_curso,
        migracao.fk_id_novo_curso,
        ds_instituicao,
        ds_faseperiodo,
        ds_catensino,
        ds_manha,
        ds_tarde,
        ds_noite,
        ds_datacadastro,
        ds_horacadastro');
        $this->join('migracao_processos', 'migracao_processos.fk_id_inscrito = migracao.pk_id_cadastrado');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = migracao_processos.fk_id_edital');
        $this->where('migracao.ds_cpf', $cpf);
        return $this->findAll();
    }


}