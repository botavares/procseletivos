<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\EscolaridadesModel;
use App\Models\NiveisEscolaridadeModel;
use App\Services\Escolaridades\EscolaridadeService;
use App\Services\Escolaridades\EscolaridadeGridService;

class Escolaridades extends BaseController
{
    protected $escolaridadesModel;
    protected $niveisEscolaridadeModel;

    public function __construct() {
        $this->escolaridadesModel       = new \App\Models\EscolaridadesModel();
        $this->niveisEscolaridadeModel = new \App\Models\NiveisEscolaridadeModel();
    }

    public function index($camada1 = '', $camada2 = 'pages', $page = 'Escolaridades')
    {
        return $this->listarParaGrid($camada1, $camada2, $page);
    }

    private function listarParaGrid(string $camada1, string $camada2, string $page)
    {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $grid = service('escolaridadeGridService')->escolaridades();

        if (!is_array($grid) || !isset($grid['data'], $grid['columns'])) {
            throw new \RuntimeException('Grid retornado em formato inválido');
        }

        return view('layoutDash', [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'tipo'          => 'escolaridades',
            'titulo'        => 'Gerenciar Escolaridades',
            'escolaridades' => $grid['data'],
            'titulosTabela' => $grid['columns'],
            'user'          => session('nome'),
        ]);
    }

    public function formularioCadastro($camada1 = 'pages', $camada2 = 'cadastros', $page = 'formEscolaridades')
    {
        return $this->renderFormulario([
            'acao'     => 'create',
            'camada1'  => $camada1,
            'camada2'  => $camada2,
            'page'     => $page,
        ]);
    }

    public function formularioAlteracao($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formEscolaridades')
    {
        return $this->renderFormulario([
            'id'       => $id,
            'acao'     => 'update',
            'camada1'  => $camada1,
            'camada2'  => $camada2,
            'page'     => $page,
        ]);
    }

    private function renderFormulario(array $config)
    {
        $config = array_merge([
            'id'      => null,
            'camada1' => 'pages',
            'camada2' => 'cadastros',
            'page'    => 'formEscolaridades',
        ], $config);

        $this->validarView($config['camada1'], $config['camada2'], $config['page']);
        $this->validarSessao();

        $dados = null;
        if ($config['acao'] === 'update' && $config['id']) {
            $dados = $this->escolaridadesModel->find($config['id']);
        }

        $niveis = $this->niveisEscolaridadeModel->findAll();

        $viewData = [
            'camada1' => $config['camada1'],
            'camada2' => $config['camada2'],
            'pagina'  => $config['page'],
            'acao'    => $config['acao'],
            'dados'   => $dados,
            'niveis'  => $niveis,
            'user'    => session('nome'),
        ];

        $padroes = ['id', 'acao', 'camada1', 'camada2', 'page', 'titulo'];
        foreach ($config as $chave => $valor) {
            if (!in_array($chave, $padroes, true)) {
                $viewData[$chave] = $valor;
            }
        }

        return view('layoutDash', $viewData);
    }

    public function registrar()
    {
        try {
            $data = [
                'pk_id_escolaridade'  => $this->request->getPost('pk_id_escolaridade'),
                'ds_nome_escolaridade' => $this->request->getPost('ds_nome_escolaridade'),
                'ds_instrucao'        => $this->request->getPost('ds_instrucao'),
                'fk_id_nivel'         => $this->request->getPost('fk_id_nivel'),
            ];
            $acao = $this->request->getPost('action') ?? 'create';

            if (empty($data['ds_nome_escolaridade'])) {
                throw new \Exception('O nome da escolaridade deve ser informado.');
            }
            if (empty($data['fk_id_nivel'])) {
                throw new \Exception('O nível deve ser selecionado.');
            }

            $service = service('escolaridadeService');

            if ($acao === 'update') {
                $service->atualizar((int)$data['pk_id_escolaridade'], $data);
            } else {
                $id = $service->salvar($data);
            }

            return redirect()->route('Escolaridades')
                ->with('mensagemSuccess', 'Registro salvo com sucesso');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['erro' => $e->getMessage()]);
        }
    }

    public function deletar()
    {
        try {
            $id = (int) $this->request->getPost('chavePrimaria');

            if ($id <= 0) {
                throw new \InvalidArgumentException('Escolaridade inválida');
            }

            service('escolaridadeService')->deletar($id);

            return redirect()
                ->route('Escolaridades')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->route('Escolaridades')
                ->with('mensagemError', $e->getMessage());
        }
    }

    private function validarSessao(): void
    {
        if (!checklogged()) {
            redirect()->route('home')->with('error', 'Sua sessão expirou')->send();
            exit;
        }
    }

    private function validarView(string $camada1, string $camada2, string $page): void
    {
        if (!is_file(APPPATH . "Views/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw new PageNotFoundException("Página não encontrada: {$page}");
        }
    }
}
