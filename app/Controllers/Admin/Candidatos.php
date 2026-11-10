<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Services\Admin\Candidatos\SituacaoService;
use App\Models\ProcessosSeletivosModel;
use App\Models\Admin\CargosModel;
use App\Models\Admin\DadosContratosModel;
use App\Models\Admin\CandidatosModel;
use App\Traits\Admin\AtualizarAndamentoTrait;
use App\Traits\Admin\RegistrarEventoTrait;

class Candidatos extends BaseController{

    use AtualizarAndamentoTrait;
    use RegistrarEventoTrait;
    protected $manifestosData;

    protected $processosSeletivosModel;
    protected $cargosModel;
    protected $candidatosModel;
    protected $dadosContratosModel;

    public function __construct(){
        $this->processosSeletivosModel = new \App\Models\ProcessosSeletivosModel();
        $this->cargosModel        = new \App\Models\Admin\CargosModel();
        $this->candidatosModel    = new \App\Models\Admin\CandidatosModel();
        $this->dadosContratosModel = new \App\Models\Admin\DadosContratosModel();
    }

    public function index($idProcesso = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'Candidatos'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        if (!$idProcesso || !$idCargo) {
            return redirect()->to(base_url('home'));
        }

        $dadosCargo   = $this->cargosModel->getCargo($idCargo);
        $dadosProcesso = $this->processosSeletivosModel->find($idProcesso);

        if (!$dadosCargo || !$dadosProcesso) {
            return redirect()->to(base_url('home'))->with('error', 'Cargo ou Processo não encontrado.');
        }

        if ((int) $dadosCargo->fk_id_secretaria !== (int) session('fk_id_secretaria')) {
            return redirect()->to(base_url('home'))->with('error', 'Cargo não pertence à secretaria do usuário logado.');
        }

        $candidatos = $this->candidatosModel->getCandidatosPorProcessoCargo($idProcesso, $idCargo, (int) session('fk_id_secretaria'));
        $lista = [];
        foreach ($candidatos as $c) {
            $lista[$c->pk_id_cadastrado] = [
                'pk_id_cadastrado' => $c->pk_id_cadastrado,
                'ds_numero_edital' => $c->ds_numero_edital,
                'ds_data_cadastro' => $c->ds_data_cadastro,
                'ds_nome'          => $c->ds_nome,
                'ds_nascimento'    => $c->ds_nascimento,
                'ds_celular'       => $c->ds_celular,
                'ds_email'         => $c->ds_email,
                'fk_id_processo'   => $idProcesso,
                'fk_id_cargo'      => $idCargo,
                'ds_possui_pne'    => ((int)($c->fk_id_pne ?? 0)) > 1 ? 1 : 0,
            ];
        }

        echo view('Admin/layoutDash', [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidatos'    => $lista,
            'idProcesso'    => $idProcesso,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            "titulosTabela" => ["Edital Ref.","Data de Insc.","Nome do Candidato","Nascimento","Telefone","Email","Recursos"],
            'titulo'        => "Gerenciar Candidatos",
        ]);
    }
    public function salvarEscolha(){
        $idSecretaria = (int) session('fk_id_secretaria');
        $processo = (int) $this->request->getPost('processo');
        $cargo    = (int) $this->request->getPost('cargo');

        $dadosCargo = $this->cargosModel->find($cargo);
        if (!$dadosCargo || (int) $dadosCargo->fk_id_secretaria !== $idSecretaria) {
            return redirect()->to(base_url_adm('Dashboard'))->with('error', 'Cargo não pertence à secretaria do usuário logado.');
        }

        return redirect()->to(base_url_adm("Candidatos/{$processo}/{$cargo}"));
    }

    public function exibirDados($processo,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'dadosCandidato'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $idSecretaria = (int) session('fk_id_secretaria');
        $dadosCandidato = service('candidatosService')->listarCandidatoId($processo,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
        }

        // Valida se o candidato possui protocolo para a secretaria do usuário logado
        $protocolo = $this->candidatosModel->getCandidatosPorProcessoCargo($processo, $cargo, $idSecretaria);
        $encontrado = array_filter($protocolo, fn($p) => (int) $p->pk_id_cadastrado === (int) $id);
        if (empty($encontrado)) {
            return redirect()->to(base_url('home'))->with('error', 'Candidato não encontrado para a secretaria do usuário logado.');
        }

        echo view('Admin/layoutDash', [
            'camada1'        => $camada1,
            'camada2'        => $camada2,
            'pagina'         => $page,
            'dadosCandidato' => $dadosCandidato,
            'perfil'         => session('perfil'),
            'user'           => session('nome'),
            'titulo'         => "Dados do Candidato",
        ]);
    }   

    public function getCandidatosByCargoAndProcesso(){
        $idCargo = $this->request->getPost('idCargo');
        $idProcesso = $this->request->getPost('idProcesso');
        $idSecretaria = (int) session('fk_id_secretaria');
        $candidatos = $this->candidatosModel->getCandidatosPorProcessoCargo($idProcesso, $idCargo, $idSecretaria);
        return $this->response->setJSON($candidatos);
    }

    public function formSituacaoCandidato($processo,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'FormSituacaoCandidato'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService = service('candidatosService');
        $dadosCandidato = $candidatosService->listarCandidatoId($processo,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
        }

        $cargo = $this->cargosModel->where('pk_id_cargo', $dadosCandidato['idCargo'])->first();
        $dados = [
            'candidato'     => $dadosCandidato['candidato']->ds_nome,
            'nomeCandidato' => $dadosCandidato['candidato']->ds_nome,
            'idCandidato'   => $dadosCandidato['idCandidato'],
            'idProcesso'    => $dadosCandidato['idProcesso'],
            'idCargo'       => $dadosCandidato['idCargo'],
            'nomeCargo'     => $cargo->ds_nome_cargo,
        ];

        $situacaoService = service('situacaoService');
        $dadosSituacao = $situacaoService->getCandidatoCargoProcesso(
            (int) $dadosCandidato['idProcesso'],
            $dadosCandidato['idCargo'],
            $dadosCandidato['idCandidato']
        );

        if ($dadosSituacao === null) {
            $dadosSituacao = (object) ['situacao' => null];
        } elseif (is_array($dadosSituacao)) {
            $dadosSituacao = (object) $dadosSituacao;
        }

        echo view('Admin/layoutDash', array_merge($dados, [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'dadosSituacao' => $dadosSituacao,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Situação do Candidato",
        ]));
    }
    public function registrarSituacao(){
        $idCandidato = $this->request->getPost('id_candidato');
        $idCargo     = $this->request->getPost('id_cargo');
        $idProcesso  = $this->request->getPost('fk_id_processo');
        $situacao    = $this->request->getPost('nm_id_situacao');

        $data = [
            'fk_id_candidato' => $idCandidato,
            'fk_id_cargo'     => $idCargo,
            'fk_id_processo'  => $idProcesso,
            'nm_id_situacao'  => $situacao
        ];

        $situacaoService = service('situacaoService');
        $existente = $situacaoService->getCandidatoCargoProcesso((int)$idProcesso, (int)$idCargo, (int)$idCandidato);

        if ($existente !== null) {
            if ($situacao > 0) {
                $situacaoService->atualizar($existente['pk_id_situacao'] ?? $existente['id'], $data);
                $msg = 'atualizada';
            } else {
                $situacaoService->excluir($existente['pk_id_situacao'] ?? $existente['id']);
                $msg = 'excluída';
            }
        } else {
            $situacaoService->salvar($data);
            $msg = 'registrada';
        }

        return redirect()->back()->with('mensagemSuccess', "Situação {$msg} com sucesso.");
    }

    public function exportarXlsx($processo, $cargo)
    {
        $idSecretaria = (int) session('fk_id_secretaria');
        $dadosProcesso = $this->processosSeletivosModel->find($processo);
        $dadosCargo    = $this->cargosModel->getCargo($cargo);

        if (!$dadosCargo || (int) $dadosCargo->fk_id_secretaria !== $idSecretaria) {
            return redirect()->to(base_url('home'))->with('error', 'Cargo não pertence à secretaria do usuário logado.');
        }

        $candidatos    = $this->candidatosModel->getCandidatosPorProcessoCargo($processo, $cargo, $idSecretaria);

        if (empty($candidatos)) {
            return redirect()->back()->with('error', 'Não há candidatos para exportar.');
        }

        $somentePcd = $this->request->getGet('pcd') == '1';
        if ($somentePcd) {
            $candidatos = array_filter($candidatos, fn($c) => ((int)($c->fk_id_pne ?? 0)) > 1);
            if (empty($candidatos)) {
                return redirect()->back()->with('error', 'Não há candidatos PCD nesta listagem.');
            }
        }

        service('excelExportService')->exportarCandidatos($candidatos, [
            'edital'     => ($dadosProcesso->nm_numero_processo ?? '') . '/' . ($dadosProcesso->nm_ano_processo ?? ''),
            'cargo'      => $dadosCargo->ds_nome_cargo   ?? 'cargo',
            'somentePcd' => $somentePcd,
        ]);
    }

}