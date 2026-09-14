<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CargosDesempateConfigModel;
use App\Models\CargosModel;
use App\Models\CargosExperienciasModel;
use App\Models\CargosEscolaridadesModel;
use App\Models\CargosCriteriosAdicionaisModel;
use App\Models\CargosCursosModel;
use App\Services\Classificacao\DesempateConfigService;

class CargosDesempateConfig extends BaseController
{
    protected $helpers = ['url', 'form'];

    /**
     * Tela principal de configuracao de desempate.
     */
    public function index($idCargo = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('home');
        }

        // Aceita tanto /CargosDesempateConfig/5 quanto ?cargo=5
        if (!$idCargo) {
            $idCargo = $this->request->getGet('cargo');
        }

        $cargosModel = new CargosModel();
        $desempateService = new DesempateConfigService();

        $cargoSelecionado = null;
        $configuracoes = [];
        $referencias = [
            'experiencias'      => [],
            'escolaridades'     => [],
            'criterios'         => [],
            'aperfeicoamentos'  => [],
        ];

        if ($idCargo) {
            $cargoSelecionado = $cargosModel->find($idCargo);
            $configuracoes = $desempateService->buscarConfiguracao((int)$idCargo);

            // Carrega referencias do cargo para os dropdowns
            $experienciasModel = new CargosExperienciasModel();
            $referencias['experiencias'] = $experienciasModel->listarExperienciasDoCargo((int)$idCargo);

            $escolaridadesModel = new CargosEscolaridadesModel();
            $referencias['escolaridades'] = $escolaridadesModel->listarEscolaridadesDoCargo((int)$idCargo);

            $criteriosModel = new CargosCriteriosAdicionaisModel();
            $referencias['criterios'] = $criteriosModel->listarCriteriosDoCargo((int)$idCargo);

            $cursosModel = new CargosCursosModel();
            $referencias['aperfeicoamentos'] = $cursosModel->listarCursosDoCargo((int)$idCargo);
        }

        return view('layoutDash', [
            'camada1'          => 'pages',
            'camada2'          => 'cargos',
            'pagina'           => 'DesempateConfig',
            'titulo'           => 'Configuração de Critérios de Desempate',
            'cargos'           => $cargosModel->findAll(),
            'cargoSelecionado' => $cargoSelecionado,
            'configuracoes'    => $configuracoes,
            'referencias'      => $referencias,
            'user'             => session('nome'),
        ]);
    }

    /**
     * Lista configuracoes de desempate para um cargo (AJAX/JSON).
     */
    public function listar($idCargo)
    {
        $desempateService = new DesempateConfigService();
        $config = $desempateService->buscarConfiguracao((int)$idCargo);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $config,
        ]);
    }

    /**
     * Salva um criterio de desempate (novo ou atualizacao).
     * Valida que PONTUACAO_TOTAL deve ser sempre o primeiro critério.
     */
    public function salvar()
    {
        $model = new CargosDesempateConfigModel();

        $idDesempate = $this->request->getPost('pk_id_desempate');
        $cargoId = $this->request->getPost('fk_id_cargo');
        $tipoCriterio = $this->request->getPost('ds_tipo_criterio');
        $ordem = (int) $this->request->getPost('ds_ordem');

        // VALIDACAO: PONTUACAO_TOTAL deve ser sempre o primeiro critério (ordem 1)
        if ($ordem === 1 && $tipoCriterio !== 'PONTUACAO_TOTAL') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'O primeiro critério (ordem 1) deve ser obrigatoriamente "Pontuacao Total" (PONTUACAO_TOTAL).',
            ]);
        }

        // Se já existe config para este cargo, verificar se ordem 1 é PONTUACAO_TOTAL
        $configsExistentes = $model->where('fk_id_cargo', $cargoId)
                              ->orderBy('ds_ordem', 'ASC')
                              ->findAll();

        if (!empty($configsExistentes)) {
            $primeiro = $configsExistentes[0];
            if ((int) $primeiro->ds_ordem === 1 && $primeiro->ds_tipo_criterio !== 'PONTUACAO_TOTAL') {
                // Isso não deveria acontecer, mas se acontecer, alertamos
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Configuração inválida detectada: o primeiro critério existente não é Pontuação Total. Por favor, exclua todos os critérios e recomece com PONTUACAO_TOTAL na ordem 1.',
                ]);
            }
        }

        // Se for novo critério na ordem 1, garantir que é PONTUACAO_TOTAL
        if (!$idDesempate && $ordem === 1 && $tipoCriterio !== 'PONTUACAO_TOTAL') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'O primeiro critério deve ser obrigatoriamente "Pontuacao Total" (PONTUACAO_TOTAL).',
            ]);
        }

        // Se for novo critério e já existe ordem 1, verificar se é PONTUACAO_TOTAL
        if (!$idDesempate && !empty($configsExistentes)) {
            $primeiroTipo = $configsExistentes[0]->ds_tipo_criterio;
            if ($primeiroTipo !== 'PONTUACAO_TOTAL') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'O primeiro critério existente não é "Pontuacao Total". Por favor, configure PONTUACAO_TOTAL na ordem 1 primeiro.',
                ]);
            }
        }

        $data = [
            'fk_id_cargo'        => $cargoId,
            'ds_ordem'           => $ordem,
            'ds_tipo_criterio'   => $tipoCriterio,
            'fk_id_referencia'   => $this->request->getPost('fk_id_referencia') ?: null,
            'ds_direcao'         => $this->request->getPost('ds_direcao') ?: 'DESC',
            'ds_descricao'       => $this->request->getPost('ds_descricao'),
            'ds_parametro_extra' => $this->request->getPost('ds_parametro_extra') ?: null,
        ];

        if ($idDesempate) {
            // Atualizar
            if ($model->update($idDesempate, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Criterio atualizado com sucesso.',
                ]);
            }
        } else {
            // Inserir novo
            if ($model->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Criterio salvo com sucesso.',
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao salvar criterio.',
        ]);
    }

    /**
     * Atualiza a ordem dos criterios (drag-and-drop).
     */
    public function reordenar()
    {
        $model = new CargosDesempateConfigModel();
        $ordens = $this->request->getPost('ordens');

        if (empty($ordens) || !is_array($ordens)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos.',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($ordens as $index => $idDesempate) {
            $model->update($idDesempate, ['ds_ordem' => $index + 1]);
        }

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso.',
        ]);
    }

    /**
     * Remove um criterio de desempate.
     */
    public function excluir($idDesempate)
    {
        $model = new CargosDesempateConfigModel();

        $item = $model->find($idDesempate);
        if (!$item) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Criterio nao encontrado.',
            ]);
        }

        $cargoId = $item->fk_id_cargo;
        $ordem = (int) $item->ds_ordem;

        $db = \Config\Database::connect();
        $db->transStart();

        $model->delete($idDesempate);

        // Reordenar os demais: decrementar ordens maiores
        $model->where('fk_id_cargo', $cargoId)
              ->where('ds_ordem >', $ordem)
              ->set('ds_ordem', 'ds_ordem - 1', false)
              ->update();

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Criterio removido com sucesso.',
        ]);
    }

    /**
     * Move um criterio para cima ou para baixo na ordem.
     * Valida que PONTUACAO_TOTAL deve permanecer sempre na ordem 1.
     */
    public function mover($idDesempate)
    {
        $model = new CargosDesempateConfigModel();
        $direcao = $this->request->getGet('direcao');

        $item = $model->find($idDesempate);
        if (!$item || !in_array($direcao, ['subir', 'descer'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requisicao invalida.',
            ]);
        }

        $cargoId = $item->fk_id_cargo;
        $ordemAtual = (int) $item->ds_ordem;

        // VALIDACAO: Nao permite mover critério que está na ordem 1 para baixo
        // (PONTUACAO_TOTAL deve ficar sempre em primeiro)
        if ($ordemAtual === 1 && $direcao === 'descer') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'O critério "Pontuação Total" deve permanecer sempre em primeiro lugar.',
            ]);
        }

        // Nao permite mover outro critério para cima além do segundo lugar
        // (ou seja, ninguém pode passar a ficar na frente do primeiro critério)
        $configs = $model->where('fk_id_cargo', $cargoId)
                        ->orderBy('ds_ordem', 'ASC')
                        ->findAll();

        $primeiro = $configs[0] ?? null;
        if ($primeiro && $primeiro->pk_id_desempate == $idDesempate && $direcao === 'subir') {
            // Já está no topo, não pode subir mais
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ja esta no topo.',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        if ($direcao === 'subir') {
            $novaOrdem = $ordemAtual - 1;
            // Encontra o item acima e troca com ele
            $model->where('fk_id_cargo', $cargoId)
                  ->where('ds_ordem', $novaOrdem)
                  ->set('ds_ordem', $ordemAtual)
                  ->update();
        } else {
            $novaOrdem = $ordemAtual + 1;
            $model->where('fk_id_cargo', $cargoId)
                  ->where('ds_ordem', $novaOrdem)
                  ->set('ds_ordem', $ordemAtual)
                  ->update();
        }

        $model->update($idDesempate, ['ds_ordem' => $novaOrdem]);

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso.',
        ]);
    }

    /**
     * Busca referencias disponiveis para um cargo (AJAX).
     */
    public function referencias($idCargo)
    {
        $tipo = $this->request->getGet('tipo');
        $resultado = [];

        switch ($tipo) {
            case 'experiencia':
                $model = new CargosExperienciasModel();
                $resultado = $model->listarExperienciasDoCargo((int)$idCargo);
                break;
            case 'escolaridade':
                $model = new CargosEscolaridadesModel();
                $resultado = $model->listarEscolaridadesDoCargo((int)$idCargo);
                break;
            case 'criterio_adicional':
                $model = new CargosCriteriosAdicionaisModel();
                $resultado = $model->listarCriteriosDoCargo((int)$idCargo);
                break;
            case 'aperfeicoamento':
                $model = new CargosCursosModel();
                $resultado = $model->listarCursosDoCargo((int)$idCargo);
                break;
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $resultado,
        ]);
    }
}
