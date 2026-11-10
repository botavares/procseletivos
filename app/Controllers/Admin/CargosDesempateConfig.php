<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;
use App\Models\Admin\CargosDesempateConfigModel;
use App\Models\Admin\CargosModel;
use App\Models\Admin\CargosExperienciasModel;
use App\Models\Admin\ExperienciasModel;
use App\Models\Admin\CargosEscolaridadesModel;
use App\Models\Admin\EscolaridadesModel;
use App\Models\Admin\CargosCriteriosAdicionaisModel;
use App\Models\Admin\CargosCursosModel;
use App\Models\Admin\AperfeicoamentosModel;
use App\Models\Admin\CriteriosAdicionaisModel;
use App\Services\Admin\Classificacao\DesempateConfigService;

class CargosDesempateConfig extends BaseController
{
    protected $helpers = ['url', 'form', 'edital'];

    protected $cargosModel;
    protected $cargosDesempateConfigModel;
    protected $cargosExperienciasModel;
    protected $experienciasModel;
    protected $cargosEscolaridadesModel;
    protected $escolaridadesModel;
    protected $cargosCriteriosAdicionaisModel;
    protected $cargosCursosModel;
    protected $aperfeicoamentosModel;
    protected $criteriosAdicionaisModel;

    public function __construct() {
        $this->cargosModel                   = new \App\Models\Admin\CargosModel();
        $this->cargosDesempateConfigModel      = new \App\Models\Admin\CargosDesempateConfigModel();
        $this->cargosExperienciasModel        = new \App\Models\Admin\CargosExperienciasModel();
        $this->experienciasModel             = new \App\Models\Admin\ExperienciasModel();
        $this->cargosEscolaridadesModel       = new \App\Models\Admin\CargosEscolaridadesModel();
        $this->escolaridadesModel            = new \App\Models\Admin\EscolaridadesModel();
        $this->cargosCriteriosAdicionaisModel = new \App\Models\Admin\CargosCriteriosAdicionaisModel();
        $this->cargosCursosModel             = new \App\Models\Admin\CargosCursosModel();
        $this->aperfeicoamentosModel        = new \App\Models\Admin\AperfeicoamentosModel();
        $this->criteriosAdicionaisModel      = new \App\Models\Admin\CriteriosAdicionaisModel();
    }

    /**
     * Tela principal de configuracao de desempate.
     * Aceita:
     *   /CargosDesempateConfig/{idProcesso}/{idCargo}
     *   /CargosDesempateConfig/{idCargo}         (legacy)
     *   /CargosDesempateConfig?cargo={idCargo}   (legacy)
     */
    public function index($idProcesso = null, $idCargo = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('home');
        }

        // Quando só um parâmetro é passado, interpreta como cargo (legacy)
        if ($idProcesso !== null && $idCargo === null) {
            $idCargo = $idProcesso;
            $idProcesso = null;
        }

        // Aceita ?cargo=5 como fallback
        if (!$idCargo) {
            $idCargo = $this->request->getGet('cargo');
        }

        $cargoSelecionado = null;
        $processo = null;
        $configuracoes = [];
        $referencias = [
            'experiencias'      => [],
            'escolaridades'     => [],
            'criterios'         => [],
            'aperfeicoamentos'  => [],
        ];

        if ($idProcesso) {
            $processoModel = new \App\Models\ProcessosSeletivosModel();
            $processo = $processoModel->find($idProcesso);
        }

        $fkIdSecretaria = (int) session('fk_id_secretaria');

        if ($idCargo) {
            $cargoSelecionado = $this->cargosModel->find($idCargo);

            // Restringe configuração de desempate à secretaria do usuário
            if (!$cargoSelecionado || (int) $cargoSelecionado->fk_id_secretaria !== $fkIdSecretaria) {
                return redirect()->to(base_url_adm('Dashboard'))->with('error', 'Cargo não pertence à secretaria do usuário logado.');
            }

            $configuracoes = service('adminDesempateConfigService')->buscarConfiguracao(
                (int)$idCargo,
                $idProcesso ? (int)$idProcesso : null,
                $fkIdSecretaria
            );

            // Carrega referencias do cargo para os dropdowns
            $referencias['experiencias'] = $this->experienciasModel->listarExperienciasPorCargo((int)$idCargo);
            $referencias['escolaridades'] = $this->escolaridadesModel->listarEscolaridadesPorCargo((int)$idCargo);
            $referencias['criterios'] = $this->criteriosAdicionaisModel->listarCriteriosPorCargo((int)$idCargo);
            $referencias['aperfeicoamentos'] = $this->aperfeicoamentosModel->listarAperfeicoamentosPorCargo((int)$idCargo);
        }

        return view('Admin/layoutDash', [
            'camada1'          => 'pages',
            'camada2'          => 'cargos',
            'pagina'           => 'DesempateConfig',
            'titulo'           => 'Configuração de Critérios de Desempate',
            'cargos'           => $this->cargosModel->where('fk_id_secretaria', $fkIdSecretaria)->findAll(),
            'cargoSelecionado' => $cargoSelecionado,
            'processo'         => $processo,
            'idProcesso'       => $idProcesso,
            'idCargo'          => $idCargo,
            'configuracoes'    => $configuracoes,
            'referencias'      => $referencias,
            'user'             => session('nome'),
        ]);
    }

    /**
     * Lista configuracoes de desempate para um cargo (AJAX/JSON).
     *
     * @param int|string $idCargo
     * @param int|string|null $idProcesso
     */
    public function listar($idCargo, $idProcesso = null)
    {
        $fkIdSecretaria = (int) session('fk_id_secretaria');

        $cargo = $this->cargosModel->find($idCargo);
        if (!$cargo || (int) $cargo->fk_id_secretaria !== $fkIdSecretaria) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cargo não pertence à secretaria do usuário logado.',
            ]);
        }

        $config = service('adminDesempateConfigService')->buscarConfiguracao(
            (int)$idCargo,
            $idProcesso ? (int)$idProcesso : null,
            $fkIdSecretaria
        );

        return $this->response->setJSON([
            'success' => true,
            'data'    => $config,
        ]);
    }

    /**
     * Salva um criterio de desempate (novo ou atualizacao).
     * Valida que PONTUACAO_TOTAL deve ser sempre o primeiro critério.
     */
    public function salvar(): ResponseInterface
    {
        $model = $this->cargosDesempateConfigModel;

        $idDesempate = $this->request->getPost('pk_id_desempate');
        $cargoId = $this->request->getPost('fk_id_cargo');
        $processoId = $this->request->getPost('fk_id_processo');
        $tipoCriterio = $this->request->getPost('ds_tipo_criterio');
        $ordem = (int) $this->request->getPost('ds_ordem');

        // VALIDACAO: PONTUACAO_TOTAL deve ser sempre o primeiro critério (ordem 1)
        if ($ordem === 1 && $tipoCriterio !== 'PONTUACAO_TOTAL') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'O primeiro critério (ordem 1) deve ser obrigatoriamente "Pontuacao Total" (PONTUACAO_TOTAL).',
                csrf_token() => csrf_hash(),
            ]);
        }

        // Se já existe config para este cargo, verificar se ordem 1 é PONTUACAO_TOTAL
        $configsExistentes = $this->buscarConfiguracoesExistentes($model, $cargoId, $processoId);

        if (!empty($configsExistentes)) {
            $primeiro = $configsExistentes[0];
            if ((int) $primeiro->ds_ordem === 1 && $primeiro->ds_tipo_criterio !== 'PONTUACAO_TOTAL') {
                // Isso não deveria acontecer, mas se acontecer, alertamos
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Configuração inválida detectada: o primeiro critério existente não é Pontuação Total. Por favor, exclua todos os critérios e recomece com PONTUACAO_TOTAL na ordem 1.',
                    csrf_token() => csrf_hash(),
                ]);
            }
        }

        // Se for novo critério na ordem 1, garantir que é PONTUACAO_TOTAL
        if (!$idDesempate && $ordem === 1 && $tipoCriterio !== 'PONTUACAO_TOTAL') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'O primeiro critério deve ser obrigatoriamente "Pontuacao Total" (PONTUACAO_TOTAL).',
                csrf_token() => csrf_hash(),
            ]);
        }

        // Se for novo critério e já existe ordem 1, verificar se é PONTUACAO_TOTAL
        if (!$idDesempate && !empty($configsExistentes)) {
            $primeiroTipo = $configsExistentes[0]->ds_tipo_criterio;
            if ($primeiroTipo !== 'PONTUACAO_TOTAL') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'O primeiro critério existente não é "Pontuacao Total". Por favor, configure PONTUACAO_TOTAL na ordem 1 primeiro.',
                    csrf_token() => csrf_hash(),
                ]);
            }
        }

        $referencia = $this->request->getPost('fk_id_referencia');
        $referencia = ($referencia === '' || $referencia === null || $referencia === 'undefined')
            ? null
            : (int) $referencia;

        $precisaReferencia = [
            'EXPERIENCIA_ESPECIFICA',
            'ESCOLARIDADE_ESPECIFICA',
            'CRITERIO_ADICIONAL',
            'APERFEICOAMENTO_ESPECIFICO',
        ];
        if (in_array($tipoCriterio, $precisaReferencia, true) && $referencia === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Por favor, selecione uma referencia para este tipo de criterio.',
                csrf_token() => csrf_hash(),
            ]);
        }

        $fkIdSecretaria = (int) session('fk_id_secretaria');

        // Valida que o cargo pertence à secretaria do usuário logado
        $cargo = $this->cargosModel->find($cargoId);
        if (!$cargo || (int) $cargo->fk_id_secretaria !== $fkIdSecretaria) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cargo não pertence à secretaria do usuário logado.',
                csrf_token() => csrf_hash(),
            ]);
        }

        $data = [
            'fk_id_secretaria'        => $fkIdSecretaria,
            'fk_id_cargo'             => $cargoId,
            'fk_id_processo_seletivo' => $processoId ? (int) $processoId : null,
            'ds_ordem'                => $ordem,
            'ds_tipo_criterio'        => $tipoCriterio,
            'fk_id_referencia'        => $referencia,
            'ds_direcao'              => $this->request->getPost('ds_direcao') ?: 'DESC',
            'ds_descricao'            => $this->request->getPost('ds_descricao'),
            'ds_parametro_extra'      => $this->request->getPost('ds_parametro_extra') ?: null,
        ];

        if ($idDesempate) {
            // Atualizar
            if ($model->update($idDesempate, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Criterio atualizado com sucesso.',
                    csrf_token() => csrf_hash(),
                ]);
            }
        } else {
            // Inserir novo
            if ($model->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Criterio salvo com sucesso.',
                    csrf_token() => csrf_hash(),
                ]);
            }
        }

        $erro = $this->obterErroModel($model) ?? 'Erro ao salvar criterio.';
        log_message('error', '[CargosDesempateConfig::salvar] Falha ao salvar: ' . $erro . ' | Dados: ' . json_encode($data));

        return $this->response->setJSON([
            'success' => false,
            'message' => $erro,
            csrf_token() => csrf_hash(),
        ]);
    }

    /**
     * Retorna erros do model como string, ou null se nao houver erro.
     */
    private function obterErroModel(Model $model): ?string
    {
        $erros = $model->errors();
        if (empty($erros)) {
            return null;
        }

        $mensagens = [];
        foreach ($erros as $campo => $erro) {
            $mensagens[] = is_array($erro) ? implode(', ', $erro) : $erro;
        }

        return implode(' | ', $mensagens);
    }

    /**
     * Busca configuracoes existentes respeitando o escopo de processo.
     */
    private function buscarConfiguracoesExistentes(Model $model, $cargoId, $processoId): array
    {
        $fkIdSecretaria = (int) session('fk_id_secretaria');
        $queryExistentes = $model->where('fk_id_cargo', $cargoId)
                                 ->where('fk_id_secretaria', $fkIdSecretaria);

        if ($processoId) {
            $queryExistentes->where('fk_id_processo_seletivo', (int) $processoId);
        } else {
            $queryExistentes->where('fk_id_processo_seletivo IS NULL');
        }

        return $queryExistentes->orderBy('ds_ordem', 'ASC')
                               ->findAll();
    }

    /**
     * Atualiza a ordem dos criterios (drag-and-drop).
     */
    public function reordenar()
    {
        $model = $this->cargosDesempateConfigModel;
        $ordens = $this->request->getPost('ordens');
        $fkIdSecretaria = (int) session('fk_id_secretaria');

        if (empty($ordens) || !is_array($ordens)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos.',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($ordens as $index => $idDesempate) {
            $item = $model->find($idDesempate);
            if ($item && (int) $item->fk_id_secretaria === $fkIdSecretaria) {
                $model->update($idDesempate, ['ds_ordem' => $index + 1]);
            }
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
        $model = $this->cargosDesempateConfigModel;

        $item = $model->find($idDesempate);
        if (!$item) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Criterio nao encontrado.',
            ]);
        }

        $fkIdSecretaria = (int) session('fk_id_secretaria');
        if ((int) $item->fk_id_secretaria !== $fkIdSecretaria) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Critério não pertence à secretaria do usuário logado.',
            ]);
        }

        $cargoId = $item->fk_id_cargo;
        $ordem = (int) $item->ds_ordem;

        $db = \Config\Database::connect();
        $db->transStart();

        $model->delete($idDesempate);

        // Reordenar os demais: decrementar ordens maiores
        $model->where('fk_id_secretaria', $fkIdSecretaria)
              ->where('fk_id_cargo', $cargoId)
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
        $model = $this->cargosDesempateConfigModel;
        $direcao = $this->request->getGet('direcao');

        $fkIdSecretaria = (int) session('fk_id_secretaria');

        $item = $model->find($idDesempate);
        if (!$item || !in_array($direcao, ['subir', 'descer'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requisicao invalida.',
            ]);
        }

        if ((int) $item->fk_id_secretaria !== $fkIdSecretaria) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Critério não pertence à secretaria do usuário logado.',
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
        $configs = $model->where('fk_id_secretaria', $fkIdSecretaria)
                        ->where('fk_id_cargo', $cargoId)
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
            $model->where('fk_id_secretaria', $fkIdSecretaria)
                  ->where('fk_id_cargo', $cargoId)
                  ->where('ds_ordem', $novaOrdem)
                  ->set('ds_ordem', $ordemAtual)
                  ->update();
        } else {
            $novaOrdem = $ordemAtual + 1;
            $model->where('fk_id_secretaria', $fkIdSecretaria)
                  ->where('fk_id_cargo', $cargoId)
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
                $resultado = $this->experienciasModel->listarExperienciasPorCargo((int)$idCargo);
                break;
            case 'escolaridade':
                $resultado = $this->escolaridadesModel->listarEscolaridadesPorCargo((int)$idCargo);
                break;
            case 'criterio_adicional':
                $resultado = $this->criteriosAdicionaisModel->listarCriteriosPorCargo((int)$idCargo);
                break;
            case 'aperfeicoamento':
                $resultado = $this->aperfeicoamentosModel->listarAperfeicoamentosPorCargo((int)$idCargo);
                break;
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $resultado,
        ]);
    }
}
