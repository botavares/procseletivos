<?php
namespace App\Services;
use App\Models\CargosModel;
use App\Models\EscolaridadesModel;
use App\Models\AperfeicoamentosModel;
use App\Models\ExperienciasModel;
use App\Models\CriteriosAdicionaisModel;
use App\Models\CadastradosExperienciasModel;
use App\Models\CadastradosEscolaridadesModel;
use App\Models\CadastradosAperfeicoamentosModel;
use App\Models\CadastradosCriteriosAdicionaisModel;
use App\Models\CargosExperienciasModel;
use App\Models\CargosEscolaridadesModel;
use App\Models\CargosAperfeicoamentosModel;
use App\Models\CargosCriteriosAdicionaisModel;
class ClassificatorioService{
    protected CargosModel $cargosModel;
    protected EscolaridadesModel $escolaridadesModel;
    protected AperfeicoamentosModel $aperfeicoamentosModel;
    protected ExperienciasModel $experienciasModel;
    protected CriteriosAdicionaisModel $criteriosAdicionaisModel;
    protected CadastradosExperienciasModel $experienciasCadastrados;
    protected CadastradosEscolaridadesModel $escolaridadesCadastrados;
    protected CadastradosAperfeicoamentosModel $aperfeicoamentosCadastrados;
    protected CadastradosCriteriosAdicionaisModel $criteriosAdicionaisCadastrados;
    protected CargosExperienciasModel $experienciasCargo;
    protected CargosEscolaridadesModel $escolaridadesCargo;
    protected CargosAperfeicoamentosModel $aperfeicoamentosCargo;
    protected CargosCriteriosAdicionaisModel $criteriosAdicionaisCargo;
    public function __construct(){
        $this->cargosModel = new CargosModel();
        $this->escolaridadesModel = new EscolaridadesModel();
        $this->aperfeicoamentosModel = new AperfeicoamentosModel();
        $this->experienciasModel = new ExperienciasModel();
        $this->criteriosAdicionaisModel = new CriteriosAdicionaisModel();

        $this->experienciasCadastrados = new CadastradosExperienciasModel();
        $this->escolaridadesCadastrados = new CadastradosEscolaridadesModel();
        $this->aperfeicoamentosCadastrados = new CadastradosAperfeicoamentosModel();
        $this->criteriosAdicionaisCadastrados = new CadastradosCriteriosAdicionaisModel();

        $this->experienciasCargo = new CargosExperienciasModel();
        $this->escolaridadesCargo = new CargosEscolaridadesModel();
        $this->aperfeicoamentosCargo = new CargosAperfeicoamentosModel();
        $this->criteriosAdicionaisCargo = new CargosCriteriosAdicionaisModel();
    }
    /**
     * Busca dados completos do cargo
     */
    public function buscarDadosCargo(int $cargoId): ?object{
        return $this->cargosModel->where('pk_id_cargo', $cargoId)->first();
    }
    /**
     * Busca requisitos classificatórios do cargo
     */
    public function buscarRequisitos(int $cargoId): array{
        return [
            'experienciasClassificatorias' => $this->experienciasModel->listarRequisitosExperiencias($cargoId),
            'escolaridadesClassificatorias' => $this->escolaridadesModel->listarRequisitosEscolaridades($cargoId),
            'aperfeicoamentosClassificatorios' => $this->aperfeicoamentosModel->listarRequisitosAperfeicoamentos($cargoId),
            'criteriosAdicionaisClassificatorios' => $this->criteriosAdicionaisModel->listarRequisitosCriteriosAdicionais($cargoId),
        ];
    }
    
    /**
     * Busca dados já cadastrados do candidato
     */
    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array{
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
        $criteriosAdicionais = $this->criteriosAdicionaisCadastrados
            ->where('fk_id_cadastrado', $candidatoId)    
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();
        
        // Formata para fácil acesso na view
        $experienciasSalvas = [];
        foreach ($experiencias as $exp) {
            $experienciasSalvas[$exp->fk_id_experiencia] = $exp->ds_quantidade;
        }
        
        //Armazena os dados de escolaridades já cadastradas em um array indexado pelo ID da escolaridade
        $escolaridadesIndexadas = [];
        if (!empty($escolaridades)) {
            foreach ($escolaridades as $esc) {
                $escolaridadesIndexadas[$esc->fk_id_escolaridade] = $esc->ds_quantidade;
            }
        }

         //Armazena os dados de critérios adicionais já cadastrados em um array indexado pelo ID do critério
        $criteriosAdicionaisIndexados = [];
        if (!empty($criteriosAdicionais)) {
            foreach ($criteriosAdicionais as $ca) {
                $criteriosAdicionaisIndexados[$ca->fk_id_criterio] = $ca->ds_quantidade ?? 1;
            }
        }

        //Armazena os dados de aperfeicoamentos já cadastradas em um array indexado pelo ID do curso
        $aperfeicoamentosIndexados = [];
        if (!empty($aperfeicoamentos)) {
            foreach ($aperfeicoamentos as $ap) {
                $aperfeicoamentosIndexados[$ap->fk_id_curso] = $ap->ds_quantidade ?? 1;
            }
        }


        // gerando arrays com ids das escolaridades e aperfeicoamentos e criterios adicionais
        $idsEscolaridades = array_map(fn($e) => $e->fk_id_escolaridade, $escolaridades);
        $idsAperfeicoamentos = array_map(fn($a) => $a->fk_id_curso, $aperfeicoamentos);
        $idsCriteriosAdicionais = array_map(fn($c) => $c->fk_id_criterio, $criteriosAdicionais);
        return [
            'experiencias' => $experiencias,
            'escolaridades' => $escolaridades,
            'aperfeicoamentos' => $aperfeicoamentos,
            'criteriosAdicionais'=>$criteriosAdicionais,
            'experienciasSalvas' => $experienciasSalvas,
            'idsEscolaridades' => $idsEscolaridades,
            'idsAperfeicoamentos' => $idsAperfeicoamentos,
            'idsCriteriosAdicionais'=>$idsCriteriosAdicionais,
            'escolaridadesIndexadas' => $escolaridadesIndexadas,
            'criteriosAdicionaisIndexados'=>$criteriosAdicionaisIndexados,
            'aperfeicoamentosIndexados' => $aperfeicoamentosIndexados
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
        // Processa critérios adicionais
        $criteriosAdicionais=$this->extrairCriteriosAdicionaisDoPost($post,$editalId,$cargoId);
        $this->salvarCriteriosAdicionais($candidatoId,$criteriosAdicionais,$cargoId,$editalId);
        return true;
    }
    private function extrairExperienciasDoPost(array $post, int $editalId, int $cargoId): array
    {
        $experiencias = [];
        $experienciasCargo = $this->experienciasCargo->where('fk_id_cargo', $cargoId)->findAll();
        foreach ($experienciasCargo as $exp) {
           
            $campo = "quantidadeExperiencia{$exp->fk_id_experiencia}"; // fiz esse recurso para concatenar parte do nome do campo com o id da experiencia
            if(!array_key_exists($campo, $post)) {
                continue;
            }   
            $quantidade = is_numeric($post[$campo]) ? (int) $post[$campo] : 0;
            
            $experiencias[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'id_experiencia' => $exp->fk_id_experiencia,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $exp->ds_pontuacao_minima, 
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
                
            ]);
        }
    }
    // Métodos similares para escolaridades aperfeiçoamentos e critérios adicionais...
    private function extrairEscolaridadesDoPost(array $post, int $editalId, int $cargoId): array{
        
        $escolaridades = [];
        $lista = $this->escolaridadesCargo->where('fk_id_cargo', $cargoId)->findAll();
        
        foreach ($lista as $item) {
            $campo = "escolaridade{$item->fk_id_escolaridade}";
            
            $valor = $post[$campo] ?? null;
            
            if ($item->ds_tipo_campo === "CHECK") {
                $quantidade = $valor;
            } else {
                // INPUT numérico
                $quantidade = is_numeric($valor) ? (int) $valor : 0;
            }

            $escolaridades[] = [
                'id_edital'         => $editalId,
                'id_cargo'          => $cargoId,
                'id_escolaridade'   => $item->fk_id_escolaridade,
                'ds_quantidade'     => $quantidade,
                'ds_multiplicador'  => $item->ds_pontuacao_minima,
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
                'ds_quantidade' => $esc['ds_quantidade'],
                'ds_multiplicador' => $esc['ds_multiplicador'],
            ]);
        }
    }
    private function extrairAperfeicoamentosDoPost(array $post, int $editalId, int $cargoId): array{
        $aperfeicoamentos = [];
        $lista = $this->aperfeicoamentosCargo->where('fk_id_cargo', $cargoId)->findAll();
        foreach ($lista as $item) {
            $campo = "aperfeicoamento{$item->fk_id_curso}";
            
            // Checkbox não marcado não é enviado no POST
            $valor = $post[$campo] ?? null;
            
            if ($item->ds_tipo_campo === "CHECK") {
                $quantidade = $valor;
            } else {
                $quantidade = is_numeric($valor) ? (int) $valor : 0;
            }
            
            $aperfeicoamentos[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'status' => $item->ds_obrigatorio,
                'id_aperfeicoamento' => $item->fk_id_curso,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $item->ds_pontuacao_minima,
            ];
        }
        return $aperfeicoamentos;
    }
    private function salvarAperfeicoamentos(int $candidatoId, array $aperfeicoamentos, int $cargoId, int $editalId): void{
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
                
            ]);
        }
    }
    private function extrairCriteriosAdicionaisDoPost(array $post, int $editalId, int $cargoId): array{
        $criteriosAdicionais = [];
        $lista = $this->criteriosAdicionaisCargo->where('fk_id_cargo', $cargoId)->findAll();
        foreach ($lista as $item) {
            $campo = "criterio{$item->fk_id_criterio}";
            
            // Checkbox não marcado não é enviado no POST
            $valor = $post[$campo] ?? null;
            
            if ($item->ds_tipo_campo === "CHECK") {
                $quantidade = $valor;
            } else {
                $quantidade = is_numeric($valor) ? (int) $valor : 0;
            }

            $criteriosAdicionais[] = [
                'id_edital' => $editalId,
                'id_cargo'              => $cargoId,
                'id_criterio_adicional' => $item->fk_id_criterio,
                'ds_quantidade'         => $quantidade,
                'ds_multiplicador'      => $item->ds_pontuacao_minima,
            ];
        }
        return $criteriosAdicionais;
    }
    private function salvarCriteriosAdicionais(int $candidatoId, array $criteriosAdicionais, int $cargoId, int $editalId): void{
        $this->criteriosAdicionaisCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();
        foreach ($criteriosAdicionais as $ca) {
            $this->criteriosAdicionaisCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_edital' => $ca['id_edital'],
                'fk_id_cargo' => $ca['id_cargo'],
                'fk_id_criterio' => $ca['id_criterio_adicional'],
                'ds_quantidade' => $ca['ds_quantidade'],
                'ds_multiplicador' => $ca['ds_multiplicador'],
                
            ]);
        }
    }
}