<?php
namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Services\LogsService;
use App\Services\Candidatos\RecursosService;
use App\Services\Candidatos\CandidatosService;

use DateTime;

class Recursos extends BaseController{
    public function index($edital = null, $cargo = null, $candidato = null, $camada1 = 'pages',$camada2 = 'candidatos', $page = 'FormRecursos'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService = new CandidatosService();
        $recursosService = new RecursosService();

        $recurso = $candidatosService->listarCandidatoId($edital, $cargo, $candidato);
        $camposFormularios = $recursosService->tiposCamposFormulario($cargo);
        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'edital'        =>  $edital,
            'cargo'         =>  $cargo,
            'candidato'     =>  $candidato,
            'acao'          =>  'update',
            "dadosRecursos" =>  $recurso,
            "camposFormularios" => $camposFormularios,
            'titulo'        =>  'Aplicação de Recurso',
        ];
        echo view('layoutDash', $parametros);
    }

    public function cargosCandidato($cpfCandidato = null, $camada1 = 'pages',$camada2 = 'candidatos', $page = 'Recursos'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }

     // POST: pesquisa inicial
    if ($this->request->getMethod() === 'post') {

        $cpf = preg_replace('/\D/', '', $this->request->getPost('ds_cpf'));
        
        // após processar, REDIRECIONA para GET com CPF
        return redirect()->to(
            route_to('recursos.cargosCandidato', $cpf)
        );
    }

    // GET: exibição do grid
    if (! $cpfCandidato) {
        throw new \InvalidArgumentException('CPF não informado');
    }

    $cpf = $cpfCandidato;
        

        $protocolosModel = new ProtocolosModel();
        $protocolos = $protocolosModel->getProtocoloByCpf($cpf);
        
        $dadosCandidato = new CandidatosModel();
        $candidato = $dadosCandidato->where('ds_cpf', $cpf)->first();
        if($candidato){
            $nomeCandidato = $candidato->ds_nome;
        }else{
            $nomeCandidato = '';
        }
         $titulosTabela = array(
            "Cargo","Protocolo"
        );

        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'acao'          =>  'create',
            'titulo'        =>  'Recursos',
            'nomeCandidato'=>  $nomeCandidato,
            'titulosTabela' =>  $titulosTabela,
            'protocolos'    =>  $protocolos,
        ];
        echo view('layoutDash', $parametros);
    }

    public function registrar(){
        $recursosService = new RecursosService();
        $dados = $this->request->getPost();
        
        $edital = $dados['pk_id_edital'];
        $cargo = $dados['pk_id_cargo'];
        $candidato = $dados['pk_id_candidato'];
        $recursosService->aplicarRecursos($edital,$cargo,$candidato,$dados);
        
        $servicesLogs = new LogsService();
        
        $servicesLogs->inserirLog('Registrou Recurso', 'Recurso registrado do candidato '.$dados['ds_nome'],'tb_cadastrados_experiencia, tb_cadastrados_escolaridades e tb_cadastrados_aperfeicoamentos');

        return redirect()->route('Candidatos',[$edital,$cargo])->with('success', 'Recurso registrado com sucesso!');
    }

    


}