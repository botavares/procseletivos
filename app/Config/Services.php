<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 */
class Services extends BaseService
{
    // -------------------------------------------------------------------------
    // Services de Domínio
    // -------------------------------------------------------------------------

    public static function candidatoService(bool $getShared = true): \App\Services\CandidatoService
    {
        if ($getShared) {
            return static::getSharedInstance('candidatoService');
        }

        return new \App\Services\CandidatoService(
            new \App\Models\CandidatosModel(),
            new \App\Models\DeficienciasModel()
        );
    }

    public static function sessao(bool $getShared = true): \App\Services\Base\SessaoService
    {
        if ($getShared) {
            return static::getSharedInstance('sessao');
        }

        return new \App\Services\Base\SessaoService();
    }

    public static function classificatorioService(bool $getShared = true): \App\Services\ClassificatorioService
    {
        if ($getShared) {
            return static::getSharedInstance('classificatorioService');
        }

        return new \App\Services\ClassificatorioService(
            new \App\Models\CargosModel(),
            service('experienciaService', false),
            service('escolaridadeService', false),
            service('aperfeicoamentoService', false),
            service('criterioAdicionalService', false),
            \Config\Database::connect(),
        );
    }

    public static function experienciaService(bool $getShared = true): \App\Services\ExperienciaService
    {
        if ($getShared) {
            return static::getSharedInstance('experienciaService');
        }

        return new \App\Services\ExperienciaService(
            new \App\Models\ExperienciasModel(),
            new \App\Models\CadastradosExperienciasModel(),
            new \App\Models\CargosExperienciasModel(),
        );
    }

    public static function escolaridadeService(bool $getShared = true): \App\Services\EscolaridadeService
    {
        if ($getShared) {
            return static::getSharedInstance('escolaridadeService');
        }

        return new \App\Services\EscolaridadeService(
            new \App\Models\EscolaridadesModel(),
            new \App\Models\CadastradosEscolaridadesModel(),
            new \App\Models\CargosEscolaridadesModel(),
        );
    }

    public static function aperfeicoamentoService(bool $getShared = true): \App\Services\AperfeicoamentoService
    {
        if ($getShared) {
            return static::getSharedInstance('aperfeicoamentoService');
        }

        return new \App\Services\AperfeicoamentoService(
            new \App\Models\AperfeicoamentosModel(),
            new \App\Models\CadastradosAperfeicoamentosModel(),
            new \App\Models\CargosAperfeicoamentosModel(),
        );
    }

    public static function criterioAdicionalService(bool $getShared = true): \App\Services\CriterioAdicionalService
    {
        if ($getShared) {
            return static::getSharedInstance('criterioAdicionalService');
        }

        return new \App\Services\CriterioAdicionalService(
            new \App\Models\CriteriosAdicionaisModel(),
            new \App\Models\CadastradosCriteriosAdicionaisModel(),
            new \App\Models\CargosCriteriosAdicionaisModel(),
        );
    }

    public static function editaisService(bool $getShared = true): \App\Services\EditaisService
    {
        if ($getShared) {
            return static::getSharedInstance('editaisService');
        }

        return new \App\Services\EditaisService(
            new \App\Models\EditaisModel(),
            new \App\Models\CargosModel(),
            service('editalRepository', false),
        );
    }

    public static function protocoloService(bool $getShared = true): \App\Services\ProtocoloService
    {
        if ($getShared) {
            return static::getSharedInstance('protocoloService');
        }

        return new \App\Services\ProtocoloService(
            new \App\Models\ProtocolosModel(),
            new \App\Models\CandidatosModel(),
            new \App\Models\CargosModel(),
            service('protocoloRepository', false),
        );
    }

    public static function pdfService(bool $getShared = true): \App\Services\PdfService
    {
        if ($getShared) {
            return static::getSharedInstance('pdfService');
        }

        return new \App\Services\PdfService();
    }

    public static function govBrService(bool $getShared = true): \App\Services\GovBrService
    {
        if ($getShared) {
            return static::getSharedInstance('govBrService');
        }

        return new \App\Services\GovBrService();
    }

    public static function comprovanteService(bool $getShared = true): \App\Services\ComprovanteService
    {
        if ($getShared) {
            return static::getSharedInstance('comprovanteService');
        }

        return new \App\Services\ComprovanteService(
            new \App\Models\CandidatosModel(),
            new \App\Models\CargosModel(),
            new \App\Models\ProtocolosModel(),
            new \App\Models\EditaisModel(),
            service('pdfService', false),
        );
    }

    public static function candidatoRecursoService(bool $getShared = true): \App\Services\CandidatoRecursoService
    {
        if ($getShared) {
            return static::getSharedInstance('candidatoRecursoService');
        }

        return new \App\Services\CandidatoRecursoService(
            service('recursoRepository', false),
        );
    }

    public static function transparenciaService(bool $getShared = true): \App\Services\Transparencia\TransparenciaService
    {
        if ($getShared) {
            return static::getSharedInstance('transparenciaService');
        }

        return new \App\Services\Transparencia\TransparenciaService(
            service('transparenciaRepository', false),
            service('transparenciaPresenter', false),
            service('desempateConfigService', false),
        );
    }

    public static function desempateConfigService(bool $getShared = true): \App\Services\Classificacao\DesempateConfigService
    {
        if ($getShared) {
            return static::getSharedInstance('desempateConfigService');
        }

        return new \App\Services\Classificacao\DesempateConfigService(
            new \App\Models\CargosDesempateConfigModel(),
        );
    }

    public static function homeService(bool $getShared = true): \App\Services\HomeService
    {
        if ($getShared) {
            return static::getSharedInstance('homeService');
        }

        return new \App\Services\HomeService(
            new \App\Models\CandidatosModel(),
            new \App\Models\EditaisModel(),
            new \App\Models\EditaisCandidatosModel(),
            service('sessao', false),
        );
    }

    public static function perguntaFrequenteService(bool $getShared = true): \App\Services\PerguntaFrequenteService
    {
        if ($getShared) {
            return static::getSharedInstance('perguntaFrequenteService');
        }

        return new \App\Services\PerguntaFrequenteService(
            new \App\Models\PerguntasFrequentesModel(),
        );
    }

    public static function setorService(bool $getShared = true): \App\Services\SetorService
    {
        if ($getShared) {
            return static::getSharedInstance('setorService');
        }

        return new \App\Services\SetorService(
            new \App\Models\SetoresModel(),
        );
    }

    public static function servicoService(bool $getShared = true): \App\Services\ServicoService
    {
        if ($getShared) {
            return static::getSharedInstance('servicoService');
        }

        return new \App\Services\ServicoService();
    }

    public static function transparenciaFormService(bool $getShared = true): \App\Services\Transparencia\TransparenciaFormService
    {
        if ($getShared) {
            return static::getSharedInstance('transparenciaFormService');
        }

        return new \App\Services\Transparencia\TransparenciaFormService(
            \Config\Services::incomingrequest(),
            new \App\Models\CargosModel(),
            new \App\Models\EditaisModel(),
        );
    }
    public static function cepService(bool $getShared = true): \App\Services\Endereco\CepService
    {
        if ($getShared) {
            return static::getSharedInstance('cepService');
        }

        return new \App\Services\Endereco\CepService();
    }

    public static function verificarContratosService(bool $getShared = true): \App\Services\VerificarContratosService
    {
        if ($getShared) {
            return static::getSharedInstance('verificarContratosService');
        }

        return new \App\Services\VerificarContratosService(
            new \App\Models\VerificadorModel(),
            new \App\Models\DadosContratosModel(),
            new \App\Models\CandidatosModel(),
            new \App\Models\AcademicosModel(),
        );
    }

    public static function ordenacaoDinamicaService(bool $getShared = true): \App\Services\Classificacao\OrdenacaoDinamicaService
    {
        if ($getShared) {
            return static::getSharedInstance('ordenacaoDinamicaService');
        }

        return new \App\Services\Classificacao\OrdenacaoDinamicaService();
    }

    public static function candidatoScoreBuilderService(bool $getShared = true): \App\Services\Classificacao\CandidatoScoreBuilderService
    {
        if ($getShared) {
            return static::getSharedInstance('candidatoScoreBuilderService');
        }

        return new \App\Services\Classificacao\CandidatoScoreBuilderService(
            \Config\Database::connect(),
        );
    }

    public static function transparenciaPresenter(bool $getShared = true): \App\Presenters\TransparenciaPresenter
    {
        if ($getShared) {
            return static::getSharedInstance('transparenciaPresenter');
        }

        return new \App\Presenters\TransparenciaPresenter(
            service('desempateConfigService', false),
        );
    }

    public static function vinculoCandidatoService(bool $getShared = true): \App\Services\VinculoCandidatoService
    {
        if ($getShared) {
            return static::getSharedInstance('vinculoCandidatoService');
        }

        return new \App\Services\VinculoCandidatoService(
            new \App\Models\AcademicosModel(),
            new \App\Models\CursosModel(),
            new \App\Models\EditaisCandidatosModel(),
            new \App\Models\EditaisCursosModel(),
        );
    }

    public static function dadosContratadoService(bool $getShared = true): \App\Services\DadosContratadoService
    {
        if ($getShared) {
            return static::getSharedInstance('dadosContratadoService');
        }

        return new \App\Services\DadosContratadoService(
            new \App\Models\CandidatosModel(),
            new \App\Models\SetoresModel(),
            new \App\Models\FeriasModel(),
            new \App\Models\DadosContratosModel(),
            service('contratoRepository', false),
        );
    }

    // -------------------------------------------------------------------------
    // Repositories
    // -------------------------------------------------------------------------

    public static function candidatoRepository(bool $getShared = true): \App\Repositories\CandidatoRepository
    {
        if ($getShared) {
            return static::getSharedInstance('candidatoRepository');
        }

        return new \App\Repositories\CandidatoRepository(
            new \App\Models\CandidatosModel()
        );
    }

    public static function contratoRepository(bool $getShared = true): \App\Repositories\ContratoRepository
    {
        if ($getShared) {
            return static::getSharedInstance('contratoRepository');
        }

        return new \App\Repositories\ContratoRepository(
            new \App\Models\DadosContratosModel()
        );
    }

    public static function convocadoRepository(bool $getShared = true): \App\Repositories\ConvocadoRepository
    {
        if ($getShared) {
            return static::getSharedInstance('convocadoRepository');
        }

        return new \App\Repositories\ConvocadoRepository(
            new \App\Models\ConvocadosModel()
        );
    }

    public static function editalRepository(bool $getShared = true): \App\Repositories\EditalRepository
    {
        if ($getShared) {
            return static::getSharedInstance('editalRepository');
        }

        return new \App\Repositories\EditalRepository(
            new \App\Models\EditaisCargosModel(),
            new \App\Models\EditaisCandidatosModel(),
            new \App\Models\EditaisModel()
        );
    }

    public static function protocoloRepository(bool $getShared = true): \App\Repositories\ProtocoloRepository
    {
        if ($getShared) {
            return static::getSharedInstance('protocoloRepository');
        }

        return new \App\Repositories\ProtocoloRepository(
            new \App\Models\ProtocolosModel()
        );
    }

    public static function recursoRepository(bool $getShared = true): \App\Repositories\RecursoRepository
    {
        if ($getShared) {
            return static::getSharedInstance('recursoRepository');
        }

        return new \App\Repositories\RecursoRepository(
            \Config\Database::connect()
        );
    }

    public static function transparenciaRepository(bool $getShared = true): \App\Repositories\TransparenciaRepository
    {
        if ($getShared) {
            return static::getSharedInstance('transparenciaRepository');
        }

        return new \App\Repositories\TransparenciaRepository(
            \Config\Database::connect()
        );
    }


    /* ================================================================
     * SERVICES DO ADMIN (migrados de adm/app/Config/Services.php)
     * ================================================================ */

/* ================================================================
     * FACTORIES DE MODELS (Injeção de Dependência)
     * ================================================================ */

    public static function cargosModel(bool $getShared = true): \App\Models\Admin\CargosModel
    {
        return $getShared ? static::getSharedInstance('cargosModel') : new \App\Models\Admin\CargosModel();
    }

    public static function editaisModel(bool $getShared = true): \App\Models\Admin\EditaisModel
    {
        return $getShared ? static::getSharedInstance('editaisModel') : new \App\Models\Admin\EditaisModel();
    }

    public static function editaisCargosModel(bool $getShared = true): \App\Models\Admin\EditaisCargosModel
    {
        return $getShared ? static::getSharedInstance('editaisCargosModel') : new \App\Models\Admin\EditaisCargosModel();
    }

    public static function editaisAbrangenciasModel(bool $getShared = true): \App\Models\Admin\EditaisAbrangenciasModel
    {
        return $getShared ? static::getSharedInstance('editaisAbrangenciasModel') : new \App\Models\Admin\EditaisAbrangenciasModel();
    }

    /* ================================================================
     * FACTORIES DE SERVICES (Injeção de Dependência)
     * ================================================================ */

    public static function editalGridService(bool $getShared = true): \App\Services\Admin\Editais\EditalGridService
    {
        return $getShared ? static::getSharedInstance('editalGridService') : new \App\Services\Admin\Editais\EditalGridService();
    }

    public static function editalFormService(bool $getShared = true): \App\Services\Admin\Editais\EditalFormService
    {
        return $getShared ? static::getSharedInstance('editalFormService') : new \App\Services\Admin\Editais\EditalFormService();
    }

    public static function editalService(bool $getShared = true): \App\Services\Admin\Editais\EditalService
    {
        return $getShared
            ? static::getSharedInstance('editalService')
            : new \App\Services\Admin\Editais\EditalService(service('editalRepository'));
    }

    public static function editalUploadService(bool $getShared = true): \App\Services\Admin\Editais\EditalUploadService
    {
        return $getShared ? static::getSharedInstance('editalUploadService') : new \App\Services\Admin\Editais\EditalUploadService();
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

        return new \App\Services\Admin\Candidatos\SituacaoService(
                new \App\Models\Admin\SituacaoModel()
        );
    }

    public static function escolaridadeGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('escolaridadeGridService') : new \App\Services\Admin\Escolaridades\EscolaridadeGridService();
    }

    public static function adminEscolaridadeService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('adminEscolaridadeService') : new \App\Services\Admin\Escolaridades\EscolaridadeService();
    }

    public static function experienciaGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('experienciaGridService') : new \App\Services\Admin\Experiencias\ExperienciaGridService();
    }

    public static function adminExperienciaService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('adminExperienciaService') : new \App\Services\Admin\Experiencias\ExperienciaService();
    }

    public static function aperfeicoamentoGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('aperfeicoamentoGridService') : new \App\Services\Admin\Aperfeicoamentos\AperfeicoamentoGridService();
    }

    public static function adminAperfeicoamentoService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('adminAperfeicoamentoService') : new \App\Services\Admin\Aperfeicoamentos\AperfeicoamentoService();
    }

    /* ================================================================
     * FACTORIES DE EXPORTAÇÃO
     * ================================================================ */

    public static function excelExportService(bool $getShared = true): \App\Services\Admin\Export\ExcelExportService
    {
        return $getShared ? static::getSharedInstance('excelExportService') : new \App\Services\Admin\Export\ExcelExportService();
    }

    public static function pdfExportService(bool $getShared = true): \App\Services\Admin\Export\PdfExportService
    {
        return $getShared ? static::getSharedInstance('pdfExportService') : new \App\Services\Admin\Export\PdfExportService();
    }

    public static function qrCodeService(bool $getShared = true): \App\Services\Admin\Export\QrCodeService
    {
        return $getShared ? static::getSharedInstance('qrCodeService') : new \App\Services\Admin\Export\QrCodeService();
    }

    /* ================================================================
     * FACTORIES DE REPOSITORIES (Injeção de Dependência)
     * ================================================================ */

    public static function adminCandidatoRepository(bool $getShared = true): \App\Repositories\Admin\Interfaces\ICandidatoRepository
    {
        return $getShared
            ? static::getSharedInstance('adminCandidatoRepository')
            : new \App\Repositories\Admin\CandidatoRepository(service('candidatosModel'));
    }

    public static function adminEditalRepository(bool $getShared = true): \App\Repositories\Admin\Interfaces\IEditalRepository
    {
        return $getShared
            ? static::getSharedInstance('adminEditalRepository')
            : new \App\Repositories\Admin\EditalRepository(service('editaisModel'));
    }

    public static function classificacaoRepository(bool $getShared = true): \App\Repositories\Admin\Interfaces\IClassificacaoRepository
    {
        return $getShared
            ? static::getSharedInstance('classificacaoRepository')
            : new \App\Repositories\Admin\ClassificacaoRepository(service('classificacaoModel'));
    }

    public static function adminContratoRepository(bool $getShared = true): \App\Repositories\Admin\Interfaces\IContratoRepository
    {
        return $getShared
            ? static::getSharedInstance('adminContratoRepository')
            : new \App\Repositories\Admin\ContratoRepository(service('dadosContratosModel'));
    }

    public static function criterioAdicionalGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('criterioAdicionalGridService') : new \App\Services\Admin\CriteriosAdicionais\CriterioAdicionalGridService();
    }

    public static function adminCriterioAdicionalService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('adminCriterioAdicionalService') : new \App\Services\Admin\CriteriosAdicionais\CriterioAdicionalService();
    }

    public static function secretariasModel(bool $getShared = true): \App\Models\Admin\SecretariasModel
    {
        return $getShared ? static::getSharedInstance('secretariasModel') : new \App\Models\Admin\SecretariasModel();
    }

    public static function setoresModel(bool $getShared = true): \App\Models\Admin\SetoresModel
    {
        return $getShared ? static::getSharedInstance('setoresModel') : new \App\Models\Admin\SetoresModel();
    }

    public static function vagasModel(bool $getShared = true): \App\Models\Admin\VagasModel
    {
        return $getShared ? static::getSharedInstance('vagasModel') : new \App\Models\Admin\VagasModel();
    }

    public static function cursosModel(bool $getShared = true): \App\Models\Admin\CursosModel
    {
        return $getShared ? static::getSharedInstance('cursosModel') : new \App\Models\Admin\CursosModel();
    }

    public static function cargoService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('cargoService') : new \App\Services\Admin\Cargos\CargoService();
    }

    public static function cargoGridService(bool $getShared = true)
    {
        return $getShared ? static::getSharedInstance('cargoGridService') : new \App\Services\Admin\Cargos\CargoGridService();
    }

    public static function candidatosService(bool $getShared = true)
    {
        return $getShared
            ? static::getSharedInstance('candidatosService')
            : new \App\Services\Admin\Candidatos\CandidatosService(service('candidatoRepository'));
    }
    public static function cargoFormService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargoFormService');
        }

        return new \App\Services\Admin\Cargos\CargoFormService();
    }
    public static function cargosCursosFormService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargosCursosFormService');
        }

        return new \App\Services\Admin\Cargos\CargosCursosFormService();
    }
    public static function cargosCriteriosFormService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cargosCriteriosFormService');
        }

        return new \App\Services\Admin\Cargos\CargosCriteriosFormService();
    }
    public static function cargosCriteriosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosCriteriosService') : new \App\Services\Admin\Cargos\CargosCriteriosService();
    }

    /* ================================================================
     * FACTORIES DE MODELS RESTANTES
     * ================================================================ */

    public static function criteriosAdicionaisModel(bool $getShared = true): \App\Models\Admin\CriteriosAdicionaisModel
    {
        return $getShared ? static::getSharedInstance('criteriosAdicionaisModel') : new \App\Models\Admin\CriteriosAdicionaisModel();
    }

    public static function cargosCriteriosAdicionaisModel(bool $getShared = true): \App\Models\Admin\CargosCriteriosAdicionaisModel
    {
        return $getShared ? static::getSharedInstance('cargosCriteriosAdicionaisModel') : new \App\Models\Admin\CargosCriteriosAdicionaisModel();
    }

    public static function cargosCursosModel(bool $getShared = true): \App\Models\Admin\CargosCursosModel
    {
        return $getShared ? static::getSharedInstance('cargosCursosModel') : new \App\Models\Admin\CargosCursosModel();
    }

    public static function escolaridadesModel(bool $getShared = true): \App\Models\Admin\EscolaridadesModel
    {
        return $getShared ? static::getSharedInstance('escolaridadesModel') : new \App\Models\Admin\EscolaridadesModel();
    }

    public static function cargosEscolaridadesModel(bool $getShared = true): \App\Models\Admin\CargosEscolaridadesModel
    {
        return $getShared ? static::getSharedInstance('cargosEscolaridadesModel') : new \App\Models\Admin\CargosEscolaridadesModel();
    }

    public static function experienciasModel(bool $getShared = true): \App\Models\Admin\ExperienciasModel
    {
        return $getShared ? static::getSharedInstance('experienciasModel') : new \App\Models\Admin\ExperienciasModel();
    }

    public static function cargosExperienciasModel(bool $getShared = true): \App\Models\Admin\CargosExperienciasModel
    {
        return $getShared ? static::getSharedInstance('cargosExperienciasModel') : new \App\Models\Admin\CargosExperienciasModel();
    }

    public static function classificacaoModel(bool $getShared = true): \App\Models\Admin\ClassificacaoModel
    {
        return $getShared ? static::getSharedInstance('classificacaoModel') : new \App\Models\Admin\ClassificacaoModel();
    }

    public static function dadosContratosModel(bool $getShared = true): \App\Models\Admin\DadosContratosModel
    {
        return $getShared ? static::getSharedInstance('dadosContratosModel') : new \App\Models\Admin\DadosContratosModel();
    }

    public static function dadosRescisaoModel(bool $getShared = true): \App\Models\Admin\DadosRescisaoModel
    {
        return $getShared ? static::getSharedInstance('dadosRescisaoModel') : new \App\Models\Admin\DadosRescisaoModel();
    }

    public static function dadosAditivosModel(bool $getShared = true): \App\Models\Admin\DadosAditivosModel
    {
        return $getShared ? static::getSharedInstance('dadosAditivosModel') : new \App\Models\Admin\DadosAditivosModel();
    }

    public static function convocadosModel(bool $getShared = true): \App\Models\Admin\ConvocadosModel
    {
        return $getShared ? static::getSharedInstance('convocadosModel') : new \App\Models\Admin\ConvocadosModel();
    }

    public static function verificadorModel(bool $getShared = true): \App\Models\Admin\VerificadorModel
    {
        return $getShared ? static::getSharedInstance('verificadorModel') : new \App\Models\Admin\VerificadorModel();
    }

    public static function contratoService(bool $getShared = true): \App\Services\Admin\Contratos\ContratoService
    {
        return $getShared
            ? static::getSharedInstance('contratoService')
            : new \App\Services\Admin\Contratos\ContratoService(
                service('contratoRepository'),
                service('vagasModel'),
                service('logsService'),
                service('dadosRescisaoModel'),
                service('dadosAditivosModel'),
                service('convocadosModel'),
                service('verificadorModel')
            );
    }

    public static function cadastrosExperienciasModel(bool $getShared = true): \App\Models\Admin\CadastrosExperienciasModel
    {
        return $getShared ? static::getSharedInstance('cadastrosExperienciasModel') : new \App\Models\Admin\CadastrosExperienciasModel();
    }

    public static function cadastrosEscolaridadesModel(bool $getShared = true): \App\Models\Admin\CadastrosEscolaridadesModel
    {
        return $getShared ? static::getSharedInstance('cadastrosEscolaridadesModel') : new \App\Models\Admin\CadastrosEscolaridadesModel();
    }

    public static function cadastrosAperfeicoamentosModel(bool $getShared = true): \App\Models\Admin\CadastrosAperfeicoamentosModel
    {
        return $getShared ? static::getSharedInstance('cadastrosAperfeicoamentosModel') : new \App\Models\Admin\CadastrosAperfeicoamentosModel();
    }

    public static function protocolosModel(bool $getShared = true): \App\Models\Admin\ProtocolosModel
    {
        return $getShared ? static::getSharedInstance('protocolosModel') : new \App\Models\Admin\ProtocolosModel();
    }

    public static function academicosModel(bool $getShared = true): \App\Models\Admin\AcademicosModel
    {
        return $getShared ? static::getSharedInstance('academicosModel') : new \App\Models\Admin\AcademicosModel();
    }

    public static function abrangenciasModel(bool $getShared = true): \App\Models\Admin\EditaisAbrangenciasModel
    {
        return $getShared ? static::getSharedInstance('abrangenciasModel') : new \App\Models\Admin\EditaisAbrangenciasModel();
    }

    public static function cargosCursosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosCursosService') : new \App\Services\Admin\Cargos\CargosCursosService();
    }

    public static function cargosEscolaridadesService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosEscolaridadesService') : new \App\Services\Admin\Cargos\CargosEscolaridadesService();
    }

    public static function cargosExperienciasService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('cargosExperienciasService') : new \App\Services\Admin\Cargos\CargosExperienciasService();
    }

    public static function recursosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('recursosService') : new \App\Services\Admin\Candidatos\RecursosService();
    }

    public static function recursosConsultaService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('recursosConsultaService') : new \App\Services\Admin\Candidatos\RecursosConsultaService();
    }

    public static function logsService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('logsService') : new \App\Services\Admin\LogsService();
    }

    public static function relatoriosService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('relatoriosService') : new \App\Services\Admin\Relatorios\RelatoriosService(
            service('candidatosModel'),
            service('academicosModel'),
            service('cursosModel'),
            service('editaisAbrangenciasModel')
        );
    }

    public static function classificacaoService(bool $getShared = true){
        return $getShared
            ? static::getSharedInstance('classificacaoService')
            : new \App\Services\Admin\Classificacao\ClassificacaoService(service('classificacaoRepository'));
    }

    public static function adminDesempateConfigService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('adminDesempateConfigService') : new \App\Services\Admin\Classificacao\DesempateConfigService();
    }

    public static function classificacaoColunasService(bool $getShared = true){
        return $getShared ? static::getSharedInstance('classificacaoColunasService') : new \App\Services\Admin\Classificacao\ClassificacaoColunasService();
    }

    public static function processosSeletivosService(bool $getShared = true): \App\Services\ProcessosSeletivos\ProcessosSeletivosService
    {
        return $getShared
            ? static::getSharedInstance('processosSeletivosService')
            : new \App\Services\ProcessosSeletivos\ProcessosSeletivosService(
                new \App\Models\ProcessosSeletivosModel(),
                service('logsService', false)
            );
    }

    public static function processosDocumentosService(bool $getShared = true): \App\Services\ProcessosDocumentos\ProcessosDocumentosService
    {
        return $getShared
            ? static::getSharedInstance('processosDocumentosService')
            : new \App\Services\ProcessosDocumentos\ProcessosDocumentosService(
                new \App\Models\Admin\ProcessosDocumentosModel(),
                service('logsService', false)
            );
    }
}
