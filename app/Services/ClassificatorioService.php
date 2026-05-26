<?php
namespace App\Services;
use App\Models\CargosModel;
use App\Models\EscolaridadesModel;
use App\Models\AperfeicoamentosModel;
use App\Models\ExperienciasModel;
use App\Models\CadastradosExperienciasModel;
use App\Models\CadastradosEscolaridadesModel;
use App\Models\CadastradosAperfeicoamentosModel;
use App\Models\CargosExperienciasEditaisModel;
use App\Models\CargosEscolaridadesEditaisModel;
use App\Models\CargosAperfeicoamentosEditaisModel;
class ClassificatorioService
{
    protected CargosModel $cargosModel;
    protected EscolaridadesModel $escolaridadesModel;
    protected AperfeicoamentosModel $aperfeicoamentosModel;
    protected ExperienciasModel $experienciasModel;
    protected CadastradosExperienciasModel $experienciasCadastrados;
    protected CadastradosEscolaridadesModel $escolaridadesCadastrados;
    protected CadastradosAperfeicoamentosModel $aperfeicoamentosCadastrados;
    protected CargosExperienciasEditaisModel $experienciasCargo;
    protected CargosEscolaridadesEditaisModel $escolaridadesCargo;
    protected CargosAperfeicoamentosEditaisModel $aperfeicoamentosCargo;
    public function __construct()
    {
        $this->cargosModel = new CargosModel();
        $this->escolaridadesModel = new EscolaridadesModel();
        $this->aperfeicoamentosModel = new AperfeicoamentosModel();
        $this->experienciasModel = new ExperienciasModel();
        $this->experienciasCadastrados = new CadastradosExperienciasModel();
        $this->escolaridadesCadastrados = new CadastradosEscolaridadesModel();
        $this->aperfeicoamentosCadastrados = new CadastradosAperfeicoamentosModel();
        $this->experienciasCargo = new CargosExperienciasEditaisModel();
        $this->escolaridadesCargo = new CargosEscolaridadesEditaisModel();
        $this->aperfeicoamentosCargo = new CargosAperfeicoamentosEditaisModel();
    }
    /**
     * Busca dados completos do cargo
     */
    public function buscarDadosCargo(int $cargoId): ?object
    {
        return $this->cargosModel->where('pk_id_cargo', $cargoId)->first();
    }
    /**
     * Busca requisitos classificatórios do cargo
     */
    public function buscarRequisitos(int $cargoId): array
    {
        return [
            'escolaridadesObrigatorias' => $this->escolaridadesModel->listarStatusEscolaridades($cargoId, '1'),
            'escolaridadesClassificatorias' => $this->escolaridadesModel->listarStatusEscolaridades($cargoId, '0'),
            'aperfeicoamentosObrigatorios' => $this->aperfeicoamentosModel->listarStatusAperfeicoamentos($cargoId, '1'),
            'aperfeicoamentosClassificatorios' => $this->aperfeicoamentosModel->listarStatusAperfeicoamentos($cargoId, '0'),
        ];
    }
    /**
     * Busca experiências do cargo no edital
     */
    public function buscarExperiencias(int $editalId, int $cargoId): array
    {
        return $this->experienciasModel->listarExperienciaDoCargo($editalId, $cargoId);
    }
    /**
     * Busca dados já cadastrados do candidato
     */
    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array
    {
        $experiencias = $this->experienciasCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();
        $escolaridades = $this->escolaridadesCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();
        $aperfeicoamentos = $this->aperfeicoamentosCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();
        // Formata para fácil acesso na view
        $experienciasSalvas = [];
        foreach ($experiencias as $exp) {
            $experienciasSalvas[$exp->fk_id_experiencia] = $exp->ds_quantidade;
        }
        $idsEscolaridades = array_map(fn($e) => $e->fk_id_escolaridade, $escolaridades);
        $idsAperfeicoamentos = array_map(fn($a) => $a->fk_id_curso, $aperfeicoamentos);
        return [
            'experiencias' => $experiencias,
            'escolaridades' => $escolaridades,
            'aperfeicoamentos' => $aperfeicoamentos,
            'experienciasSalvas' => $experienciasSalvas,
            'idsEscolaridades' => $idsEscolaridades,
            'idsAperfeicoamentos' => $idsAperfeicoamentos,
        ];
    }
    /**
     * Processa e salva dados do formulário classificatório
     */
    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $editalId): bool
    {
        // Processa experiências
        $experiencias = $this->extrairExperienciasDoPost($post, $editalId, $cargoId);
        $this->salvarExperiencias($candidatoId, $experiencias, $cargoId, $editalId);
        // Processa escolaridades
        $escolaridades = $this->extrairEscolaridadesDoPost($post, $editalId, $cargoId);
        $this->salvarEscolaridades($candidatoId, $escolaridades, $cargoId, $editalId);
        // Processa aperfeiçoamentos
        $aperfeicoamentos = $this->extrairAperfeicoamentosDoPost($post, $editalId, $cargoId);
        $this->salvarAperfeicoamentos($candidatoId, $aperfeicoamentos, $cargoId, $editalId);
        return true;
    }
    private function extrairExperienciasDoPost(array $post, int $editalId, int $cargoId): array
    {
        $experiencias = [];
        $experienciasCargo = $this->experienciasCargo->where('fk_id_cargo', $cargoId)->findAll();
        foreach ($experienciasCargo as $exp) {
            if ($exp->ds_obrigatorio !== "0") {
                continue;
            }
            $campo = "quantidadeExperiencia{$exp->fk_id_experiencia}"; 
            if(!array_key_exists($campo, $post)) {
                continue;
            }   
            $quantidade = is_numeric($post[$campo]) ? (int) $post[$campo] : 0;
            
            $experiencias[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'status' => $exp->ds_obrigatorio,
                'id_experiencia' => $exp->fk_id_experiencia,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $exp->ds_multiplicador,
            ];
            
        }
        return $experiencias;
    }
    private function salvarExperiencias(int $candidatoId, array $experiencias, int $cargoId, int $editalId): void{
        // Remove registros anteriores
        $this->experienciasCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();
        // Insere novos
        foreach ($experiencias as $exp) {
            $this->experienciasCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_cargo' => $exp['id_cargo'],
                'fk_id_edital' => $exp['id_edital'],
                'fk_id_experiencia' => $exp['id_experiencia'],
                'ds_quantidade' => $exp['ds_quantidade'],
                'ds_multiplicador' => $exp['ds_multiplicador'],
                'ds_obrigatorio' => $exp['status'],
            ]);
        }
    }
    // Métodos similares para escolaridades e aperfeiçoamentos...
    private function extrairEscolaridadesDoPost(array $post, int $editalId, int $cargoId): array
    {
        $escolaridades = [];
        $lista = $this->escolaridadesCargo->where('fk_id_cargo', $cargoId)->findAll();
        
        foreach ($lista as $item) {
            $campo = "escolaridade{$item->fk_id_escolaridade}";
            
            /*if (!isset($post[$campo])) {
                continue;
            }*/
            $valor = $post[$campo] ?? null;
            
            if ($item->ds_tipo_campo === "CHECK") {
                if($valor === "on" || $valor === "1") {
                    $valor = $item->ds_pontuacao_minima;
                } else {
                    $valor = 0;
                }
                
                // checkbox usa valor da pontuação mínima
                $quantidade = is_numeric($valor) ? (int) $valor : 0;
            } else {
                // input numérico normal
                $quantidade = (int) ($valor ?: 0);
            }
            
            $escolaridades[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'status' => $item->ds_obrigatorio,
                'id_escolaridade' => $item->fk_id_escolaridade,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $item->ds_multiplicador,
            ];
            
        }
        return $escolaridades;
    }
    private function salvarEscolaridades(int $candidatoId, array $escolaridades, int $cargoId, int $editalId): void
    {
        $this->escolaridadesCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();
        foreach ($escolaridades as $esc) {
            $this->escolaridadesCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_edital' => $esc['id_edital'],
                'fk_id_cargo' => $esc['id_cargo'],
                'fk_id_escolaridade' => $esc['id_escolaridade'],
                'ds_status' => $esc['status'],
                'ds_quantidade' => $esc['ds_quantidade'],
                'ds_multiplicador' => $esc['ds_multiplicador'],
            ]);
        }
    }
    private function extrairAperfeicoamentosDoPost(array $post, int $editalId, int $cargoId): array
    {
        $aperfeicoamentos = [];
        $lista = $this->aperfeicoamentosCargo->where('fk_id_cargo', $cargoId)->findAll();
        foreach ($lista as $item) {
            $campo = "aperfeicoamento{$item->fk_id_curso}";
            if (!array_key_exists($campo, $post)) {
                continue;
            }
            $quantidade = is_numeric($post[$campo]) ? (int) $post[$campo] : 0;
            $aperfeicoamentos[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'status' => $item->ds_obrigatorio,
                'id_aperfeicoamento' => $item->fk_id_curso,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $item->ds_multiplicador,
            ];
        }
        return $aperfeicoamentos;
    }
    private function salvarAperfeicoamentos(int $candidatoId, array $aperfeicoamentos, int $cargoId, int $editalId): void
    {
        $this->aperfeicoamentosCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();
        foreach ($aperfeicoamentos as $ap) {
            $this->aperfeicoamentosCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_edital' => $ap['id_edital'],
                'fk_id_cargo' => $ap['id_cargo'],
                'fk_id_curso' => $ap['id_aperfeicoamento'],
                'ds_quantidade' => $ap['ds_quantidade'],
                'ds_multiplicador' => $ap['ds_multiplicador'],
                'ds_status' => $ap['status'],
            ]);
        }
    }
}