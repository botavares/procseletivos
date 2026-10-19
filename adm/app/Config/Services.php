<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use App\Helpers\MyHelper_helper;

class Services extends BaseService
{
    /* ================================================================
     * FACTORIES DE MODELS (Injeção de Dependência)
     * ================================================================ */

    public static function cargosModel(bool $getShared = true): \App\Models\CargosModel
    {
        return $getShared ? static::getSharedInstance('cargosModel') : new \App\Models\CargosModel();
    }

    public static function editaisModel(bool $getShared = true): \App\Models\EditaisModel
    {
        return $getShared ? static::getSharedInstance('editaisModel') : new \App\Models\EditaisModel();
    }

    public static function editaisCargosModel(bool $getShared = true): \App\Models\EditaisCargosModel
    {
        return $getShared ? static::getSharedInstance('editaisCargosModel') : new \App\Models\EditaisCargosModel();
    }

    public static function editaisAbrangenciasModel(bool $getShared = true): \App\Models\EditaisAbrangenciasModel
    {
        return $getShared ? static::getSharedInstance('editaisAbrangenciasModel') : new \App\Models\EditaisAbrangenciasModel();
    }

    /* ================================================================
     * FACTORIES DE SERVICES (Injeção de Dependência)
     * ================================================================ */

    public static function editalGridService(bool $getShared = true): \App\Services\Editais\EditalGridService
    {
        return $getShared ? static::getSharedInstance('editalGridService') : new \App\Services\Editais\EditalGridService();
    }

    public static function editalFormService(bool $getShared = true): \App\Services\Editais\EditalFormService
    {
        return $getShared ? static::getSharedInstance('editalFormService') : new \App\Services\Editais\EditalFormService();
    }

    public static function editalService(bool $getShared = true): \App\Services\Editais\EditalService
    {
        return $getShared
            ? static::getSharedInstance('editalService')
            : new \App\Services\Editais\EditalService(service('editalRepository'));
    }

    public static function editalUploadService(bool $getShared = true): \App\Services\Editais\EditalUploadService
    {
        return $getShared ? static::getSharedInstance('editalUploadService') : new \App\Services\Editais\EditalUploadService();
    }

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

    public static function escolaridadeGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('escolaridadeGridService') : new \App\Services\Escolaridades\EscolaridadeGridService();
    }

    public static function escolaridadeService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('escolaridadeService') : new \App\Services\Escolaridades\EscolaridadeService();
    }

    public static function experienciaGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('experienciaGridService') : new \App\Services\Experiencias\ExperienciaGridService();
    }

    public static function experienciaService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('experienciaService') : new \App\Services\Experiencias\ExperienciaService();
    }

    public static function aperfeicoamentoGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('aperfeicoamentoGridService') : new \App\Services\Aperfeicoamentos\AperfeicoamentoGridService();
    }

    public static function aperfeicoamentoService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('aperfeicoamentoService') : new \App\Services\Aperfeicoamentos\AperfeicoamentoService();
    }

    /* ================================================================
     * FACTORIES DE EXPORTAÇÃO
     * ================================================================ */

    public static function excelExportService(bool $getShared = true): \App\Services\Export\ExcelExportService
    {
        return $getShared ? static::getSharedInstance('excelExportService') : new \App\Services\Export\ExcelExportService();
    }

    public static function pdfExportService(bool $getShared = true): \App\Services\Export\PdfExportService
    {
        return $getShared ? static::getSharedInstance('pdfExportService') : new \App\Services\Export\PdfExportService();
    }

    public static function qrCodeService(bool $getShared = true): \App\Services\Export\QrCodeService
    {
        return $getShared ? static::getSharedInstance('qrCodeService') : new \App\Services\Export\QrCodeService();
    }

    /* ================================================================
     * FACTORIES DE REPOSITORIES (Injeção de Dependência)
     * ================================================================ */

    public static function candidatoRepository(bool $getShared = true): \App\Repositories\Interfaces\ICandidatoRepository
    {
        return $getShared
            ? static::getSharedInstance('candidatoRepository')
            : new \App\Repositories\CandidatoRepository(service('candidatosModel'));
    }

    public static function editalRepository(bool $getShared = true): \App\Repositories\Interfaces\IEditalRepository
    {
        return $getShared
            ? static::getSharedInstance('editalRepository')
            : new \App\Repositories\EditalRepository(service('editaisModel'));
    }

    public static function classificacaoRepository(bool $getShared = true): \App\Repositories\Interfaces\IClassificacaoRepository
    {
        return $getShared
            ? static::getSharedInstance('classificacaoRepository')
            : new \App\Repositories\ClassificacaoRepository(service('classificacaoModel'));
    }

    public static function contratoRepository(bool $getShared = true): \App\Repositories\Interfaces\IContratoRepository
    {
        return $getShared
            ? static::getSharedInstance('contratoRepository')
            : new \App\Repositories\ContratoRepository(service('dadosContratosModel'));
    }

    public static function criterioAdicionalGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('criterioAdicionalGridService') : new \App\Services\CriteriosAdicionais\CriterioAdicionalGridService();
    }

    public static function criterioAdicionalService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('criterioAdicionalService') : new \App\Services\CriteriosAdicionais\CriterioAdicionalService();
    }

    public static function secretariasModel(bool $getShared = true): \App\Models\SecretariasModel
    {
        return $getShared ? static::getSharedInstance('secretariasModel') : new \App\Models\SecretariasModel();
    }

    public static function setoresModel(bool $getShared = true): \App\Models\SetoresModel
    {
        return $getShared ? static::getSharedInstance('setoresModel') : new \App\Models\SetoresModel();
    }

    public static function vagasModel(bool $getShared = true): \App\Models\VagasModel
    {
        return $getShared ? static::getSharedInstance('vagasModel') : new \App\Models\VagasModel();
    }

    public static function cursosModel(bool $getShared = true): \App\Models\CursosModel
    {
        return $getShared ? static::getSharedInstance('cursosModel') : new \App\Models\CursosModel();
    }

    public static function cargoService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('cargoService') : new \App\Services\Cargos\CargoService();
    }

    public static function candidatosService(bool $getShared = true)
    {
        return $getShared
            ? static::getSharedInstance('candidatosService')
            : new \App\Services\Candidatos\CandidatosService(service('candidatoRepository'));
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
    public static function cargosCriteriosFormService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargosCriteriosFormService');
        }

        return new \App\Services\Cargos\CargosCriteriosFormService();
    }
    public static function cargosCriteriosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosCriteriosService') : new \App\Services\Cargos\CargosCriteriosService();
    }

    /* ================================================================
     * FACTORIES DE MODELS RESTANTES
     * ================================================================ */

    public static function criteriosAdicionaisModel(bool $getShared = true): \App\Models\CriteriosAdicionaisModel
    {
        return $getShared ? static::getSharedInstance('criteriosAdicionaisModel') : new \App\Models\CriteriosAdicionaisModel();
    }

    public static function cargosCriteriosAdicionaisModel(bool $getShared = true): \App\Models\CargosCriteriosAdicionaisModel
    {
        return $getShared ? static::getSharedInstance('cargosCriteriosAdicionaisModel') : new \App\Models\CargosCriteriosAdicionaisModel();
    }

    public static function cargosCursosModel(bool $getShared = true): \App\Models\CargosCursosModel
    {
        return $getShared ? static::getSharedInstance('cargosCursosModel') : new \App\Models\CargosCursosModel();
    }

    public static function escolaridadesModel(bool $getShared = true): \App\Models\EscolaridadesModel
    {
        return $getShared ? static::getSharedInstance('escolaridadesModel') : new \App\Models\EscolaridadesModel();
    }

    public static function cargosEscolaridadesModel(bool $getShared = true): \App\Models\CargosEscolaridadesModel
    {
        return $getShared ? static::getSharedInstance('cargosEscolaridadesModel') : new \App\Models\CargosEscolaridadesModel();
    }

    public static function experienciasModel(bool $getShared = true): \App\Models\ExperienciasModel
    {
        return $getShared ? static::getSharedInstance('experienciasModel') : new \App\Models\ExperienciasModel();
    }

    public static function cargosExperienciasModel(bool $getShared = true): \App\Models\CargosExperienciasModel
    {
        return $getShared ? static::getSharedInstance('cargosExperienciasModel') : new \App\Models\CargosExperienciasModel();
    }

    public static function classificacaoModel(bool $getShared = true): \App\Models\ClassificacaoModel
    {
        return $getShared ? static::getSharedInstance('classificacaoModel') : new \App\Models\ClassificacaoModel();
    }

    public static function dadosContratosModel(bool $getShared = true): \App\Models\DadosContratosModel
    {
        return $getShared ? static::getSharedInstance('dadosContratosModel') : new \App\Models\DadosContratosModel();
    }

    public static function dadosRescisaoModel(bool $getShared = true): \App\Models\DadosRescisaoModel
    {
        return $getShared ? static::getSharedInstance('dadosRescisaoModel') : new \App\Models\DadosRescisaoModel();
    }

    public static function dadosAditivosModel(bool $getShared = true): \App\Models\DadosAditivosModel
    {
        return $getShared ? static::getSharedInstance('dadosAditivosModel') : new \App\Models\DadosAditivosModel();
    }

    public static function convocadosModel(bool $getShared = true): \App\Models\ConvocadosModel
    {
        return $getShared ? static::getSharedInstance('convocadosModel') : new \App\Models\ConvocadosModel();
    }

    public static function verificadorModel(bool $getShared = true): \App\Models\VerificadorModel
    {
        return $getShared ? static::getSharedInstance('verificadorModel') : new \App\Models\VerificadorModel();
    }

    public static function contratoService(bool $getShared = true): \App\Services\Contratos\ContratoService
    {
        return $getShared
            ? static::getSharedInstance('contratoService')
            : new \App\Services\Contratos\ContratoService(
                service('contratoRepository'),
                service('vagasModel'),
                service('logsService'),
                service('dadosRescisaoModel'),
                service('dadosAditivosModel'),
                service('convocadosModel'),
                service('verificadorModel')
            );
    }

    public static function cadastrosExperienciasModel(bool $getShared = true): \App\Models\CadastrosExperienciasModel
    {
        return $getShared ? static::getSharedInstance('cadastrosExperienciasModel') : new \App\Models\CadastrosExperienciasModel();
    }

    public static function cadastrosEscolaridadesModel(bool $getShared = true): \App\Models\CadastrosEscolaridadesModel
    {
        return $getShared ? static::getSharedInstance('cadastrosEscolaridadesModel') : new \App\Models\CadastrosEscolaridadesModel();
    }

    public static function cadastrosAperfeicoamentosModel(bool $getShared = true): \App\Models\CadastrosAperfeicoamentosModel
    {
        return $getShared ? static::getSharedInstance('cadastrosAperfeicoamentosModel') : new \App\Models\CadastrosAperfeicoamentosModel();
    }

    public static function protocolosModel(bool $getShared = true): \App\Models\ProtocolosModel
    {
        return $getShared ? static::getSharedInstance('protocolosModel') : new \App\Models\ProtocolosModel();
    }

    public static function academicosModel(bool $getShared = true): \App\Models\AcademicosModel
    {
        return $getShared ? static::getSharedInstance('academicosModel') : new \App\Models\AcademicosModel();
    }

    public static function abrangenciasModel(bool $getShared = true): \App\Models\EditaisAbrangenciasModel
    {
        return $getShared ? static::getSharedInstance('abrangenciasModel') : new \App\Models\EditaisAbrangenciasModel();
    }

    public static function cargosCursosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosCursosService') : new \App\Services\Cargos\CargosCursosService();
    }

    public static function cargosEscolaridadesService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosEscolaridadesService') : new \App\Services\Cargos\CargosEscolaridadesService();
    }

    public static function cargosExperienciasService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosExperienciasService') : new \App\Services\Cargos\CargosExperienciasService();
    }

    public static function recursosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('recursosService') : new \App\Services\Candidatos\RecursosService();
    }

    public static function recursosConsultaService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('recursosConsultaService') : new \App\Services\Candidatos\RecursosConsultaService();
    }

    public static function logsService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('logsService') : new \App\Services\LogsService();
    }

    public static function relatoriosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('relatoriosService') : new \App\Services\Relatorios\RelatoriosService(
            service('candidatosModel'),
            service('academicosModel'),
            service('cursosModel'),
            service('editaisAbrangenciasModel')
        );
    }

    public static function classificacaoService(bool $getShared = true){
        return $getShared
            ? static::getSharedInstance('classificacaoService')
            : new \App\Services\Classificacao\ClassificacaoService(service('classificacaoRepository'));
    }

    public static function desempateConfigService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('desempateConfigService') : new \App\Services\Classificacao\DesempateConfigService();
    }

    public static function classificacaoColunasService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('classificacaoColunasService') : new \App\Services\Classificacao\ClassificacaoColunasService();
    }
}
