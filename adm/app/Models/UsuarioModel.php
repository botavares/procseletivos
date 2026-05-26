<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_users';
    protected $primaryKey       = 'pk_id_user';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ds_nome_user','ds_nick_user','ds_perfil_user','ds_email_user','ds_senha_user','ds_status_user','ds_administrador','ds_visualiza_recursos','ds_responde_recursos'];
    
    // Validation
    protected $validationRules      = [
        'ds_nome_user'                  =>'required',
        'ds_nick_user'                  =>'required|is_unique[tb_users.ds_nick_user]',
        'ds_email_user'                 =>'required|valid_email|is_unique[tb_users.ds_email_user]',
        'ds_senha_user'                 =>'required|min_length[6]|matches[ds_confirma_senha]',
        'ds_status_user'                =>'required',
        'ds_perfil_user'                =>'required',
        'ds_administrador'              =>'required',
        'ds_confirma_senha'             =>'required|matches[ds_senha_user]'
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
    protected $beforeUpdate   = [
        'hashPassword'
    ];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function hashPassword($password){
        $password['data']['ds_senha_user'] = password_hash($password['data']['ds_senha_user'], PASSWORD_DEFAULT);
        return $password;
    }

    public function checkPassword($user,$password){
        $buscarUsuario = $this->where('ds_nick_user', $user)->first();
        if(is_null($buscarUsuario)){
            return false;
        }
        
        if(!password_verify($password, $buscarUsuario->ds_senha_user)){
            return false;
        }
        return $buscarUsuario;
    }
    public function atualizarSenha($id, $senhaNova){
        $this->skipValidation(true);
        return $this->update($id, [
            'ds_senha_user' => $senhaNova
        ]);
    }

    


}
