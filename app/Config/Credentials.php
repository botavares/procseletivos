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
     * ATENÇÃO: Nunca versione valores reais. Use .env
     */
    public string $govbrAppKey    = '';
    public string $govbrAppSecret = '';

    public function __construct()
    {
        parent::__construct();
        $this->govbrAppKey    = env('GOVBR_APP_KEY', '');
        $this->govbrAppSecret = env('GOVBR_APP_SECRET', '');
    }
}
