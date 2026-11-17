<?php

namespace App\Controllers\Admin;

use App\Models\Admin\CandidatosModel;
use App\Models\Admin\CargosModel;
use App\Services\Admin\ProcessosConvocacao\ProcessosConvocacaoService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Convocados extends BaseController
{
    protected ?ProcessosConvocacaoService $convocacaoService = null;
    protected ?CandidatosModel $candidatosModel = null;
    protected ?CargosModel $cargosModel = null;

    public function __construct()
    {
        $this->convocacaoService = service('processosConvocacaoService');
        $this->candidatosModel   = new CandidatosModel();
        $this->cargosModel       = new CargosModel();
    }

    /**
     * Exibe a tela de convocados/inscritos para um processo e cargo.
     * Também aceita o ID da convocação como primeiro parâmetro.
     */
    public function index($idProcesso = null, $idCargo = null)
    {
        $camada1 = '';
        $camada2 = 'pages';
        $page    = 'Convocados';

        if (!is_file(APPPATH . 'Views/Admin/' . ($camada1 ? $camada1 . '/' : '') . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        $idProcesso = (int) $idProcesso;
        $idCargo    = (int) $idCargo;

        if (!$idProcesso || !$idCargo) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Processo ou cargo não informados.');
        }

        $idSecretaria = (int) session('fk_id_secretaria');

        $dadosCargo = $this->cargosModel->find($idCargo);
        if (!$dadosCargo || (int) $dadosCargo->fk_id_secretaria !== $idSecretaria) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Cargo não pertence à secretaria do usuário logado.');
        }

        $candidatosInscritos = $this->candidatosModel->getCandidatosPorProcessoCargo($idProcesso, $idCargo, $idSecretaria);

        // Adapta os candidatos inscritos para o formato esperado pela view de convocados
        $convocados = [];
        foreach ($candidatosInscritos as $candidato) {
            $convocados[] = (object) [
                'pk_id_candidato'  => $candidato->pk_id_cadastrado,
                'ds_periodo'       => $candidato->ds_protocolo ?? '-',
                'ds_nome'          => $candidato->ds_nome,
                'ds_nascimento'    => $candidato->ds_nascimento,
                'ds_celular'       => $candidato->ds_celular,
                'ds_email'         => $candidato->ds_email,
                'ds_nome_curso'    => $dadosCargo->ds_nome_cargo,
                'ds_data'          => $candidato->ds_data_cadastro,
                'ds_interesse'     => null,
                'ds_comparecimento' => null,
            ];
        }

        echo view('Admin/layoutDash', [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'convocados'    => $convocados,
            'titulosTabela' => ['Protocolo', 'Nome do Candidato', 'Nascimento', 'Telefone', 'E-mail', 'Cargo', 'Data de Inscrição'],
            'titulo'        => 'Candidatos Inscritos - Convocação',
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'idProcesso'    => $idProcesso,
            'idCargo'       => $idCargo,
            'modoInscritos' => true,
        ]);
    }

    /**
     * Recebe a escolha do modal de convocação do dashboard.
     */
    public function salvarEscolha()
    {
        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        $idConvocacao = (int) $this->request->getPost('convocacao');

        if ($idConvocacao <= 0) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Selecione um edital de convocação.');
        }

        $dadosConvocacao = $this->convocacaoService->buscarPorId($idConvocacao);
        if (!$dadosConvocacao) {
            return redirect()->to(base_url_adm('Dashboard'))->with('mensagemError', 'Edital de convocação não encontrado.');
        }

        $convocacao = (object) $dadosConvocacao;

        return redirect()->to(base_url_adm("Convocados/{$convocacao->fk_id_processo}/{$convocacao->fk_id_cargo}"));
    }

    /**
     * Retorna as convocações vinculadas a um processo seletivo (AJAX).
     */
    public function getConvocacoesByProcesso()
    {
        $idProcesso = $this->request->getPost('idProcesso');
        if (!$idProcesso) {
            return $this->response->setJSON(['convocacoes' => []]);
        }

        $convocacoes = $this->convocacaoService->listarPorProcesso((int) $idProcesso);

        return $this->response->setJSON(['convocacoes' => $convocacoes]);
    }
}
