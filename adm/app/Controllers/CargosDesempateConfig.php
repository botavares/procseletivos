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

class CargosDesempateConfig extends Controller
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
     */
    public function salvar()
    {
        $model = new CargosDesempateConfigModel();

        $idDesempate = $this->request->getPost('pk_id_desempate');

        $data = [
            'fk_id_cargo'        => $this->request->getPost('fk_id_cargo'),
            'ds_ordem'           => $this->request->getPost('ds_ordem'),
            'ds_tipo_criterio'   => $this->request->getPost('ds_tipo_criterio'),
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

        $db = \Config\Database::connect();
        $db->transStart();

        if ($direcao === 'subir') {
            if ($ordemAtual <= 1) {
                $db->transComplete();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ja esta no topo.',
                ]);
            }
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
