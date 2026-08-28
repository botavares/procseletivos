<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */
    
    
    /**
     * Servico de Gerenciamento de Candidatos
     * @param bool $getShared
     * @return \App\Services\CandidatoService
     */
    public static function candidatoService(bool $getShared = true): \App\Services\CandidatoService{
        if ($getShared) {
            return static::getSharedInstance('candidatoService');
        }
        return new \App\Services\CandidatoService();
    }


     /**
     * Serviço de Gerenciamento de Sessão
     *
     * @param bool $getShared
     * @return \App\Services\Base\SessaoService
     */
    public static function sessao(bool $getShared = true): \App\Services\Base\SessaoService{
        if ($getShared) {
            return static::getSharedInstance('sessao');
        }
        return new \App\Services\Base\SessaoService();
    }
    
    /**
     * Servico de gerenciamento de dados Classificatorios
     * @param bool $getShared
     * @return \App\Services\ClassificatoriosService
     */
    public static function classificatorioService(bool $getShared = true): \App\Services\ClassificatorioService{
        if ($getShared) {
            return static::getSharedInstance('classificatorioService');
        }
        return new \App\Services\ClassificatorioService();
    }
 
   
    /**
     * Serviço de Gerenciamento de Editais
     *
     * @param bool $getShared
     * @return \App\Services\EditaisService
     */
    public static function editaisService(bool $getShared = true): \App\Services\EditaisService{
        if ($getShared) {
            return static::getSharedInstance('editaisService');
        }
        return new \App\Services\EditaisService();
    }

    /**
     * Serviço de Gerenciamento de Protocolos
     *
     * @param bool $getShared
     * @return \App\Services\ProtocoloService
     */
    public static function protocoloService(bool $getShared = true): \App\Services\ProtocoloService{
        if ($getShared) {
            return static::getSharedInstance('protocoloService');
        }
        return new \App\Services\ProtocoloService();
    }

    /**
     * Serviço de Gerenciamento de PDF
     *
     * @param bool $getShared
     * @return \App\Services\PdfService
     */
    public static function pdfService(bool $getShared = true): \App\Services\PdfService{
        if ($getShared) {
            return static::getSharedInstance('pdfService');
        }
        return new \App\Services\PdfService();
    }

    /**
     * Serviço de Gerenciamento de GovBr
     *
     * @param bool $getShared
     * @return \App\Services\GovBrService
     */
    public static function govBrService(bool $getShared = true): \App\Services\GovBrService{
        if ($getShared) {
            return static::getSharedInstance('govBrService');
        }
        return new \App\Services\GovBrService();
    }
    /**
     * Serviço de Gerenciamento de Comprovantes
     *
     * @param bool $getShared
     * @return \App\Services\ComprovanteService
     */
    public static function comprovanteService(bool $getShared = true): \App\Services\ComprovanteService{
        if ($getShared) {
            return static::getSharedInstance('comprovanteService');
        }
        return new \App\Services\ComprovanteService();
    }
}
