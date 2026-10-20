<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\AperfeicoamentosModel;
use App\Services\Aperfeicoamentos\AperfeicoamentoService;
use App\Services\Aperfeicoamentos\AperfeicoamentoGridService;

class Aperfeicoamentos extends BaseController
{
    protected $aperfeicoamentosModel;

    public function __construct() {
        $this->aperfeicoamentosModel = new \App\Models\AperfeicoamentosModel();
    }

    public function index($camada1 = '', $camada2 = 'pages', $page = 'Aperfeicoamentos')
    {
        return $this->listarParaGrid($camada1, $camada2, $page);
    }

    private function listarParaGrid(string $camada1, string $camada2, string $page)
    {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $grid = service('aperfeicoamentoGridService')->aperfeicoamentos();

        if (!is_array($grid) || !isset($grid['data'], $grid['columns'])) {
            throw new \RuntimeException('Grid retornado em formato inválido');
        }

        return view('layoutDash', [
            'camada1'         => $camada1,
            'camada2'         => $camada2,
            'pagina'          => $page,
            'tipo'            => 'aperfeicoamentos',
            'titulo'          => 'Gerenciar Cursos de Aperfeiçoamento',
            'aperfeicoamentos' => $grid['data'],
            'titulosTabela'   => $grid['columns'],
            'user'            => session('nome'),
        ]);
    }

    public function formularioCadastro($camada1 = 'pages', $camada2 = 'cadastros', $page = 'formAperfeicoamentos')
    {
        return $this->renderFormulario([
            'acao'     => 'create',
            'camada1'  => $camada1,
            'camada2'  => $camada2,
            'page'     => $page,
        ]);
    }

    public function formularioAlteracao($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formAperfeicoamentos')
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
            'page'    => 'formAperfeicoamentos',
        ], $config);

        $this->validarView($config['camada1'], $config['camada2'], $config['page']);
        $this->validarSessao();

        $dados = null;
        if ($config['acao'] === 'update' && $config['id']) {
            $dados = $this->aperfeicoamentosModel->find($config['id']);
        }

        $viewData = [
            'camada1' => $config['camada1'],
            'camada2' => $config['camada2'],
            'pagina'  => $config['page'],
            'acao'    => $config['acao'],
            'dados'   => $dados,
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
                'pk_id_curso'   => $this->request->getPost('pk_id_curso'),
                'ds_nome_curso' => $this->request->getPost('ds_nome_curso'),
            ];
            $acao = $this->request->getPost('action') ?? 'create';

            if (empty($data['ds_nome_curso'])) {
                throw new \Exception('O nome do curso deve ser informado.');
            }

            $service = service('aperfeicoamentoService');

            if ($acao === 'update') {
                $service->atualizar((int)$data['pk_id_curso'], $data);
            } else {
                $id = $service->salvar($data);
            }

            return redirect()->route('Aperfeicoamentos')
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
                throw new \InvalidArgumentException('Curso de aperfeiçoamento inválido');
            }

            service('aperfeicoamentoService')->deletar($id);

            return redirect()
                ->route('Aperfeicoamentos')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->route('Aperfeicoamentos')
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
