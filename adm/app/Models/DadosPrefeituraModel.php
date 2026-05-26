<?php
namespace App\Models;

use CodeIgniter\Model;

class DadosPrefeituraModel extends Model{
    //Atributos
    protected $table = 'tb_prefeitura';
    protected $primaryKey = 'ds_nome_prefeitura';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'ds_nome_prefeitura',
        'ds_rua',
        'ds_numero',
        'ds_bairro',
        'ds_cep',
        'ds_municipio',
        'ds_estado',
        'ds_telefone',
        'ds_cnpj',
        'ds_diretor_rh'
    ];

    protected $returnType = 'object';

}