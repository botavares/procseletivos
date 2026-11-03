<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\DTOs\Admin\ProcessosCargosDTO;
use App\Models\Admin\CargosModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\ProcessosCargos\ProcessosCargosService;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProcessosCargos extends BaseController
{
    protected $processosCargosService;
    protected $cargosModel;
    protected $processosModel;

    public function __construct()
    {
        $this->processosCargosService = service('processosCargosService');
        $this->cargosModel              = new CargosModel();
        $this->processosModel           = new ProcessosSeletivosModel();
    }

    /* =============================================================
     * LISTAGEM DE VÍNCULOS CADASTRADOS
     ============================================================= */
    public function index($camada1 = '', $camada2 = 'pages', $page = 'formProcessosCargos')
    {
        // A tela principal exige um processo seletivo; redireciona para a listagem.
        return redirect()
            ->route('ProcessosSeletivos')
            ->with('mensagemError', 'Selecione um processo seletivo para vincular cargos.');
    }

    /* =============================================================
     * TELA DE VINCULAÇÃO: CARGOS DA SECRETARIA PARA UM PROCESSO
     ============================================================= */
    public function vincular(int $idProcesso, ?string $from = null, $camada1 = '', $camada2 = 'pages', $page = 'ProcessosCargos')
    {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $fkIdSecretaria = (int) session('fk_id_secretaria');
        if ($fkIdSecretaria <= 0) {
            return redirect()
                ->route('Dashboard')
                ->with('mensagemError', 'Secretaria não identificada na sessão.');
        }

        $processo = $this->processosModel->find($idProcesso);
        if (!$processo) {
            return redirect()
                ->route('ProcessosSeletivos')
                ->with('mensagemError', 'Processo seletivo não encontrado.');
        }

        $cargos = $this->cargosModel
            ->where('fk_id_secretaria', $fkIdSecretaria)
            ->orderBy('ds_nome_cargo', 'asc')
            ->findAll();

        $vinculados = $this->processosCargosService->buscarPorProcesso($idProcesso);
        $vinculadosIds = array_map(fn ($v) => (int) $v->fk_id_cargo, $vinculados);

        // Remove os cargos já vinculados da lista superior
        $cargosDisponiveis = array_filter($cargos, fn ($c) => !in_array((int) $c->pk_id_cargo, $vinculadosIds, true));

        $returnUrl = match ($from) {
            'alteracao' => 'ProcessosSeletivos/formularioAlteracao/' . $idProcesso,
            default     => 'ProcessosSeletivos',
        };

        return view('Admin/layoutDash', [
            'camada1'     => $camada1,
            'camada2'     => $camada2,
            'pagina'      => $page,
            'titulo'      => 'Vincular Cargos ao Processo',
            'processo'    => $processo,
            'cargos'      => $cargosDisponiveis,
            'vinculados'  => $vinculados,
            'returnUrl'   => $returnUrl,
            'user'        => session('nome'),
        ]);
    }

    /* =============================================================
     * FORMULÁRIOS DE CADASTRO / ALTERAÇÃO DO VÍNCULO
     ============================================================= */
    public function formularioCadastro(int $idProcesso, int $idCargo, $returnTo = null, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formProcessosCargos')
    {
        return $this->renderFormulario([
            'acao'          => 'create',
            'idProcesso'    => $idProcesso,
            'idCargo'       => $idCargo,
            'returnTo'      => $returnTo,
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'page'          => $page,
        ]);
    }

    public function formularioAlteracao(int $id, $returnTo = null, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formProcessosCargos')
    {
        return $this->renderFormulario([
            'acao'     => 'update',
            'id'       => $id,
            'returnTo' => $returnTo,
            'camada1'  => $camada1,
            'camada2'  => $camada2,
            'page'     => $page,
        ]);
    }

    private function renderFormulario(array $config)
    {
        $config = array_merge([
            'id'            => null,
            'idProcesso'    => null,
            'idCargo'       => null,
            'returnTo'      => null,
            'acao'          => 'create',
            'camada1'       => 'pages',
            'camada2'       => 'cadastros',
            'page'          => 'formProcessosCargos',
        ], $config);

        $this->validarView($config['camada1'], $config['camada2'], $config['page']);
        $this->validarSessao();

        $fkIdSecretaria = (int) session('fk_id_secretaria');
        if ($fkIdSecretaria <= 0) {
            return redirect()
                ->route('Dashboard')
                ->with('mensagemError', 'Secretaria não identificada na sessão.');
        }

        $dados    = null;
        $processo = null;
        $cargo    = null;

        if ($config['acao'] === 'update' && $config['id']) {
            $dados = $this->processosCargosService->buscarPorId((int) $config['id']);
            if (!$dados) {
                return redirect()
                    ->route('ProcessosCargos')
                    ->with('mensagemError', 'Vínculo Processo-Cargo não encontrado.');
            }
            $config['idProcesso'] = (int) $dados->fk_id_processo;
            $config['idCargo']    = (int) $dados->fk_id_cargo;
        }

        $idProcesso = (int) $config['idProcesso'];
        $idCargo    = (int) $config['idCargo'];

        $processo = $this->processosModel->find($idProcesso);
        $cargo    = $this->cargosModel->find($idCargo);

        if (!$processo) {
            return redirect()
                ->route('ProcessosSeletivos')
                ->with('mensagemError', 'Processo seletivo não encontrado.');
        }

        if (!$cargo) {
            return redirect()
                ->route('ProcessosCargos')
                ->with('mensagemError', 'Cargo não encontrado.');
        }

        if ($cargo->fk_id_secretaria != $fkIdSecretaria) {
            return redirect()
                ->route('ProcessosCargos')
                ->with('mensagemError', 'Cargo não pertence à secretaria do usuário logado.');
        }

        $returnUrl = $config['returnTo'] ? str_replace('~', '/', $config['returnTo']) : 'ProcessosSeletivos';

        return view('Admin/layoutDash', [
            'camada1'       => $config['camada1'],
            'camada2'       => $config['camada2'],
            'pagina'        => $config['page'],
            'acao'          => $config['acao'],
            'dados'         => $dados,
            'fk_id_processo'=> $idProcesso,
            'fk_id_cargo'   => $idCargo,
            'processo'      => $processo,
            'cargo'         => $cargo,
            'returnUrl'     => $returnUrl,
            'titulo'        => ($config['acao'] === 'update' ? 'Alterar' : 'Cadastrar') . ' Vínculo Processo × Cargo',
            'user'          => session('nome'),
        ]);
    }

    /* =============================================================
     * TELA DE CLASSIFICATÓRIOS DO VÍNCULO PROCESSO × CARGO
     ============================================================= */
    public function classificatorios(int $idProcesso, int $idCargo, $camada1 = 'pages', $camada2 = 'cargos', $page = 'ProcessosClassificatorios')
    {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $fkIdSecretaria = (int) session('fk_id_secretaria');
        if ($fkIdSecretaria <= 0) {
            return redirect()
                ->route('Dashboard')
                ->with('mensagemError', 'Secretaria não identificada na sessão.');
        }

        $processo = $this->processosModel->find($idProcesso);
        if (!$processo) {
            return redirect()
                ->route('ProcessosSeletivos')
                ->with('mensagemError', 'Processo seletivo não encontrado.');
        }

        $cargo = $this->cargosModel->find($idCargo);
        if (!$cargo) {
            return redirect()
                ->route('ProcessosCargos')
                ->with('mensagemError', 'Cargo não encontrado.');
        }

        if ($cargo->fk_id_secretaria != $fkIdSecretaria) {
            return redirect()
                ->route('ProcessosCargos')
                ->with('mensagemError', 'Cargo não pertence à secretaria do usuário logado.');
        }

        return view('Admin/layoutDash', [
            'camada1'      => $camada1,
            'camada2'      => $camada2,
            'pagina'       => $page,
            'titulo'       => 'Dados Classificatórios do Cargo',
            'processo'     => $processo,
            'cargo'        => $cargo,
            'returnUrl'    => 'ProcessosCargos/vincular/' . $idProcesso . '/from/alteracao',
            'user'         => session('nome'),
        ]);
    }

    /* =============================================================
     * AÇÕES CRUD
     ============================================================= */
    public function registrar()
    {
        try {
            $fkIdSecretaria = (int) session('fk_id_secretaria');
            if ($fkIdSecretaria <= 0) {
                throw new \Exception('Sua sessão não contém a secretaria vinculada. Faça logout e login novamente.');
            }

            $acao = $this->request->getPost('action') ?? 'create';

            $rules = [
                'fk_id_processo'          => 'required|integer',
                'fk_id_cargo'             => 'required|integer',
                'nm_vagas'                => 'required|integer',
                'nm_vagas_pcd'            => 'permit_empty|integer',
                'nm_cadastro_reserva'     => 'required|integer|in_list[0,1]',
                'ds_requisito_provimento' => 'required|string|max_length[255]',
                'nm_carga_horaria'        => 'required|integer',
                'nm_tipo_carga_horaria'   => 'required|integer|in_list[1,2]',
                'nm_remuneracao'          => 'required',
            ];

            $messages = [
                'fk_id_processo' => [
                    'required' => 'O processo seletivo é obrigatório.',
                    'integer'  => 'O ID do processo deve ser um número inteiro.',
                ],
                'fk_id_cargo' => [
                    'required' => 'O cargo é obrigatório.',
                    'integer'  => 'O ID do cargo deve ser um número inteiro.',
                ],
                'nm_vagas' => [
                    'required' => 'O número de vagas é obrigatório.',
                    'integer'  => 'O número de vagas deve ser um número inteiro.',
                ],
                'nm_cadastro_reserva' => [
                    'required' => 'O campo cadastro reserva é obrigatório.',
                    'integer'  => 'O campo cadastro reserva deve ser um número inteiro.',
                    'in_list'  => 'O campo cadastro reserva deve ser Sim ou Não.',
                ],
                'ds_requisito_provimento' => [
                    'required'   => 'O requisito de provimento é obrigatório.',
                    'max_length' => 'O requisito de provimento deve ter no máximo 255 caracteres.',
                ],
                'nm_carga_horaria' => [
                    'required' => 'A carga horária é obrigatória.',
                    'integer'  => 'A carga horária deve ser um número inteiro.',
                ],
                'nm_tipo_carga_horaria' => [
                    'required' => 'O tipo de carga horária é obrigatório.',
                    'integer'  => 'O tipo de carga horária deve ser um número inteiro.',
                    'in_list'  => 'O tipo de carga horária deve ser SEMANAIS ou MENSAIS.',
                ],
                'nm_remuneracao' => [
                    'required' => 'A remuneração é obrigatória.',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $remuneracao = $this->converterMoedaParaDecimal($this->request->getPost('nm_remuneracao'));

            $dto = new ProcessosCargosDTO(
                pkIdProcessoCargo: $acao === 'update' ? (int) $this->request->getPost('pk_id_processo_cargo') : null,
                fkIdProcesso: (int) $this->request->getPost('fk_id_processo'),
                fkIdCargo: (int) $this->request->getPost('fk_id_cargo'),
                nmVagas: (int) $this->request->getPost('nm_vagas'),
                nmVagasPcd: $this->request->getPost('nm_vagas_pcd') ? (int) $this->request->getPost('nm_vagas_pcd') : null,
                nmCadastroReserva: (int) $this->request->getPost('nm_cadastro_reserva'),
                dsRequisitoProvimento: (string) $this->request->getPost('ds_requisito_provimento'),
                nmCargaHoraria: (int) $this->request->getPost('nm_carga_horaria'),
                nmTipoCargaHoraria: (int) $this->request->getPost('nm_tipo_carga_horaria'),
                nmRemuneracao: $remuneracao,
            );

            if ($acao === 'update' && $dto->pkIdProcessoCargo !== null) {
                $this->processosCargosService->atualizar($dto->pkIdProcessoCargo, $dto);
                $mensagem = 'Registro atualizado com sucesso';
            } else {
                $this->processosCargosService->salvar($dto);
                $mensagem = 'Registro salvo com sucesso';
            }

            $returnTo = $this->request->getPost('return_to');
            $returnUrl = $returnTo ? str_replace('~', '/', $returnTo) : 'ProcessosCargos/vincular/' . $dto->fkIdProcesso;

            return redirect()
                ->to(base_url_adm($returnUrl))
                ->with('mensagemSuccess', $mensagem);

        } catch (\Exception $e) {
            log_message('error', '[ProcessosCargos::registrar] ERRO: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('mensagemError', $e->getMessage());
        }
    }

    public function deletar()
    {
        try {
            $id = (int) $this->request->getPost('chavePrimaria');

            $vinculo = $this->processosCargosService->buscarPorId($id);
            $processoId = $vinculo ? (int) $vinculo->fk_id_processo : null;

            $this->processosCargosService->deletar($id);

            if ($processoId) {
                $referer = $this->request->getServer('HTTP_REFERER', FILTER_SANITIZE_URL) ?? '';
                $fromAlteracao = str_contains($referer, '/from/alteracao');
                $returnPath = $fromAlteracao
                    ? 'ProcessosSeletivos/formularioAlteracao/' . $processoId
                    : 'ProcessosCargos/vincular/' . $processoId . '/from/lista';

                return redirect()
                    ->to(base_url_adm($returnPath))
                    ->with('mensagemSuccess', 'Registro excluído com sucesso');
            }

            return redirect()
                ->route('ProcessosSeletivos')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');
        } catch (\Throwable $e) {
            return redirect()
                ->route('ProcessosSeletivos')
                ->with('mensagemError', $e->getMessage());
        }
    }

    /**
     * Retorna os cargos vinculados a um processo (JSON).
     */
    public function buscarPorProcesso(int $processoId)
    {
        try {
            $cargos = $this->processosCargosService->buscarPorProcesso($processoId);
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $cargos,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /* =====================================================
     * CONVERSÃO DE MOEDA PARA DECIMAL
     * ===================================================== */
    private function converterMoedaParaDecimal(?string $valor): float
    {
        if (empty($valor)) {
            return 0.0;
        }

        // Remove o prefixo R$, espaços e pontos de milhar; troca vírgula decimal por ponto
        $limpo = preg_replace('/[^\d,]/', '', $valor);
        $limpo = str_replace('.', '', $limpo);
        $limpo = str_replace(',', '.', $limpo);

        return (float) $limpo;
    }

    /* =====================================================
     * VALIDAÇÕES AUXILIARES
     * ===================================================== */
    private function validarSessao(): void
    {
        if (!checklogged()) {
            redirect()->route('home')->with('error', 'Sua sessão expirou')->send();
            exit;
        }
    }

    private function validarView(string $camada1, string $camada2, string $page): void
    {
        if (!is_file(APPPATH . "Views/Admin/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw new PageNotFoundException("Página não encontrada: {$page}");
        }
    }
}
