<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\DTOs\Admin\ProcessosDocumentosDTO;
use App\Enums\Admin\TiposDocumentosEnum;
use App\Services\ProcessosDocumentos\ProcessosDocumentosService;
use App\Models\ProcessosSeletivosModel;
use App\Models\Admin\SecretariasModel;

class ProcessosDocumentos extends BaseController
{
    public function __construct(
        protected ?ProcessosDocumentosService $service = null,
        protected ?ProcessosSeletivosModel $processosModel = null,
        protected ?SecretariasModel $secretariasModel = null,
    ) {
        $this->service ??= service('processosDocumentosService');
        $this->processosModel ??= new ProcessosSeletivosModel();
        $this->secretariasModel ??= new SecretariasModel();
    }

    /* =============================================================
     * LISTAGEM (GRID INICIAL)
     ============================================================= */
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Documentos')
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

        $documentos = $this->service->listarPorSecretaria($fkIdSecretaria);

        // Enriquecer com label do tipo
        foreach ($documentos as $doc) {
            $enum = TiposDocumentosEnum::tryFrom((int) $doc->nm_tipo);
            $doc->tipo_label = $enum?->label() ?? 'Desconhecido';
        }

        return view('Admin/layoutDash', [
            'camada1'    => $camada1,
            'camada2'    => $camada2,
            'pagina'     => $page,
            'titulo'     => 'Gerenciar Documentos',
            'documentos' => $documentos,
            'user'       => session('nome'),
        ]);
    }

    /* =============================================================
     * FORMULÁRIOS
     ============================================================= */
    public function formularioCadastro($idProcesso = null, $returnTo = null, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formProcessosDocumentos')
    {
        return $this->renderFormulario([
            'acao'        => 'create',
            'idProcesso'  => $idProcesso,
            'returnTo'    => $returnTo,
            'camada1'     => $camada1,
            'camada2'     => $camada2,
            'page'        => $page,
        ]);
    }

    public function formularioAlteracao($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formProcessosDocumentos')
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
            'id'          => null,
            'idProcesso'  => null,
            'acao'        => 'create',
            'camada1'     => 'pages',
            'camada2'     => 'cadastros',
            'page'        => 'formProcessosDocumentos',
            'returnTo'    => null,
        ], $config);

        $this->validarView($config['camada1'], $config['camada2'], $config['page']);
        $this->validarSessao();

        $fkIdSecretaria = (int) session('fk_id_secretaria');
        if ($fkIdSecretaria <= 0) {
            return redirect()
                ->route('Dashboard')
                ->with('mensagemError', 'Secretaria não identificada na sessão.');
        }

        $dados = null;
        if ($config['acao'] === 'update' && $config['id']) {
            $dados = $this->service->buscarPorId((int) $config['id']);
            if (!$dados) {
                return redirect()
                    ->route('ProcessosDocumentos')
                    ->with('mensagemError', 'Documento não encontrado.');
            }
        }

        $processos = $this->processosModel->porSecretaria($fkIdSecretaria);

        $returnUrl = $config['returnTo'] ? str_replace('~', '/', $config['returnTo']) : 'ProcessosDocumentos';

        return view('Admin/layoutDash', [
            'camada1'     => $config['camada1'],
            'camada2'     => $config['camada2'],
            'pagina'      => $config['page'],
            'acao'        => $config['acao'],
            'dados'       => $dados,
            'processos'   => $processos,
            'idProcesso'  => $config['idProcesso'],
            'returnUrl'   => $returnUrl,
            'titulo'      => ($config['acao'] === 'update' ? 'Alterar' : 'Cadastrar') . ' Documento',
            'user'        => session('nome'),
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
                'fk_processo_referencia'  => 'required|integer',
                'nm_tipo'                 => 'required|integer|in_list[1,2,3,4,5,6,7]',
                'nm_numero_documento'     => 'required|integer|apenas_numeros',
                'nm_ano_documento'        => 'required|integer|ano_atual_ou_futuro',
                'ds_codigo_oficial'       => 'required|string|max_length[255]',
                'ds_arquivo'              => 'permit_empty|arquivo_pdf_condicional',
                'nm_versao_vigente'       => 'required|integer|versao_vigente_regra',
                'nm_documento_retificado' => 'required|integer|documento_retificado_regra',
            ];

            $messages = [
                'fk_processo_referencia' => [
                    'required' => 'O campo Processo de Referência é obrigatório.',
                    'integer'  => 'O campo Processo de Referência deve ser um número inteiro.',
                ],
                'nm_tipo' => [
                    'required' => 'O campo Tipo é obrigatório.',
                    'integer'  => 'O campo Tipo deve ser um número inteiro.',
                    'in_list'  => 'O campo Tipo deve ser um valor válido (1 a 7).',
                ],
                'nm_numero_documento' => [
                    'required'      => 'O campo Número do Documento é obrigatório.',
                    'integer'         => 'O campo Número do Documento deve ser um número inteiro.',
                    'apenas_numeros'  => 'O campo Número do Documento aceita apenas números.',
                ],
                'nm_ano_documento' => [
                    'required'           => 'O campo Ano do Documento é obrigatório.',
                    'integer'              => 'O campo Ano do Documento deve ser um número inteiro.',
                    'ano_atual_ou_futuro'  => 'O campo Ano do Documento deve ser igual ou maior que o ano atual.',
                ],
                'ds_codigo_oficial' => [
                    'required'   => 'O campo Código Oficial é obrigatório.',
                    'max_length' => 'O campo Código Oficial deve ter no máximo 255 caracteres.',
                ],
                'ds_arquivo' => [
                    'arquivo_pdf_condicional' => 'O arquivo PDF é obrigatório para Edital de Abertura, Retificação e Republicação Consolidada.',
                ],
                'nm_versao_vigente' => [
                    'required'            => 'O campo Versão Vigente é obrigatório.',
                    'integer'             => 'O campo Versão Vigente deve ser um número inteiro.',
                    'versao_vigente_regra' => 'Retificação não permite valor em Versão Vigente.',
                ],
                'nm_documento_retificado' => [
                    'required'                => 'O campo Documento Retificado é obrigatório.',
                    'integer'                 => 'O campo Documento Retificado deve ser um número inteiro.',
                    'documento_retificado_regra' => 'Documento Retificado só pode ter valor quando o Tipo for Retificação.',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $fkProcessoReferencia = (int) $this->request->getPost('fk_processo_referencia');
            $tipoDocumento = (int) $this->request->getPost('nm_tipo');
            $idDocumentoAtual = $acao === 'update' ? (int) $this->request->getPost('pk_id_documento') : null;

            // Validar unicidade de Edital de Abertura por processo
            if ($tipoDocumento === TiposDocumentosEnum::EDITAL_ABERTURA->value) {
                if ($this->service->existeEditalAbertura($fkProcessoReferencia, $idDocumentoAtual)) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('mensagemError', 'Ja existe um Edital de Abertura para este processo seletivo.');
                }
            }

            // Tratar upload de arquivo - salva em subpastas por tipo
            $arquivoNome = '';
            $arquivoAtual = $acao === 'update' ? ($this->request->getPost('ds_arquivo_atual') ?? '') : '';
            $arquivoUpload = $this->request->getFile('ds_arquivo');
            $tipoDocumento = (int) $this->request->getPost('nm_tipo');

            if ($arquivoUpload && $arquivoUpload->isValid() && !$arquivoUpload->hasMoved()) {
                // Buscar dados do processo e secretaria para nomenclatura do arquivo
                $processo = $this->processosModel->find($fkProcessoReferencia);
                $siglaSecretaria = '';
                if ($processo) {
                    $secretaria = $this->secretariasModel->find($processo->fk_id_secretaria);
                    if ($secretaria) {
                        $siglaSecretaria = $secretaria->ds_sigla_secretaria ?? '';
                    }
                }

                $nmDocumento = (int) $this->request->getPost('nm_numero_documento');
                $nmAno = (int) $this->request->getPost('nm_ano_documento');
                $nmProcesso = (int) ($processo->nm_numero_processo ?? 0);
                $anoProcesso = (int) ($processo->nm_ano_processo ?? 0);

                $novoNome = $this->gerarNomeArquivo(
                    $tipoDocumento,
                    $nmDocumento,
                    $nmAno,
                    $nmProcesso,
                    $anoProcesso,
                    $siglaSecretaria
                );

                // Mapear tipo para pasta
                $pastas = [
                    TiposDocumentosEnum::EDITAL_ABERTURA->value   => 'original',
                    TiposDocumentosEnum::RETIFICACAO->value       => 'retificacoes',
                    TiposDocumentosEnum::RESULTADO_PARCIAL->value => 'republicacoes',
                    TiposDocumentosEnum::HOMOLOGACAO->value       => 'republicacoes',
                    TiposDocumentosEnum::EDITAL_CONVOCACAO->value => 'republicacoes',
                    TiposDocumentosEnum::COMUNICADO->value        => 'anexos',
                    TiposDocumentosEnum::REPUBLICACAO->value      => 'republicacoes',
                ];
                $pasta = $pastas[$tipoDocumento] ?? 'anexos';
                $diretorio = WRITEPATH . 'uploads/editais/' . $pasta;
                if (!is_dir($diretorio)) {
                    mkdir($diretorio, 0755, true);
                }
                $arquivoUpload->move($diretorio, $novoNome);
                $arquivoNome = $novoNome;
            } else {
                $arquivoNome = $arquivoAtual;
            }

            $dto = new ProcessosDocumentosDTO(
                idDocumento: $acao === 'update' ? (int) $this->request->getPost('pk_id_documento') : null,
                processoReferencia: $fkProcessoReferencia,
                tipo: $tipoDocumento,
                numeroDocumento: (int) $this->request->getPost('nm_numero_documento'),
                anoDocumento: (int) $this->request->getPost('nm_ano_documento'),
                codigoOficial: (string) $this->request->getPost('ds_codigo_oficial'),
                link: '',
                arquivo: $arquivoNome,
                versaoVigente: (int) $this->request->getPost('nm_versao_vigente'),
                documentoRetificado: (int) $this->request->getPost('nm_documento_retificado'),
                dataPublicacao: date('Y-m-d H:i:s'),
            );

            if ($acao === 'update' && $dto->idDocumento) {
                $sucesso = $this->service->atualizar($dto->idDocumento, $dto);
                if (!$sucesso) {
                    throw new \Exception('Falha ao atualizar o registro. Verifique se o ID existe.');
                }
            } else {
                $id = $this->service->criar($dto);
            }

            return redirect()
                ->route('ProcessosDocumentos')
                ->with('mensagemSuccess', 'Registro salvo com sucesso');

        } catch (\Exception $e) {
            log_message('error', '[ProcessosDocumentos::registrar] ERRO: ' . $e->getMessage());
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
                throw new \InvalidArgumentException('Documento inválido');
            }

            $this->service->deletar($id);

            return redirect()
                ->route('ProcessosDocumentos')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->route('ProcessosDocumentos')
                ->with('mensagemError', $e->getMessage());
        }
    }

    /* =============================================================
     * CONSULTAS PÚBLICAS (API)
     ============================================================= */
    public function buscarPorTipo(int $tipo)
    {
        return $this->response->setJSON($this->service->listarPorTipo($tipo));
    }

    public function buscarPorNumeroProcesso(int $numero, int $ano)
    {
        return $this->response->setJSON($this->service->listarPorNumeroProcesso($numero, $ano));
    }

    public function buscarPorNumeroDocumentoEAno(int $numero, int $ano)
    {
        return $this->response->setJSON($this->service->listarPorNumeroDocumentoEAno($numero, $ano));
    }

    public function buscarPorFaixaDeTempo(string $inicio, string $fim)
    {
        return $this->response->setJSON($this->service->listarPorFaixaDeTempo($inicio, $fim));
    }

    /* =====================================================
     * GERENCIAMENTO DE NOMENCLATURA DE ARQUIVOS
     * ===================================================== */
    private function gerarNomeArquivo(int $tipo, int $numeroDocumento, int $anoDocumento, int $numeroProcesso, int $anoProcesso, string $siglaSecretaria): string
    {
        $sigla = $this->sanitizarNomeArquivo($siglaSecretaria);

        $slugs = [
            TiposDocumentosEnum::EDITAL_ABERTURA->value   => 'edital_abertura',
            TiposDocumentosEnum::RETIFICACAO->value       => 'retificacao',
            TiposDocumentosEnum::RESULTADO_PARCIAL->value => 'resultado_parcial',
            TiposDocumentosEnum::HOMOLOGACAO->value       => 'homologacao',
            TiposDocumentosEnum::EDITAL_CONVOCACAO->value => 'edital_convocacao',
            TiposDocumentosEnum::COMUNICADO->value        => 'comunicado',
            TiposDocumentosEnum::REPUBLICACAO->value      => 'republicacao',
        ];

        $slug = strtoupper($slugs[$tipo] ?? 'documento');

        $numeroDocFmt = str_pad((string) $numeroDocumento, 2, '0', STR_PAD_LEFT);
        $numeroProcFmt = str_pad((string) $numeroProcesso, 2, '0', STR_PAD_LEFT);

        if ($tipo === TiposDocumentosEnum::EDITAL_ABERTURA->value) {
            return sprintf(
                '%s_PSS_%s_%s_%s.pdf',
                $slug,
                $sigla,
                $numeroProcFmt,
                $anoProcesso
            );
        }

        return sprintf(
            '%s_%s_%s_PSS_%s_%s_%s.pdf',
            $slug,
            $numeroDocFmt,
            $anoDocumento,
            $sigla,
            $numeroProcFmt,
            $anoProcesso
        );
    }

    private function sanitizarNomeArquivo(string $nome): string
    {
        $nome = mb_strtoupper($nome, 'UTF-8');
        // Remove acentos
        $nome = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nome);
        // Remove qualquer caractere que nao seja letra, numero, underscore ou hifen
        $nome = preg_replace('/[^A-Z0-9_-]/', '', $nome);
        return $nome;
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
