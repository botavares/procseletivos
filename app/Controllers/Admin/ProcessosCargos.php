<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\DTOs\Admin\ProcessosCargosDTO;
use App\Services\Admin\ProcessosCargos\ProcessosCargosService;

class ProcessosCargos extends BaseController
{
    protected $processosCargosService;

    public function __construct()
    {
        $this->processosCargosService = service('processosCargosService');
    }

    public function index()
    {
        $this->validarSessao();

        $itens = $this->processosCargosService->listarTodos();

        return view('Admin/layoutDash', [
            'camada1'       => '',
            'camada2'       => 'pages',
            'pagina'        => 'ProcessosCargos',
            'tipo'          => 'processos_cargos',
            'titulo'        => 'Gerenciar Processos × Cargos',
            'itens'         => $itens,
            'user'          => session('nome'),
        ]);
    }

    public function registrar()
    {
        try {
            $dto = new ProcessosCargosDTO(
                pkIdProcessoCargo: $this->request->getPost('pk_id_processo_cargo') ? (int) $this->request->getPost('pk_id_processo_cargo') : null,
                fkIdProcesso: (int) $this->request->getPost('fk_id_processo'),
                fkIdCargo: (int) $this->request->getPost('fk_id_cargo'),
                nmVagas: (int) $this->request->getPost('nm_vagas'),
                nmVagasPcd: $this->request->getPost('nm_vagas_pcd') ? (int) $this->request->getPost('nm_vagas_pcd') : null,
                nmCadastroReserva: (int) $this->request->getPost('nm_cadastro_reserva'),
                dsRequisitoProvimento: (string) $this->request->getPost('ds_requisito_provimento'),
                nmCargaHoraria: (int) $this->request->getPost('nm_carga_horaria'),
                nmTipoCargaHoraria: (int) $this->request->getPost('nm_tipo_carga_horaria'),
                nmRemuneracao: (float) str_replace(',', '.', $this->request->getPost('nm_remuneracao')),
            );

            $acao = $this->request->getPost('action') ?? 'create';

            if ($acao === 'update' && $dto->pkIdProcessoCargo !== null) {
                $this->processosCargosService->atualizar($dto->pkIdProcessoCargo, $dto);
                $mensagem = 'Registro atualizado com sucesso';
            } else {
                $this->processosCargosService->salvar($dto);
                $mensagem = 'Registro salvo com sucesso';
            }

            return redirect()->route('ProcessosCargos')
                ->with('mensagemSuccess', $mensagem);
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
            $this->processosCargosService->deletar($id);
            return redirect()->route('ProcessosCargos')
                ->with('mensagemSuccess', 'Registro excluído com sucesso');
        } catch (\Throwable $e) {
            return redirect()->route('ProcessosCargos')
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
     * VALIDAÇÕES AUXILIARES
     * ===================================================== */

    private function validarSessao(): void
    {
        if (!checklogged()) {
            redirect()->route('home')->with('error', 'Sua sessão expirou')->send();
            exit;
        }
    }
}
