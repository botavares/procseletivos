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
    public static function cargoFormService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargoFormService');
        }

        return new \App\Services\Cargos\CargoFormService();
    }
    public static function cargosCursosFormService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargosCursosFormService');
        }

        return new \App\Services\Cargos\CargosCursosFormService();
    }
    public static function cargosCursosService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargosCursosService');
        }

        return new \App\Services\Cargos\CargosCursosService();
    }
    public static function cargosCriteriosFormService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargosCriteriosFormService');
        }

        return new \App\Services\Cargos\CargosCriteriosFormService();
    }
    public static function cargosCriteriosService($getShared = true){
        if ($getShared) {
            return static::getSharedInstance('cargosCriteriosService');
        }

        return new \App\Services\Cargos\CargosCriteriosService();
    }
}
