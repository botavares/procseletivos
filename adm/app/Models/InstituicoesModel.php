<?php
namespace App\Models;

use CodeIgniter\Model;

class InstituicoesModel extends Model{
    //Atributos
    protected $table = 'tb_instituicoes';
    protected $primaryKey = 'pk_id_instituicao';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_nome','ds_cnpj','ds_rua','ds_numero','ds_complemento','ds_bairro','ds_cidade','ds_estado','ds_cep','ds_email','ds_telefone','ds_numero_convenio'
    ];

    protected $validationRules = [
        'ds_nome' => 'required',
        'ds_cnpj' => 'required',
        'ds_rua' => 'required',
        'ds_numero' => 'required',
        'ds_bairro' => 'required',
        'ds_cidade' => 'required',
        'ds_estado' => 'required',
        'ds_cep' => 'required',
        'ds_email' => 'required',
        'ds_telefone' => 'required'
        
    ];

    protected $returnType = 'object';

}