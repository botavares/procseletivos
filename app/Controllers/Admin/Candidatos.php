<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Services\Admin\Candidatos\SituacaoService;
use App\Models\Admin\EditaisModel;
use App\Models\Admin\CargosModel;
use App\Models\Admin\DadosContratosModel;
use App\Models\Admin\CandidatosModel;
use App\Traits\Admin\AtualizarAndamentoTrait;
use App\Traits\Admin\RegistrarEventoTrait;

class Candidatos extends BaseController{

    use AtualizarAndamentoTrait;
    use RegistrarEventoTrait;
    protected $manifestosData;

    protected $editaisModel;
    protected $cargosModel;
    protected $candidatosModel;
    protected $dadosContratosModel;

    public function __construct(){
        $this->editaisModel       = new \App\Models\Admin\EditaisModel();
        $this->cargosModel        = new \App\Models\Admin\CargosModel();
        $this->candidatosModel    = new \App\Models\Admin\CandidatosModel();
        $this->dadosContratosModel = new \App\Models\Admin\DadosContratosModel();
    }

    public function index($idEdital = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'Candidatos'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        if (!$idEdital || !$idCargo) {
            return redirect()->to(base_url('home'));
        }

        $dadosCargo  = $this->cargosModel->getCargo($idCargo);
        $dadosEdital = $this->editaisModel->getEdital($idEdital);

        if (!$dadosCargo || !$dadosEdital) {
            return redirect()->to(base_url('home'))->with('error', 'Cargo ou Edital não encontrado.');
        }

        $candidatos = $this->candidatosModel->getCandidatosPorEditalCargo($idEdital, $idCargo);
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
                'fk_id_edital'     => $idEdital,
                'fk_id_cargo'      => $idCargo,
                'ds_possui_pne'    => ((int)($c->fk_id_pne ?? 0)) > 1 ? 1 : 0,
            ];
        }

        echo view('Admin/layoutDash', [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidatos'    => $lista,
            'idEdital'      => $idEdital,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            "titulosTabela" => ["Edital Ref.","Data de Insc.","Nome do Candidato","Nascimento","Telefone","Email","Recursos"],
            'titulo'        => "Gerenciar Candidatos",
        ]);
    }
    public function salvarEscolha(){
        return redirect()->to(base_url("Candidatos/{$this->request->getPost('edital')}/{$this->request->getPost('cargo')}"));
    }

    public function exibirDados($edital,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'dadosCandidato'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $dadosCandidato = service('candidatosService')->listarCandidatoId($edital,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
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

    public function getCandidatosByCargoAndEdital(){
        $idCargo = $this->request->getPost('idCargo');
        $idEdital = $this->request->getPost('idEdital');
        $candidatos = $this->candidatosModel->getCandidatosPorEditalCargo($idEdital, $idCargo);
        return $this->response->setJSON($candidatos);
    }

    public function formSituacaoCandidato($edital,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'FormSituacaoCandidato'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService = service('candidatosService');
        $dadosCandidato = $candidatosService->listarCandidatoId($edital,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
        }

        $cargo = $this->cargosModel->where('pk_id_cargo', $dadosCandidato['idCargo'])->first();
        $dados = [
            'nomeCandidato' => $dadosCandidato['candidato']->ds_nome,
            'idCandidato'   => $dadosCandidato['idCandidato'],
            'idEdital'      => $dadosCandidato['idEdital'],
            'idCargo'       => $dadosCandidato['idCargo'],
            'nomeCargo'     => $cargo->ds_nome_cargo,
        ];

        $situacaoService = service('situacaoService');
        $dadosSituacao = $situacaoService->getCandidatoCargoEdital($dados['idEdital'], $dados['idCargo'], $dados['idCandidato']);
        $dadosSituacao ??= null;

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
        $idEdital    = $this->request->getPost('id_edital');
        $situacao    = $this->request->getPost('situacao');

        $data = [
            'fk_id_candidato' => $idCandidato,
            'fk_id_cargo'     => $idCargo,
            'fk_id_edital'    => $idEdital,
            'situacao'        => $situacao
        ];

        $situacaoService = service('situacaoService');
        $existente = $situacaoService->getCandidatoCargoEdital($idEdital, $idCargo, $idCandidato);

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

    public function exportarXlsx($edital, $cargo)
    {
        $dadosEdital = $this->editaisModel->getEdital($edital);
        $dadosCargo  = $this->cargosModel->getCargo($cargo);
        $candidatos  = $this->candidatosModel->getCandidatosPorEditalCargo($edital, $cargo);

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
            'edital'     => $dadosEdital->ds_numero_edital ?? 'edital',
            'cargo'      => $dadosCargo->ds_nome_cargo   ?? 'cargo',
            'somentePcd' => $somentePcd,
        ]);
    }
}