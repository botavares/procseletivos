<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_usuarios';
    protected $primaryKey       = 'pk_id_usuario';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ds_nome_usuario','ds_email_usuario','ds_nick_usuario','ds_senha_usuario','ds_status_usuario'];

    // Validation
    protected $validationRules      = [
        'ds_nome_usuario'   =>    'required',
        'ds_nick_usuario'   =>    'required|is_unique[tb_usuarios.ds_nick_usuario]',
        'ds_email_usuario'  =>    'required|valid_email|is_unique[tb_usuarios.ds_email_usuario]',
        'ds_senha_usuario'  =>    'required|min_length[6]|matches[ds_confirma_senha]',
        'ds_status_usuario' =>    'required'
    ];
    


    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [
        'hashPassword'
    ];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function hashPassword($password){
        $password['data']['ds_senha_usuario'] = password_hash($password['data']['ds_senha_usuario'], PASSWORD_DEFAULT);
        return $password;
    }
    public function checkPassword($user,$password){
        $buscarUsuario = $this->where('ds_nick_usuario', $user)->first();
        if(is_null($buscarUsuario)){
            return false;
        }
        if(!password_verify($password, $buscarUsuario->ds_senha_usuario)){
            return false;
        }
        return $buscarUsuario;
    }
}
