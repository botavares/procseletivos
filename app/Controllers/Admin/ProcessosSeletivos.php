<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\DTOs\Admin\ProcessosSeletivosDTO;
use App\Enums\Admin\SituacaoProcessosEnum;
use App\Services\ProcessosSeletivos\ProcessosSeletivosService;
use App\Utils\Formatador;

class ProcessosSeletivos extends BaseController
{
    public function __construct(
        protected ?ProcessosSeletivosService $service = null,
    ) {
        $this->service ??= service('processosSeletivosService');
    }

    /* =============================================================
     * LISTAGEM (GRID INICIAL)
     ============================================================= */
    public function index($camada1 = '', $camada2 = 'pages', $page = 'ProcessosSeletivos')
    {
        return $this->listarParaGrid($camada1, $camada2, $page);
    }

    private function listarParaGrid(string $camada1, string $camada2, string $page)
    {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $fkIdSecretaria = (int) session('fk_id_secretaria');
        if ($fkIdSecretaria <= 0) {
            return redirect()
                ->route('Dashboard')
                ->with('mensagemError', 'Secretaria não identificada na sessão.');
        }

        $processos = $this->service->listarPorSecretaria($fkIdSecretaria);

        // Enriquecer com label de situação
        foreach ($processos as $proc) {
            $enum = SituacaoProcessosEnum::tryFrom((int) $proc->nm_situacao);
            $proc->situacao_label = $enum?->label() ?? 'Desconhecido';
        }

        return view('Admin/layoutDash', [
            'camada1'   => $camada1,
            'camada2'   => $camada2,
            'pagina'    => $page,
            'titulo'    => 'Gerenciar Processos Seletivos',
            'processos' => $processos,
            'user'      => session('nome'),
        ]);
    }

    /* =============================================================
     * FORMULÁRIOS
     ============================================================= */
    public function formularioCadastro($camada1 = 'pages', $camada2 = 'cadastros', $page = 'formProcessosSeletivos')
    {
        return $this->renderFormulario([
            'acao'    => 'create',
            'camada1' => $camada1,
            'camada2' => $camada2,
            'page'    => $page,
        ]);
    }

    public function formularioAlteracao($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formProcessosSeletivos')
    {
        return $this->renderFormulario([
            'id'      => $id,
            'acao'    => 'update',
            'camada1' => $camada1,
            'camada2' => $camada2,
            'page'    => $page,
        ]);
    }

    private function renderFormulario(array $config)
    {
        $config = array_merge([
            'id'      => null,
            'acao'    => 'create',
            'camada1' => 'pages',
            'camada2' => 'cadastros',
            'page'    => 'formProcessosSeletivos',
        ], $config);

        $this->validarView($config['camada1'], $config['camada2'], $config['page']);
        $this->validarSessao();

        $dados = null;
        if ($config['acao'] === 'update' && $config['id']) {
            $dados = $this->service->buscarPorId((int) $config['id']);
            if (!$dados) {
                return redirect()
                    ->route('AdminProcessosSeletivos')
                    ->with('mensagemError', 'Processo Seletivo não encontrado.');
            }
        }

        return view('Admin/layoutDash', [
            'camada1' => $config['camada1'],
            'camada2' => $config['camada2'],
            'pagina'  => $config['page'],
            'acao'    => $config['acao'],
            'dados'   => $dados,
            'titulo'  => ($config['acao'] === 'update' ? 'Alterar' : 'Cadastrar') . ' Processo Seletivo',
            'user'    => session('nome'),
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

            $acao = $this->request->getPost('acao') ?? 'create';

            $rules = [
                'nm_numero_processo'   => 'required|integer',
                'nm_ano_processo'      => 'required|integer|ano_atual_ou_futuro',
                'ds_objeto'            => 'required|string|max_length[255]',
                'nm_situacao'          => 'required|integer|in_list[1,2,3,4,5,6]',
                'dt_inicio_inscricao'  => 'required|valid_date[d/m/Y]',
                'dt_fim_inscricao'     => 'required|valid_date[d/m/Y]|data_fim_maior_igual_inicio[dt_inicio_inscricao]',
                'dt_divulga_resultado' => 'permit_empty|valid_date[d/m/Y]',
                'nm_meses_validade'    => 'required|integer',
                'nm_prorrogado'        => 'required|integer|in_list[0,1]',
            ];

            $messages = [
                'nm_numero_processo' => [
                    'required' => 'O campo Número do Processo é obrigatório.',
                    'integer'  => 'O campo Número do Processo deve ser um número inteiro.',
                ],
                'nm_ano_processo' => [
                    'required'           => 'O campo Ano do Processo é obrigatório.',
                    'integer'              => 'O campo Ano do Processo deve ser um número inteiro.',
                    'ano_atual_ou_futuro'  => 'O campo Ano do Processo deve ser igual ou maior que o ano atual.',
                ],
                'ds_objeto' => [
                    'required'   => 'O campo Objeto é obrigatório.',
                    'max_length' => 'O campo Objeto deve ter no máximo 255 caracteres.',
                ],
                'nm_situacao' => [
                    'required' => 'O campo Situação é obrigatório.',
                    'integer'  => 'O campo Situação deve ser um número inteiro.',
                    'in_list'  => 'O campo Situação deve ser um valor válido.',
                ],
                'dt_inicio_inscricao' => [
                    'required'   => 'O campo Início das Inscrições é obrigatório.',
                    'valid_date' => 'O campo Início das Inscrições deve ser uma data válida (dd/mm/AAAA).',
                ],
                'dt_fim_inscricao' => [
                    'required'                      => 'O campo Fim das Inscrições é obrigatório.',
                    'valid_date'                    => 'O campo Fim das Inscrições deve ser uma data válida (dd/mm/AAAA).',
                    'data_fim_maior_igual_inicio'   => 'O campo Fim das Inscrições não pode ser anterior ao Início das Inscrições.',
                ],
                'dt_divulga_resultado' => [
                    'valid_date' => 'O campo Divulgação do Resultado deve ser uma data válida (dd/mm/AAAA).',
                ],
                'nm_meses_validade' => [
                    'required' => 'O campo Meses de Validade é obrigatório.',
                    'integer'  => 'O campo Meses de Validade deve ser um número inteiro.',
                ],
                'nm_prorrogado' => [
                    'required' => 'O campo Prorrogado é obrigatório.',
                    'integer'  => 'O campo Prorrogado deve ser um número inteiro.',
                    'in_list'  => 'O campo Prorrogado deve ser um valor válido.',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $dtInicio = Formatador::formatarDataBrParaIso($this->request->getPost('dt_inicio_inscricao'));
            $dtFim    = Formatador::formatarDataBrParaIso($this->request->getPost('dt_fim_inscricao'));
            $dtDivulga = Formatador::formatarDataBrParaIso($this->request->getPost('dt_divulga_resultado'));

            if ($dtInicio === null || $dtFim === null) {
                throw new \Exception('Datas de inscrição inválidas.');
            }

            $dto = new ProcessosSeletivosDTO(
                idProcesso: $acao === 'update' ? (int) $this->request->getPost('pk_id_processo') : null,
                idSecretaria: $fkIdSecretaria,
                numeroProcesso: (int) $this->request->getPost('nm_numero_processo'),
                anoProcesso: (int) $this->request->getPost('nm_ano_processo'),
                objeto: (string) $this->request->getPost('ds_objeto'),
                situacao: (int) $this->request->getPost('nm_situacao'),
                inicioInscricao: $dtInicio,
                fimInscricao: $dtFim,
                divulgaResultado: $dtDivulga,
                mesesValidade: (int) $this->request->getPost('nm_meses_validade'),
                prorrogado: (int) $this->request->getPost('nm_prorrogado'),
            );

            if ($acao === 'update' && $dto->idProcesso) {
                $sucesso = $this->service->atualizar($dto->idProcesso, $dto);
                if (!$sucesso) {
                    throw new \Exception('Falha ao atualizar o registro. Verifique se o ID existe.');
                }
                $id = $dto->idProcesso;
            } else {
                $id = $this->service->criar($dto);
            }

            return redirect()
                ->to(base_url_adm("ProcessosSeletivos/formularioAlteracao/{$id}"))
                ->with('mensagemSuccess', 'Registro salvo com sucesso');

        } catch (\Exception $e) {
            log_message('error', '[ProcessosSeletivos::registrar] ERRO: ' . $e->getMessage());
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

            if ($id <= 0) {
                throw new \InvalidArgumentException('Processo Seletivo inválido');
            }

            $this->service->deletar($id);

            return redirect()
                ->route('AdminProcessosSeletivos')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->route('AdminProcessosSeletivos')
                ->with('mensagemError', $e->getMessage());
        }
    }

    /* =============================================================
     * CONSULTAS PÚBLICAS (API)
     ============================================================= */
    public function buscarPorAno(int $ano)
    {
        return $this->response->setJSON($this->service->listarPorAno($ano));
    }

    public function buscarPorSituacao(int $situacao)
    {
        return $this->response->setJSON($this->service->listarPorSituacao($situacao));
    }

    public function buscarPorFaixaDeTempo(string $inicio, string $fim)
    {
        return $this->response->setJSON($this->service->listarPorFaixaDeTempo($inicio, $fim));
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
