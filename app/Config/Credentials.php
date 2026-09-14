<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Arquivo de configuração para credenciais sensíveis do aplicativo.
 * 
 * ATENÇÃO: Este arquivo contém dados sensíveis. Nunca o versione
 * no Git com valores reais de produção.
 */
class Credentials extends BaseConfig
{
    /**
     * Credenciais Gov.BR (App Prefeitura Divinópolis)
     */
    public string $govbrAppKey    = '0b7f390e92176b48bdd12a6488dcd547';
    public string $govbrAppSecret = '04a3ae30dba5b02989d10cb58cd2a9e9';
}
