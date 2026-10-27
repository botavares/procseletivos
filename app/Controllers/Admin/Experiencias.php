<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\Admin\ExperienciasModel;
use App\Services\Admin\Experiencias\ExperienciaService;
use App\Services\Admin\Experiencias\ExperienciaGridService;

class Experiencias extends BaseController
{
    protected $experienciasModel;

    public function __construct() {
        $this->experienciasModel = new \App\Models\Admin\ExperienciasModel();
    }

    public function index($camada1 = '', $camada2 = 'pages', $page = 'Experiencias')
    {
        return $this->listarParaGrid($camada1, $camada2, $page);
    }

    private function listarParaGrid(string $camada1, string $camada2, string $page)
    {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $grid = service('experienciaGridService')->experiencias();

        if (!is_array($grid) || !isset($grid['data'], $grid['columns'])) {
            throw new \RuntimeException('Grid retornado em formato inválido');
        }

        return view('Admin/layoutDash', [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'tipo'          => 'experiencias',
            'titulo'        => 'Gerenciar Experiências',
            'experiencias'  => $grid['data'],
            'titulosTabela' => $grid['columns'],
            'user'          => session('nome'),
        ]);
    }

    public function formularioCadastro($camada1 = 'pages', $camada2 = 'cadastros', $page = 'formExperiencias')
    {
        return $this->renderFormulario([
            'acao'     => 'create',
            'camada1'  => $camada1,
            'camada2'  => $camada2,
            'page'     => $page,
        ]);
    }

    public function formularioAlteracao($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formExperiencias')
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
            'page'    => 'formExperiencias',
        ], $config);

        $this->validarView($config['camada1'], $config['camada2'], $config['page']);
        $this->validarSessao();

        $dados = null;
        if ($config['acao'] === 'update' && $config['id']) {
            $dados = $this->experienciasModel->find($config['id']);
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

        return view('Admin/layoutDash', $viewData);
    }

    public function registrar()
    {
        try {
            $data = [
                'pk_id_experiencia'  => $this->request->getPost('pk_id_experiencia'),
                'ds_nome_experiencia' => $this->request->getPost('ds_nome_experiencia'),
                'ds_tipo_experiencia' => $this->request->getPost('ds_tipo_experiencia'),
            ];
            $acao = $this->request->getPost('action') ?? 'create';

            if (empty($data['ds_nome_experiencia'])) {
                throw new \Exception('O nome da experiência deve ser informado.');
            }
            if (empty($data['ds_tipo_experiencia'])) {
                throw new \Exception('O tipo de experiência deve ser informado.');
            }

            $service = service('adminExperienciaService');

            if ($acao === 'update') {
                $service->atualizar((int)$data['pk_id_experiencia'], $data);
            } else {
                $id = $service->salvar($data);
            }

            return redirect()->route('Experiencias')
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
                throw new \InvalidArgumentException('Experiência inválida');
            }

            service('adminExperienciaService')->deletar($id);

            return redirect()
                ->route('Experiencias')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->route('Experiencias')
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
        if (!is_file(APPPATH . "Views/Admin/{$camada1}/{$camada2}/{$page}_view.php")) {
            throw new PageNotFoundException("Página não encontrada: {$page}");
        }
    }
}
