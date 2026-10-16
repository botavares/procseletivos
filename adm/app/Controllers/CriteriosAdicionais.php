<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CriteriosAdicionaisModel;
use App\Services\CriteriosAdicionais\CriterioAdicionalFormService;
use App\Services\CriteriosAdicionais\CriterioAdicionalService;
use App\Services\CriteriosAdicionais\CriterioAdicionalGridService;

class CriteriosAdicionais extends BaseController
{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'CriteriosAdicionais')
    {
        return $this->listarParaGrid($camada1, $camada2, $page);
    }

    private function listarParaGrid(string $camada1, string $camada2, string $page)
    {
        $this->validarSessao();
        $this->validarView($camada1, $camada2, $page);

        $gridService = new CriterioAdicionalGridService();
        $grid = $gridService->criterios();

        if (!is_array($grid) || !isset($grid['data'], $grid['columns'])) {
            throw new \RuntimeException('Grid retornado em formato inválido');
        }

        return view('layoutDash', [
            'camada1'        => $camada1,
            'camada2'        => $camada2,
            'pagina'         => $page,
            'tipo'           => 'criteriosadicionais',
            'titulo'         => 'Gerenciar Critérios Adicionais',
            'criterios'      => $grid['data'],
            'titulosTabela'  => $grid['columns'],
            'user'           => session('nome'),
        ]);
    }

    public function formularioCadastro($camada1 = 'pages', $camada2 = 'cadastros', $page = 'formCriteriosAdicionais')
    {
        return $this->renderFormulario([
            'acao'     => 'create',
            'camada1'  => $camada1,
            'camada2'  => $camada2,
            'page'     => $page,
        ]);
    }

    public function formularioAlteracao($id, $camada1 = 'pages', $camada2 = 'cadastros', $page = 'formCriteriosAdicionais')
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
            'page'    => 'formCriteriosAdicionais',
        ], $config);

        $this->validarView($config['camada1'], $config['camada2'], $config['page']);
        $this->validarSessao();

        $dados = null;
        if ($config['acao'] === 'update' && $config['id']) {
            $dados = (new CriteriosAdicionaisModel())->find($config['id']);
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
            $form   = new CriterioAdicionalFormService($this->request);
            $dados  = $form->handle();

            $acao = $dados['acao'];
            $dto = $dados['criterio'];

            $service = new CriterioAdicionalService();

            if ($acao === 'update') {
                $service->atualizar($dto);
            } else {
                $id = $service->salvar($dto);
            }

            return redirect()->route('CriteriosAdicionais')
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
                throw new \InvalidArgumentException('Critério adicional inválido');
            }

            (new CriterioAdicionalService())->deletar($id);

            return redirect()
                ->route('CriteriosAdicionais')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');

        } catch (\Throwable $e) {
            return redirect()
                ->route('CriteriosAdicionais')
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
