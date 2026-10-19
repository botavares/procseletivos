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
}
