<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use App\Helpers\MyHelper_helper;

class Services extends BaseService
{
    public static function MyHelper_helper($getShared = true){
        if($getShared){
            return static::getSharedInstance('MyHelper_helper');
        }
        return new MyHelper_helper();
    }
    
    public static function situacaoService($getShared = true){
        if ($getShared) {
            return static::getSharedInstance('situacaoService');
        }

        return new \App\Services\Candidatos\SituacaoService(
                new \App\Models\SituacaoModel()
        );
    }

    public static function cargoService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargoService');
        }

        return new \App\Services\Cargos\CargoService();
    }
}
