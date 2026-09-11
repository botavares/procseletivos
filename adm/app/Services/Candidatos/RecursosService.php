<?php
namespace App\Services\Candidatos;
use RuntimeException;
use App\Services\Base\AbstractCrudService;
use App\Models\CadastrosExperienciasModel;
use App\Models\CadastrosEscolaridadesModel;
use App\Models\CadastrosAperfeicoamentosModel;
use App\Models\CadastrosCriteriosAdicionais;
use App\Models\CargosExperienciasModel;
use App\Models\CargosEscolaridadesModel;
use App\Models\CargosAperfeicoamentosModel;
use App\Models\CargosCriteriosAdicionaisModel;

class RecursosService extends AbstractCrudService{

    public function aplicarRecursos(int $idEdital, int $idCargo, int $idCandidato, array $recursos){
        $protocolo = $recursos['ds_protocolo'] ?? '';

        return $this->transactional(function () use($idEdital, $idCargo, $idCandidato, $protocolo, $recursos){
            $this->salvarCategoria(
                'experiencias',
                $idEdital,
                $idCargo,
                $idCandidato,
                $protocolo,
                $recursos['ds_experiencias'] ?? [],
                'tb_cadastrados_experiencias',
                'fk_id_experiencia',
                new CargosExperienciasModel()
            );

            $this->salvarCategoria(
                'escolaridades',
                $idEdital,
                $idCargo,
                $idCandidato,
                $protocolo,
                $recursos['ds_escolaridades'] ?? [],
                'tb_cadastrados_escolaridades',
                'fk_id_escolaridade',
                new CargosEscolaridadesModel()
            );

            $this->salvarCategoria(
                'aperfeicoamentos',
                $idEdital,
                $idCargo,
                $idCandidato,
                $protocolo,
                $recursos['ds_aperfeicoamentos'] ?? [],
                'tb_cadastrados_aperfeicoamentos',
                'fk_id_curso',
                new CargosAperfeicoamentosModel()
            );

            $this->salvarCategoria(
                'criterios',
                $idEdital,
                $idCargo,
                $idCandidato,
                $protocolo,
                $recursos['ds_criterios'] ?? [],
                'tb_cadastrados_criterios',
                'fk_id_criterio',
                new CargosCriteriosAdicionaisModel()
            );

            return true;
        });
    }

    private function salvarCategoria(
        string $nomeCategoria,
        int $idEdital,
        int $idCargo,
        int $idCandidato,
        string $protocolo,
        array $dadosPost,
        string $tabelaCadastro,
        string $fkCampo,
        $modelConfig
    ) {
        // Busca dados atuais do candidato usando query builder direto
        $dadosBanco = $this->db->table($tabelaCadastro)
            ->where('fk_id_cadastrado', $idCandidato)
            ->where('fk_id_edital', $idEdital)
            ->where('fk_id_cargo', $idCargo)
            ->get()
            ->getResult();

        $alteracoes = $this->verificaAlteracoes($dadosBanco, $dadosPost, $fkCampo);

        if (empty($alteracoes)) {
            return;
        }

        // Registrar histórico usando query builder direto
        $this->registrarRecursos($idEdital, $idCargo, $idCandidato, $nomeCategoria, $alteracoes, $protocolo);

        // Remover registros anteriores
        $this->db->table($tabelaCadastro)
            ->where('fk_id_cadastrado', $idCandidato)
            ->where('fk_id_edital', $idEdital)
            ->where('fk_id_cargo', $idCargo)
            ->delete();

        // Buscar configurações do cargo para obter multiplicadores
        $configMap = [];
        if ($nomeCategoria === 'experiencias') {
            $configs = $modelConfig->listarExperienciasDoCargo($idCargo);
            foreach ($configs as $c) {
                $configMap[(int)$c->fk_id_experiencia] = $c;
            }
        } elseif ($nomeCategoria === 'escolaridades') {
            $configs = $modelConfig->listarEscolaridadesDoCargo($idCargo);
            foreach ($configs as $c) {
                $configMap[(int)$c->fk_id_escolaridade] = $c;
            }
        } elseif ($nomeCategoria === 'aperfeicoamentos') {
            $configs = $modelConfig->listarAperfeicoamentosDoCargo($idCargo);
            foreach ($configs as $c) {
                $configMap[(int)$c->fk_id_curso] = $c;
            }
        } elseif ($nomeCategoria === 'criterios') {
            $configs = $modelConfig->listarCriteriosDoCargo($idCargo);
            foreach ($configs as $c) {
                $configMap[(int)$c->fk_id_criterio] = $c;
            }
        }

        // Inserir novos registros
        foreach ($dadosPost as $idCampo => $quantidade) {
            $quantidade = (int) $quantidade;
            if ($quantidade < 0) {
                continue;
            }

            $idCampoInt = (int) $idCampo;
            $ds_multiplicador = 0;
            if (isset($configMap[$idCampoInt])) {
                $config = $configMap[$idCampoInt];
                $ds_multiplicador = (float) ($config->ds_pontuacao_minima ?? 0);
            }

            $insertData = [
                'fk_id_cadastrado' => $idCandidato,
                'fk_id_edital'     => $idEdital,
                'fk_id_cargo'      => $idCargo,
                $fkCampo           => $idCampoInt,
                'ds_quantidade'    => $quantidade,
                'ds_multiplicador' => $ds_multiplicador,
            ];

            $this->db->table($tabelaCadastro)->insert($insertData);
        }
    }

    public function tiposCamposFormulario($idCargo){
        $cadastroExperienciasModel = new CadastrosExperienciasModel();
        $experiencias = $cadastroExperienciasModel->listarExperiencias($idCargo);

        $cadastroEscolaridadesModel = new CadastrosEscolaridadesModel();
        $escolaridades = $cadastroEscolaridadesModel->listarEscolaridades($idCargo);

        $cadastroAperfeicoamentosModel = new CadastrosAperfeicoamentosModel();
        $aperfeicoamentos = $cadastroAperfeicoamentosModel->listarAperfeicoamentos($idCargo);

        $cadastroCriteriosModel = new CadastrosCriteriosAdicionais();
        $criterios = $cadastroCriteriosModel->listarCriterios($idCargo);

        return [
            'experiencias'     => $experiencias,
            'escolaridades'    => $escolaridades,
            'aperfeicoamentos' => $aperfeicoamentos,
            'criterios'        => $criterios,
        ];
    }

    private function verificaAlteracoes(array $dadosBanco, array $dadosPost, string $fkCampo): array
    {
        $alteracoes = [];

        $banco = [];
        foreach ($dadosBanco as $exp) {
            $banco[(int)$exp->{$fkCampo}] = (int)$exp->ds_quantidade;
        }

        $post = [];
        foreach ($dadosPost as $id => $quantidade) {
            $post[(int)$id] = (int)$quantidade;
        }

        foreach ($banco as $id => $quantidadeAntiga) {
            if (!array_key_exists($id, $post)) {
                $alteracoes[] = [
                    'id_campo'     => $id,
                    'valor_antigo' => $quantidadeAntiga,
                    'valor_novo'   => null,
                    'tipo'         => 'removido'
                ];
                continue;
            }

            $quantidadeNova = $post[$id];
            if ($quantidadeAntiga !== $quantidadeNova) {
                $alteracoes[] = [
                    'id_campo'     => $id,
                    'valor_antigo' => $quantidadeAntiga,
                    'valor_novo'   => $quantidadeNova,
                    'tipo'         => 'alterado'
                ];
            }
        }

        foreach ($post as $id => $quantidadeNova) {
            if (!array_key_exists($id, $banco)) {
                $alteracoes[] = [
                    'id_campo'     => $id,
                    'valor_antigo' => null,
                    'valor_novo'   => $quantidadeNova,
                    'tipo'         => 'inserido'
                ];
            }
        }

        return $alteracoes;
    }

    private function registrarRecursos(int $edital, int $cargo, int $candidato, string $nomeCategoria, array $alteracoes, string $protocolo){
        foreach ($alteracoes as $alteracao) {
            $idCampo = (int) $alteracao['id_campo'];

            $registro = [
                'fk_id_edital'           => $edital,
                'fk_id_cargo'            => $cargo,
                'fk_id_candidato'        => $candidato,
                'ds_campo_alterado'      => $nomeCategoria,
                'fk_id_campo_alterado'   => $idCampo,
                'ds_tipo'                => $alteracao['tipo'],
                'ds_valor_antigo'        => $alteracao['valor_antigo'],
                'ds_valor_novo'          => $alteracao['valor_novo'],
                'ds_numero_protocolo'    => $protocolo,
                'ds_usuario_responsavel' => session('nome'),
                'ds_data_alteracao'      => date('Y-m-d'),
                'ds_hora_alteracao'      => date('H:i:s')
            ];

            // Verifica se já existe registro com mesmo protocolo + campo
            $existente = $this->db->table('tb_cadastrados_recursos')
                ->where('fk_id_edital', $edital)
                ->where('fk_id_cargo', $cargo)
                ->where('fk_id_candidato', $candidato)
                ->where('ds_numero_protocolo', $protocolo)
                ->where('ds_campo_alterado', $nomeCategoria)
                ->where('fk_id_campo_alterado', $idCampo)
                ->get()
                ->getRow();

            if ($existente) {
                $this->db->table('tb_cadastrados_recursos')
                    ->where('pk_id_historico', $existente->pk_id_historico)
                    ->update($registro);
            } else {
                $this->db->table('tb_cadastrados_recursos')->insert($registro);
            }
        }
    }
}
