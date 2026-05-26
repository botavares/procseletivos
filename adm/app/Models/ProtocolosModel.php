<?php
namespace App\Models;

use CodeIgniter\Model;

class ProtocolosModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_protocolo';
    protected $primaryKey = 'pk_id_processo';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'fk_id_cadastrado','fk_id_secretaria','fk_id_edital','fk_id_cargo','ds_cpf_cadastrado','ds_protocolo','ds_data_protocolo','ds_hora_protocolo','ds_ip_protocolo'
    ];

    protected $validationRules = [
        'fk_id_cadastrado'  => 'required|integer',
        'fk_id_secretaria'  => 'required|integer',
        'fk_id_edital'      => 'required|integer',
        'fk_id_cargo'       => 'required|integer',
        'ds_cpf_cadastrado' => 'required',
        'ds_protocolo'      => 'required',
        'ds_data_protocolo' => 'required',
        'ds_hora_protocolo' => 'required',
        //'ds_ip_protocolo'   => 'required',
        
    ];

    protected $returnType = 'object';

    public function protocolosdoCandidato($campo, $valor){
        $this->select('tb_cadastrados_protocolo.*, tb_editais.ds_numero_edital, tb_cargos.ds_nome_cargo');
        $this->join('tb_editais', 'tb_editais.pk_id_edital = tb_cadastrados_protocolo.fk_id_edital');
        $this->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_cadastrados_protocolo.fk_id_cargo');
        return $this->where($campo, $valor)->findAll();
    }
    public function protocolosCadastradosEdital($secretaria, $edital, $cargo){
        $this->select('tb_cadastrados_protocolo.*,tb_cadastrados.*');
        $this->join('tb_cadastrados', 'tb_cadastrados.pk_id_cadastrado = tb_cadastrados_protocolo.fk_id_cadastrado');
        return $this->where([
            'tb_cadastrados_protocolo.fk_id_secretaria' => $secretaria,
            'tb_cadastrados_protocolo.fk_id_edital'     => $edital,
            'tb_cadastrados_protocolo.fk_id_cargo'      => $cargo,
        ])->findAll();
    }
    

}