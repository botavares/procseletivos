<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'tb_usuarios';
    protected $primaryKey = 'pk_id_usuario';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;
    protected bool $protectFields = true;
    protected $allowedFields = ['ds_nome_usuario','ds_email_usuario','ds_nick_usuario','ds_senha_usuario','ds_status_usuario'];

    protected $validationRules = [
        'ds_nome_usuario'   => 'required',
        'ds_nick_usuario'   => 'required|is_unique[tb_usuarios.ds_nick_usuario]',
        'ds_email_usuario'  => 'required|valid_email|is_unique[tb_usuarios.ds_email_usuario]',
        'ds_senha_usuario'  => 'required|min_length[6]|matches[ds_confirma_senha]',
        'ds_status_usuario' => 'required'
    ];

    protected bool $allowCallbacks = true;
    protected array $beforeInsert = ['hashPassword'];

    protected function hashPassword(array $password): array
    {
        $password['data']['ds_senha_usuario'] = password_hash($password['data']['ds_senha_usuario'], PASSWORD_DEFAULT);
        return $password;
    }

    /**
     * @deprecated Este método será removido. Utilize um Service para verificação de senha.
     */
    public function checkPassword($user, $password)
    {
        $buscarUsuario = $this->where('ds_nick_usuario', $user)->first();
        if (is_null($buscarUsuario)) {
            return false;
        }
        if (!password_verify($password, $buscarUsuario->ds_senha_usuario)) {
            return false;
        }
        return $buscarUsuario;
    }
}
