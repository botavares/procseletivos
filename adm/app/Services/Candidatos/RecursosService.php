<?php
namespace App\Services\Candidatos;
use RuntimeException;
use App\Services\Base\AbstractCrudService;
use App\Models\CandidatosModel;
use App\Models\ProtocolosModel;
use App\Models\RecursosModel;
use App\Models\RecursosCandidatosModel;
use App\Models\CadastrosExperienciasModel;
use App\Models\CadastrosEscolaridadesModel;
use App\Models\CadastrosAperfeicoamentosModel;

class RecursosService extends AbstractCrudService{

    public function aplicarRecursos(int $idEdital,int $idCargo,int $idCandidato,array $recursos){
        //verificar se os dados atuais estão diferentes dos novos
        $candidatosModel = new CandidatosModel();
        $dadosExperienciasModel = new CadastrosExperienciasModel();
        $dadosEscolaridadesModel = new CadastrosEscolaridadesModel();
        $dadosAperfeicoamentosModel = new CadastrosAperfeicoamentosModel();
        $dadosCandidato = $candidatosModel->find($idCandidato);


        //verificar data de nascimento
        list($dia, $mes, $ano) = explode('/', $recursos['ds_data_nascimento']);
        $recursosNascimento = $ano.'-'.$mes.'-'.$dia;
        if($recursosNascimento !== date('Y-m-d', strtotime($dadosCandidato->ds_nascimento))){
            $alteraNascimento = true;
        }else{
            $alteraNascimento = false;
        }
        $protocolo = $recursos['ds_protocolo'];
        
        return $this->transactional(function () use($idEdital,$idCargo,$idCandidato,$protocolo, $recursos){
            
            $this->salvarExperiencias($idEdital, $idCargo, $idCandidato, $protocolo, $recursos['ds_experiencias'] ?? []);
            $this->salvarEscolaridades($idEdital, $idCargo, $idCandidato, $protocolo, $recursos['ds_escolaridades'] ?? []);
            $this->salvarAperfeicoamentos($idEdital, $idCargo, $idCandidato, $protocolo, $recursos['ds_aperfeicoamentos'] ?? []);
            return true;
        });
        
    }

    public function salvarExperiencias(int $idEdital, int $idCargo, int $idCandidato, $protocolo, array $experiencias){
        $cadastroExperienciaModel = new CadastrosExperienciasModel();
        //verificar se quantidade registrada no cadastro de experiencias mudou
        $dadosExperiencias = $cadastroExperienciaModel
                            ->where('fk_id_cadastrado', $idCandidato)
                            ->where('fk_id_edital', $idEdital)
                            ->where('fk_id_cargo', $idCargo)
                            ->findAll();
        $alteraExperiencia = $this->verficaExperiencia($dadosExperiencias, $experiencias);
        
        if(!empty($alteraExperiencia)){
            $this->registrarRecursos($idEdital, $idCargo, $idCandidato, $alteraExperiencia, $protocolo);
                //remover registros anterioes
                $this->db->table('tb_cadastrados_experiencias')
                    ->where('fk_id_cadastrado', $idCandidato)
                    ->where('fk_id_edital', $idEdital)
                    ->where('fk_id_cargo', $idCargo)
                    ->delete();
            
                    //inserir novos registros
                foreach($experiencias as $idExperiencia => $quantidade){
                    $quantidade = (int) $quantidade;
                    $ds_multiplicador = "";
                    $multiplicador = $cadastroExperienciaModel
                    ->where('fk_id_experiencia', $idExperiencia)
                    ->where('fk_id_cargo', $idCargo)
                    ->first();
                    if(!empty($multiplicador)){
                        $ds_multiplicador = $multiplicador->ds_multiplicador;
                    }


                    if($quantidade < 0){
                        continue; 
                    }

                    $this->db->table('tb_cadastrados_experiencias')
                        ->insert([
                        'fk_id_cadastrado' => $idCandidato,
                        'fk_id_edital' => $idEdital,
                        'fk_id_cargo' => $idCargo,
                        'fk_id_experiencia' => $idExperiencia,
                        'ds_quantidade' => $quantidade,
                        'ds_multiplicador' => $ds_multiplicador,
                        'ds_status' => 0
                    ]);
                }
            
        }
    }
    public function salvarEscolaridades(int $idEdital, int $idCargo, int $idCandidato, $protocolo, array $escolaridades){
                
        $cadastroEscolaridadeModel = new CadastrosEscolaridadesModel();
        $dadosEscolaridades = $cadastroEscolaridadeModel
                            ->where('fk_id_cadastrado', $idCandidato)
                            ->where('fk_id_edital', $idEdital)
                            ->where('fk_id_cargo', $idCargo)
                            ->findAll();
        $alteraEscolaridade = $this->verificaEscolaridades($dadosEscolaridades,$escolaridades);
        if(!empty($alteraEscolaridade)){
            $this->registrarRecursos($idEdital, $idCargo, $idCandidato, $alteraEscolaridade, $protocolo);
            //remover registros anterioes
            $this->db->table('tb_cadastrados_escolaridades')
            ->where('fk_id_cadastrado', $idCandidato)
            ->where('fk_id_edital', $idEdital)
            ->where('fk_id_cargo', $idCargo)
            ->delete();
            //inserir novos registros
            foreach($escolaridades as $idEscolaridade => $quantidade){
                $quantidade = (int) $quantidade;
                $ds_multiplicador = "";
                $multiplicador = $cadastroEscolaridadeModel
                ->where('fk_id_escolaridade', $idEscolaridade)
                ->where('fk_id_cargo', $idCargo)
                ->first();
               if(!empty($multiplicador)){
                   $ds_multiplicador = $multiplicador->ds_multiplicador;
               }
                if($quantidade < 0){
                    continue; 
                }
                $this->db->table('tb_cadastrados_escolaridades')
                ->insert([
                    'fk_id_cadastrado' => $idCandidato,
                    'fk_id_edital' => $idEdital,
                    'fk_id_cargo' => $idCargo,
                    'fk_id_escolaridade' => $idEscolaridade,
                    'ds_quantidade' => $quantidade,
                    'ds_multiplicador' => $ds_multiplicador,
                    'ds_status' => 0
                ]);
            }
        }
    }

    public function salvarAperfeicoamentos(int $idEdital, int $idCargo, int $idCandidato, $protocolo, array $aperfeicoamentos){
        $cadastroAperfeicoamentoModel = new CadastrosAperfeicoamentosModel();

        $dadosAperfeicoamentos = $cadastroAperfeicoamentoModel
                            ->where('fk_id_cadastrado', $idCandidato)
                            ->where('fk_id_edital', $idEdital)
                            ->where('fk_id_cargo', $idCargo)
                            ->findAll();
        $alteraAperfeicoamento = $this->verificaAperfeicoamentos($dadosAperfeicoamentos,$aperfeicoamentos);
        if(!empty($alteraAperfeicoamento)){
            $this->registrarRecursos($idEdital, $idCargo, $idCandidato, $alteraAperfeicoamento, $protocolo);
                //remover registros anterioes
                $this->db->table('tb_cadastrados_aperfeicoamentos')
                    ->where('fk_id_cadastrado', $idCandidato)
                    ->where('fk_id_edital', $idEdital)
                    ->where('fk_id_cargo', $idCargo)
                    ->delete();
                //inserir novos registros
                foreach($aperfeicoamentos as $idAperfeicoamento => $quantidade){
                    $quantidade = (int) $quantidade;
                    $ds_multiplicador = "";
                    $multiplicador = $cadastroAperfeicoamentoModel
                    ->where('fk_id_curso', $idAperfeicoamento)
                    ->where('fk_id_cargo', $idCargo)
                    ->first();
                    if(!empty($multiplicador)){
                        $ds_multiplicador = $multiplicador->ds_multiplicador;
                    }

                    if($quantidade < 0){
                        continue; 
                    }

                    $this->db->table('tb_cadastrados_aperfeicoamentos')
                    ->insert([
                        'fk_id_cadastrado' => $idCandidato,
                        'fk_id_edital' => $idEdital,
                        'fk_id_cargo' => $idCargo,
                        'fk_id_curso' => $idAperfeicoamento,
                        'ds_quantidade' => $quantidade,
                        'ds_multiplicador' => $ds_multiplicador,
                        'ds_status' => 0
                    ]);
                }
            
        }
    }

    public function tiposCamposFormulario($idCargo){
        $cadastroExperienciasModel = new CadastrosExperienciasModel();
        $experiencias = $cadastroExperienciasModel->listarExperiencias($idCargo);
        $cadastroEscolaridadesModel = new CadastrosEscolaridadesModel();
        $escolaridades = $cadastroEscolaridadesModel->listarEscolaridades($idCargo);
        $cadastroAperfeicoamentosModel = new CadastrosAperfeicoamentosModel();
        $aperfeicoamentos = $cadastroAperfeicoamentosModel->listarAperfeicoamentos($idCargo);
        return ['experiencias' => $experiencias, 'escolaridades' => $escolaridades, 'aperfeicoamentos' => $aperfeicoamentos];
    }

    public function verficaExperiencia(array $dadosBanco, array $dadosPost): array
{
    $alteracoes = [];

    // Normaliza banco
    $banco = [];
    foreach ($dadosBanco as $exp) {
        $banco[(int)$exp->fk_id_experiencia] = (int)$exp->ds_quantidade;
    }

    // Normaliza post
    $post = [];
    foreach ($dadosPost as $id => $quantidade) {
        $post[(int)$id] = (int)$quantidade;
    }

    // 1️⃣ Verifica alterações e remoções
    foreach ($banco as $id => $quantidadeAntiga) {

        if (!array_key_exists($id, $post)) {

            // Removido
            $alteracoes[] = [
                'campo'          => 'experiencias',
                'id_campo'       => $id,
                'valor_antigo'   => $quantidadeAntiga,
                'valor_novo'     => null,
                'tipo'           => 'removido'
            ];

            continue;
        }

        $quantidadeNova = $post[$id];

        if ($quantidadeAntiga !== $quantidadeNova) {

            // Alterado
            $alteracoes[] = [
                'campo'          => 'experiencias',
                'id_campo'       => $id,
                'valor_antigo'   => $quantidadeAntiga,
                'valor_novo'     => $quantidadeNova,
                'tipo'           => 'alterado'
            ];
        }
    }

    // 2️⃣ Verifica inserções novas
    foreach ($post as $id => $quantidadeNova) {

        if (!array_key_exists($id, $banco)) {

            $alteracoes[] = [
                'campo'          => 'experiencias',
                'id_campo'       => $id,
                'valor_antigo'   => null,
                'valor_novo'     => $quantidadeNova,
                'tipo'           => 'inserido'
            ];
        }
    }

    return $alteracoes;
}
public function verificaEscolaridades(array $dadosBanco, array $dadosPost): array
{
    $alteracoes = [];

    // Normaliza banco
    $banco = [];
    foreach ($dadosBanco as $exp) {
        $banco[(int)$exp->fk_id_escolaridade] = (int)$exp->ds_quantidade;
    }

    // Normaliza post
    $post = [];
    foreach ($dadosPost as $id => $quantidade) {
        $post[(int)$id] = (int)$quantidade;
    }

    // 1️⃣ Verifica alterações e remoções
    foreach ($banco as $id => $quantidadeAntiga) {

        if (!array_key_exists($id, $post)) {

            // Removido
            $alteracoes[] = [
                'campo'          => 'escolaridades',
                'id_campo'       => $id,
                'valor_antigo'   => $quantidadeAntiga,
                'valor_novo'     => null,
                'tipo'           => 'removido'
            ];

            continue;
        }

        $quantidadeNova = $post[$id];

        if ($quantidadeAntiga !== $quantidadeNova) {

            // Alterado
            $alteracoes[] = [
                'campo'          => 'escolaridades',
                'id_campo'       => $id,
                'valor_antigo'   => $quantidadeAntiga,
                'valor_novo'     => $quantidadeNova,
                'tipo'           => 'alterado'
            ];
        }
    }

    // 2️⃣ Verifica inserções novas
    foreach ($post as $id => $quantidadeNova) {

        if (!array_key_exists($id, $banco)) {

            $alteracoes[] = [
                'campo'          => 'escolaridades',
                'id_campo'       => $id,
                'valor_antigo'   => null,
                'valor_novo'     => $quantidadeNova,
                'tipo'           => 'inserido'
            ];
        }
    }
        return $alteracoes;
    }
    public function verificaAperfeicoamentos(array $dadosBanco, array $dadosPost): array
    {
        $alteracoes = [];

        // Normaliza banco
        $banco = [];
        foreach ($dadosBanco as $exp) {
            $banco[(int)$exp->fk_id_curso] = (int)$exp->ds_quantidade;
        }

        // Normaliza post
        $post = [];
        foreach ($dadosPost as $id => $quantidade) {
            $post[(int)$id] = (int)$quantidade;
        }

        // 1️⃣ Verifica alterações e remoções
        foreach ($banco as $id => $quantidadeAntiga) {

            if (!array_key_exists($id, $post)) {

                // Removido
                $alteracoes[] = [
                    'campo'          => 'aperfeicoamentos',
                    'id_campo'       => $id,
                    'valor_antigo'   => $quantidadeAntiga,
                    'valor_novo'     => null,
                    'tipo'           => 'removido'
                ];

                continue;
            }

            $quantidadeNova = $post[$id];

            if ($quantidadeAntiga !== $quantidadeNova) {

                // Alterado
                $alteracoes[] = [
                    'campo'          => 'aperfeicoamentos',
                    'id_campo'       => $id,
                    'valor_antigo'   => $quantidadeAntiga,
                    'valor_novo'     => $quantidadeNova,
                    'tipo'           => 'alterado'
                ];
            }
        }

        // 2️⃣ Verifica inserções novas
        foreach ($post as $id => $quantidadeNova) {

            if (!array_key_exists($id, $banco)) {

                $alteracoes[] = [
                    'campo'          => 'aperfeicoamentos',
                    'id_campo'       => $id,
                    'valor_antigo'   => null,
                    'valor_novo'     => $quantidadeNova,
                    'tipo'           => 'inserido'
                ];
            }
        }

        return $alteracoes;
    }
    public function registrarRecursos($edital, $cargo, $candidato, array $alteracoes,$protocolo){
        $recursosModel = new RecursosModel($this->db);
        foreach ($alteracoes as $alteracao) {
            $registro = array(
                'fk_id_edital'              => $edital,
                'fk_id_cargo'               => $cargo,
                'fk_id_candidato'           => $candidato,
                'ds_campo_alterado'         => $alteracao['campo'],
                'fk_id_campo_alterado'      => $alteracao['id_campo'],
                'ds_tipo'                   => $alteracao['tipo'],
                'ds_valor_antigo'           => $alteracao['valor_antigo'],
                'ds_valor_novo'             => $alteracao['valor_novo'],
                'ds_numero_protocolo'       => $protocolo,
                'ds_usuario_responsavel'    => session('nome'),
                'ds_data_alteracao'         => date('Y-m-d'),
                'ds_hora_alteracao'         => date('H:i:s')
            );
            
            if (!$recursosModel->insert($registro)) {
                throw new RuntimeException(
                    'Erro ao inserir recurso: ' . json_encode($recursosModel->errors())
                );
            }
        }
    }
}