<?php
namespace App\Models;

use CodeIgniter\Model;

namespace App\Models;

use CodeIgniter\Model;

class ArquivoModel extends Model
{
    protected $table = 'tb_arquivos_manifestos';
    protected $primaryKey = 'pk_id_postado';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['ds_protocolo', 'ds_nome_arquivo'];
}