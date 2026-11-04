<?php
namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Services\Admin\LogsService;
use App\Services\Admin\Candidatos\RecursosService;
use App\Services\Admin\Candidatos\RecursosConsultaService;
use App\Services\Admin\Candidatos\CandidatosService;

use DateTime;

class Recursos extends BaseController{

    /**
     * Exibe formulário de aplicação de recurso
     */
    public function index($processo = null, $cargo = null, $candidato = null, $camada1 = 'pages',$camada2 = 'candidatos', $page = 'FormRecursos'){
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService   = service('candidatosService');
        $recursosService     = service('recursosService');
        $consultaService     = service('recursosConsultaService');

        $recurso = $candidatosService->listarCandidatoId($processo, $cargo, $candidato);
        $camposFormularios = $recursosService->tiposCamposFormulario($cargo);

        $indeferimentosMap = $consultaService->buscarIndeferimentos(
            (int)$processo,
            (int)$cargo,
            (int)$candidato
        );

        $parametros = [
            'camada1'               =>  $camada1,
            'camada2'               =>  $camada2,
            'pagina'                =>  $page,
            'edital'                =>  $processo,
            'cargo'                 =>  $cargo,
            'candidato'             =>  $candidato,
            'acao'                  =>  'update',
            "dadosRecursos"         =>  $recurso,
            "camposFormularios"     =>  $camposFormularios,
            'indeferimentosMap'     =>  $indeferimentosMap,
            'titulo'                =>  'Aplicação de Recurso',
        ];
        echo view('Admin/layoutDash', $parametros);
    }

    /**
     * Lista protocolos de um candidato pelo CPF
     */
    public function cargosCandidato($cpfCandidato = null, $camada1 = 'pages',$camada2 = 'candidatos', $page = 'Recursos'){
        if (! is_file(APPPATH . 'Views/Admin/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
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

        $consultaService = service('recursosConsultaService');
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
        echo view('Admin/layoutDash', $parametros);
    }

    /**
     * Lista candidatos por edital e cargo para aplicar recursos
     */
    public function listar($idProcesso = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'RecursosCandidatos'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        if (!$idProcesso || !$idCargo) {
            return redirect()->to(base_url('home'));
        }

        $consultaService = service('recursosConsultaService');

        $dadosProcesso = (new \App\Models\ProcessosSeletivosModel())->find($idProcesso);
        $dadosCargo    = $consultaService->buscarDadosCargo((int)$idCargo);

        if (!$dadosProcesso || !$dadosCargo) {
            return redirect()->to(base_url('home'))->with('error', 'Processo ou Cargo não encontrado.');
        }

        $candidatos = $consultaService->buscarCandidatosComRecursosPorProcessoCargo((int)$idProcesso, (int)$idCargo);

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
                'fk_id_processo'   => $idProcesso,
                'fk_id_cargo'      => $idCargo,
                'ds_nome_cargo'    => $dadosCargo->ds_nome_cargo,
            ];
        }

        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidatos'    => $arrayCandidatos,
            'idProcesso'    => $idProcesso,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'titulosTabela' => ["Edital Ref.","Data de Insc.","Nome do Candidato","Nascimento","Telefone","Email","Protocolo"],
            'titulo'        => 'Candidatos com Recurso',
        ];

        echo view('Admin/layoutDash', $parametros);
    }

    /**
     * Registra recurso do candidato
     */
    public function registrar(){
        $recursosService = service('recursosService');
        $dados = $this->request->getPost();

        $processo  = $dados['pk_id_processo'];
        $cargo     = $dados['pk_id_cargo'];
        $candidato = $dados['pk_id_candidato'];

        $recursosService->aplicarRecursos((int)$processo, (int)$cargo, (int)$candidato, $dados);

        $servicesLogs = service('logsService');
        $servicesLogs->inserirLog('Registrou Recurso', 'Recurso registrado do candidato '.$dados['ds_nome'],'tb_cadastrados_experiencias, tb_cadastrados_escolaridades, tb_cadastrados_aperfeicoamentos, tb_cadastrados_criterios');

        return redirect()->to(base_url_adm("Recursos/listar/{$processo}/{$cargo}"))->with('success', 'Recurso registrado com sucesso!');
    }

    /**
     * Salva escolha de edital e cargo e redireciona para listagem
     */
    public function salvarEscolha(){
        $idProcesso = $this->request->getPost('processo');
        $idCargo    = $this->request->getPost('cargo');

        return redirect()->to(base_url_adm("Recursos/listar/{$idProcesso}/{$idCargo}"));
    }

    /**
     * Exibe o histórico de recursos com filtros
     */
    public function historico($camada1 = 'pages', $camada2 = 'candidatos', $page = 'RecursosHistorico'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        $consultaService = service('recursosConsultaService');

        $filtros = [];

        // Se for POST, aplicar filtros
        if ($this->request->getMethod() === 'post') {
            $filtros = [
                'protocolo'   => $this->request->getPost('protocolo'),
                'candidato'   => $this->request->getPost('candidato'),
                'data_inicio' => $this->request->getPost('data_inicio'),
                'data_fim'    => $this->request->getPost('data_fim'),
                'processo'    => $this->request->getPost('processo'),
                'cargo'       => $this->request->getPost('cargo'),
            ];

            // Limpar filtros vazios
            $filtros = array_filter($filtros, function($v) {
                return $v !== null && $v !== '';
            });
        }

        // Se vier via GET (parâmetros na URL), também aplicar
        $getFiltros = [
            'protocolo'   => $this->request->getGet('protocolo'),
            'candidato'   => $this->request->getGet('candidato'),
            'data_inicio' => $this->request->getGet('data_inicio'),
            'data_fim'    => $this->request->getGet('data_fim'),
            'processo'    => $this->request->getGet('processo'),
            'cargo'       => $this->request->getGet('cargo'),
        ];
        $getFiltros = array_filter($getFiltros, function($v) {
            return $v !== null && $v !== '';
        });

        $filtros = array_merge($filtros, $getFiltros);

        $historico = $consultaService->buscarHistorico($filtros);

        $parametros = [
            'camada1'    => $camada1,
            'camada2'    => $camada2,
            'pagina'     => $page,
            'titulo'     => 'Histórico de Recursos',
            'historico'  => $historico,
            'filtros'    => $filtros,
            'processos'  => $consultaService->listarProcessos(),
            'cargos'     => $consultaService->listarCargos(),
        ];

        echo view('Admin/layoutDash', $parametros);
    }

    /**
     * Exibe detalhes de um recurso específico
     */
    public function detalhes($id = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'RecursosDetalhes'){
        if (!is_file(APPPATH . 'Views/Admin/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error', 'Sua sessão expirou');
        }

        if (!$id) {
            return redirect()->route('Recursos.historico')->with('error', 'ID do recurso não informado');
        }

        $consultaService = service('recursosConsultaService');
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

        echo view('Admin/layoutDash', $parametros);
    }

}
