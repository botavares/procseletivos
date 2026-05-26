<?php
namespace App\Models;

use CodeIgniter\Model;

class MigradorModel extends Model{
    //Atributos
    protected $table = 'migracao';
    protected $primaryKey = 'pk_id_cadastrado';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $allowedFields = [
        'pk_id_cadastrado',
        'ds_cpf',
        'ds_rg',
        'ds_emissorrg',
        'ds_nome',
        'ds_nascimento',
        'ds_mae',
        'ds_pne',
        'ds_outrapne',
        'ds_cep',
        'ds_rua',
        'ds_numero',
        'ds_complemento',
        'ds_bairro',
        'ds_cidade',
        'ds_estado',
        'ds_telefoneresidencial',
        'ds_telefonecelular',
        'ds_email',
        'ds_ensinomedio',
        'fk_id_curso',
        'fk_id_novo_curso',
        'ds_instituicao',
        'ds_faseperiodo',
        'ds_catensino',
        'ds_manha',
        'ds_tarde',
        'ds_noite',
        'ds_datacadastro',
        'ds_horacadastro',

    ];	

    protected $validationRules = [
        'ds_nome' => 'required',
        'ds_cpf' => 'required',
        'ds_nascimento' => 'required',
        'ds_nome_mae' => 'required',
        'fk_id_pne' => 'required',
        'ds_cep' => 'required',
        'ds_rua' => 'required',
        'ds_numero' => 'required',
        'fk_id_bairro' => 'required',
        'ds_celular' => 'required',
        'ds_email' => 'required',
    ];

    public function getMigracoes(){
       $this->select('pk_id_cadastrado,
		migracao.ds_cpf as ds_cpf,
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
        fk_id_curso,
        ds_instituicao,
        ds_faseperiodo,
        ds_catensino,
        ds_manha,
        ds_tarde,
        ds_noite,
        ds_datacadastro,
        ds_horacadastro,
        migracao_processos.fk_id_edital');
        $this->join('migracao_processos', 'migracao_processos.fk_id_inscrito = migracao.pk_id_cadastrado');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = migracao_processos.fk_id_edital');
        return $this->findAll();
    }


}