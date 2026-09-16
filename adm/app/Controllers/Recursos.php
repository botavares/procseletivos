<?php
namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Services\LogsService;
use App\Services\Candidatos\RecursosService;
use App\Services\Candidatos\RecursosConsultaService;
use App\Services\Candidatos\CandidatosService;

use DateTime;

class Recursos extends BaseController{

    /**
     * Exibe formulário de aplicação de recurso
     */
    public function index($edital = null, $cargo = null, $candidato = null, $camada1 = 'pages',$camada2 = 'candidatos', $page = 'FormRecursos'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService   = new CandidatosService();
        $recursosService     = new RecursosService();
        $consultaService     = new RecursosConsultaService();

        $recurso = $candidatosService->listarCandidatoId($edital, $cargo, $candidato);
        $camposFormularios = $recursosService->tiposCamposFormulario($cargo);
        $indeferimentosMap = $consultaService->buscarIndeferimentos((int)$edital, (int)$cargo, (int)$candidato);

        $parametros = [
            'camada1'               =>  $camada1,
            'camada2'               =>  $camada2,
            'pagina'                =>  $page,
            'edital'                =>  $edital,
            'cargo'                 =>  $cargo,
            'candidato'             =>  $candidato,
            'acao'                  =>  'update',
            "dadosRecursos"         =>  $recurso,
            "camposFormularios"     =>  $camposFormularios,
            'indeferimentosMap'     =>  $indeferimentosMap,
            'titulo'                =>  'Aplicação de Recurso',
        ];
        echo view('layoutDash', $parametros);
    }

    /**
     * Lista protocolos de um candidato pelo CPF
     */
    public function cargosCandidato($cpfCandidato = null, $camada1 = 'pages',$camada2 = 'candidatos', $page = 'Recursos'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        // POST: pesquisa inicial
        if ($this->request->getMethod() === 'post') {
            $cpf = preg_replace('/\D/', '', $this->request->getPost('ds_cpf'));
            return redirect()->to(
                route_to('recursos.cargosCandidato', $cpf)
            );
        }

        // GET: exibição do grid
        if (! $cpfCandidato) {
            throw new \InvalidArgumentException('CPF não informado');
        }

        $cpf = $cpfCandidato;

        $consultaService = new RecursosConsultaService();
        $protocolos = $consultaService->buscarProtocolosPorCpf($cpf);

        $candidato = $consultaService->buscarCandidatoPorCpf($cpf);
        $nomeCandidato = $candidato ? $candidato->ds_nome : '';

        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'acao'          =>  'create',
            'titulo'        =>  'Recursos',
            'nomeCandidato' =>  $nomeCandidato,
            'titulosTabela' =>  ["Cargo","Protocolo"],
            'protocolos'    =>  $protocolos,
        ];
        echo view('layoutDash', $parametros);
    }

    /**
     * Lista candidatos por edital e cargo para aplicar recursos
     */
    public function listar($idEdital = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'RecursosCandidatos'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        if (!$idEdital || !$idCargo) {
            return redirect()->to(base_url('home'));
        }

        $consultaService = new RecursosConsultaService();

        $dadosEdital = $consultaService->buscarDadosEdital((int)$idEdital);
        $dadosCargo  = $consultaService->buscarDadosCargo((int)$idCargo);

        if (!$dadosEdital || !$dadosCargo) {
            return redirect()->to(base_url('home'))->with('error', 'Edital ou Cargo não encontrado.');
        }

        $candidatos = $consultaService->buscarCandidatosPorEditalCargo((int)$idEdital, (int)$idCargo);

        $arrayCandidatos = [];
        foreach ($candidatos as $candidato) {
            $arrayCandidatos[$candidato->pk_id_cadastrado] = [
                'pk_id_cadastrado' => $candidato->pk_id_cadastrado,
                'ds_nome'          => $candidato->ds_nome,
                'ds_cpf'           => $candidato->ds_cpf,
                'ds_data_cadastro' => $candidato->ds_data_cadastro,
                'ds_nascimento'    => $candidato->ds_nascimento,
                'ds_email'         => $candidato->ds_email,
                'ds_celular'       => $candidato->ds_celular,
                'ds_numero_edital' => $candidato->ds_numero_edital,
                'ds_protocolo'     => $candidato->ds_protocolo,
                'fk_id_edital'     => $idEdital,
                'fk_id_cargo'      => $idCargo,
                'ds_nome_cargo'    => $dadosCargo->ds_nome_cargo,
            ];
        }

        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidatos'    => $arrayCandidatos,
            'idEdital'      => $idEdital,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'titulosTabela' => ["Edital Ref.","Data de Insc.","Nome do Candidato","Nascimento","Telefone","Email","Protocolo"],
            'titulo'        => 'Candidatos - Aplicação de Recurso',
        ];

        echo view('layoutDash', $parametros);
    }

    /**
     * Registra recurso do candidato
     */
    public function registrar(){
        $recursosService = new RecursosService();
        $dados = $this->request->getPost();

        $edital = $dados['pk_id_edital'];
        $cargo = $dados['pk_id_cargo'];
        $candidato = $dados['pk_id_candidato'];
        $recursosService->aplicarRecursos($edital,$cargo,$candidato,$dados);

        $servicesLogs = new LogsService();
        $servicesLogs->inserirLog('Registrou Recurso', 'Recurso registrado do candidato '.$dados['ds_nome'],'tb_cadastrados_experiencias, tb_cadastrados_escolaridades, tb_cadastrados_aperfeicoamentos, tb_cadastrados_criterios');

        return redirect()->to(base_url("Recursos/listar/{$edital}/{$cargo}"))->with('success', 'Recurso registrado com sucesso!');
    }

    /**
     * Salva escolha de edital e cargo e redireciona para listagem
     */
    public function salvarEscolha(){
        $idEdital = $this->request->getPost('edital');
        $idCargo  = $this->request->getPost('cargo');

        return redirect()->to(base_url("Recursos/listar/{$idEdital}/{$idCargo}"));
    }

    /**
     * Exibe o histórico de recursos com filtros
     */
    public function historico($camada1 = 'pages', $camada2 = 'candidatos', $page = 'RecursosHistorico'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        $consultaService = new RecursosConsultaService();

        $filtros = [];

        // Se for POST, aplicar filtros
        if ($this->request->getMethod() === 'post') {
            $filtros = [
                'protocolo'   => $this->request->getPost('protocolo'),
                'candidato'   => $this->request->getPost('candidato'),
                'data_inicio' => $this->request->getPost('data_inicio'),
                'data_fim'    => $this->request->getPost('data_fim'),
                'edital'      => $this->request->getPost('edital'),
                'cargo'       => $this->request->getPost('cargo'),
            ];

            // Limpar filtros vazios
            $filtros = array_filter($filtros, function($v) {
                return $v !== null && $v !== '';
            });
        }

        $historico = $consultaService->buscarHistorico($filtros);

        $parametros = [
            'camada1'    => $camada1,
            'camada2'    => $camada2,
            'pagina'     => $page,
            'titulo'     => 'Histórico de Recursos',
            'historico'  => $historico,
            'filtros'    => $filtros,
            'editais'    => $consultaService->listarEditais(),
            'cargos'     => $consultaService->listarCargos(),
        ];

        echo view('layoutDash', $parametros);
    }

    /**
     * Exibe detalhes de um recurso específico
     */
    public function detalhes($id = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'RecursosDetalhes'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        if (!$id) {
            return redirect()->route('Recursos.historico')->with('error', 'ID do recurso não informado');
        }

        $consultaService = new RecursosConsultaService();
        $recurso = $consultaService->buscarRecursoPorId((int)$id);

        if (!$recurso) {
            return redirect()->route('Recursos.historico')->with('error', 'Recurso não encontrado');
        }

        // Buscar todos os registros do mesmo protocolo
        $registrosProtocolo = [];
        if (!empty($recurso->ds_numero_protocolo)) {
            $registrosProtocolo = $consultaService->buscarRegistrosPorProtocolo($recurso->ds_numero_protocolo);
        }

        $parametros = [
            'camada1'           => $camada1,
            'camada2'           => $camada2,
            'pagina'            => $page,
            'titulo'            => 'Detalhes do Recurso',
            'recurso'           => $recurso,
            'registrosProtocolo'=> $registrosProtocolo,
        ];

        echo view('layoutDash', $parametros);
    }
}
