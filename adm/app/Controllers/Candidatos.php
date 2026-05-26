<?php

namespace App\Controllers;
use FileSystemIterator;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use CodeIgniter\Exceptions\PageNotFoundException;

use App\DTOs\Domain\Candidatos\SituacaoDTO;
use App\DTOs\Domain\Candidatos\SituacaoDetalhadaDTO;

use App\Services\LogsService;
use App\Services\EmailService;
use App\Services\Candidatos\CandidatosService;
use App\Services\Candidatos\SituacaoService;

use App\Models\EditaisModel;
use App\Models\CargosModel;
use App\Models\VagasModel;
use App\Models\AuxiliosModel;
use App\Models\InstituicoesModel;
use App\Models\EditaisCargosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\DadosContratosModel;
use App\Models\UnidadesModel;
use App\Models\AcademicosModel;
use App\Models\CandidatosModel;
use App\Models\ConvocadosModel;

use App\Modules\Breadcrumbs\Breadcrumbs;

//traits
use App\Traits\AtualizarAndamentoTrait;
use App\Traits\RegistrarEventoTrait;

// services
use App\Services\ContagemDeTempoService;

use CodeIgniter\Controller;

class Candidatos extends BaseController{

    use AtualizarAndamentoTrait;
    use RegistrarEventoTrait;
    protected $manifestosData;
    

    public function __construct(){
      
      
    }

    /*==============================================================================
        FUNÇÃO: index;
        OBJETIVO: O index dessa classe será utilizada para abrir a tela inicial de candidatos;
        PARAMETROS: camada1 = primeira pasta após pasta views, camada2 = segunda pasta, $page = nome da view
        CRIAÇÃO:25/09/2025
        MODIFICADO:
        RESUMO: Função abre o tela inicial de cadidatos. 
        ==============================================================================*/
    public function index($idEdital = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'Candidatos'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $modelEditais   = new EditaisModel();
        $modelCargos    = new CargosModel();
        $modelCandidato = new CandidatosModel();
        $contratosModel  = new DadosContratosModel();

        if (!$idEdital || !$idCargo) {
            // Se não vier na URL, poderia redirecionar de volta
            return redirect()->to(base_url('home'));
        }
        $dadosCargo = $modelCargos->getCargo($idCargo);

        $candidatos = $modelCandidato->getCandidatosPorEditalCargo($idEdital, $idCargo);
        
        $dadosEdital = $modelEditais->getEdital($idEdital);

         if(!$dadosCargo || !$dadosEdital){
            return redirect()->to(base_url('home'))->with('error', 'Cargo ou Edital não encontrado.');
         }

         // Para cada candidato, calcular os dias trabalhados   
        
        $arrayCandidatos = [];
        foreach($candidatos as $candidato){
            $arrayCandidatos[$candidato->pk_id_cadastrado]["pk_id_cadastrado"] = $candidato->pk_id_cadastrado;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_numero_edital"] = $candidato->ds_numero_edital;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_data_cadastro"] = $candidato->ds_data_cadastro;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_nome"] = $candidato->ds_nome;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_nascimento"] = $candidato->ds_nascimento;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_celular"] = $candidato->ds_celular;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_email"] = $candidato->ds_email;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["fk_id_edital"] = $idEdital;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["fk_id_cargo"] = $idCargo;

        }

        $titulosTabela = ["Edital Ref.","Data de Insc.","Nome do Candidato","Nascimento","Telefone","Email"];
        
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidatos'    => $arrayCandidatos,
            'idEdital'      => $idEdital,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            "titulosTabela" => $titulosTabela,
            'titulo'        => "Gerenciar Candidatos",
        ];

        echo view('layoutDash', $parametros);
    }
    /*===============================================================================
            FUNÇÃO: salvarEscolha;
            OBJETIVO: Salvar a escolha do edital e curso e redirecionar para a listagem de candidatos;
            PARAMETROS: nenhum
            CRIAÇÃO:25/09/2025
            MODIFICADO:
            RESUMO: Função salva a escolha do edital e curso e redireciona para a listagem de candidatos.
    ==============================================================================*/
    public function salvarEscolha(){
        $idEdital = $this->request->getPost('edital');
        $idCargo  = $this->request->getPost('cargo');

        // redireciona para GET
        return redirect()->to(base_url("Candidatos/{$idEdital}/{$idCargo}"));
    }



    
    /*===============================================================================
            FUNÇÃO: formularioContratar;
            OBJETIVO: Contratar ou encaminhar candidato a estágio para o seu setor
            PARAMETROS: id do candidato
            CRIAÇÃO:01/10/2025
            MODIFICADO:
            RESUMO: Função salva a escolha do edital e curso e redireciona para a listagem de candidatos.
    ==============================================================================*/
    public function formularioContratar($id,$camada1 = 'pages', $camada2 = 'cadastros', $page = 'formContratar'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }
        $academicosModel        = new AcademicosModel();
        $candidatosModel        = new CandidatosModel();
        $cursosModel            = new CursosModel();
        $modelEditais           = new EditaisModel();
        $editaisCandidatosModel = new EditaisCandidatosModel();
        $vagasModel             = new VagasModel();
        $auxiliosModel          = new AuxiliosModel();
        $instituicaoModel       = new InstituicoesModel();

        $dadosAcademicos        = $academicosModel->where('pk_id_cadastrado', $id)->first();
        $dadosCandidato         = $candidatosModel->where('pk_id_cadastrado', $id)->first();
        $dadosCargo             = $cursosModel->where('pk_id_curso', $dadosAcademicos->fk_id_curso)->first();
        $dadosEditaisCandidatos = $editaisCandidatosModel->editaisPorId($id);
        $auxilios               = $auxiliosModel->findAll();
        $dadosInstituicoes      = $instituicaoModel->orderBy('ds_nome','ASC')->findAll();
        
        $setoresComVagas = $vagasModel->setoresComVagas($dadosAcademicos->fk_id_curso);

        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'academicos'    => $dadosAcademicos,
            'candidato'     => $dadosCandidato,
            'cargo'         => $dadosCargo->pk_id_cargo,
            'cargoNome'     => $dadosCargo->ds_nome_cargo,
            'auxilios'      => $auxilios,
            'instituicoes'  => $dadosInstituicoes,
            'edital'        => $dadosEditaisCandidatos->fk_id_edital,
            'setoresComVagas'=> $setoresComVagas,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Contratar Candidatos",
        ];
        

        echo view('layoutDash', $parametros);
    }
 
    /*=====================================================================* 
        FUNÇÃO: processarUpload;
        OBJETIVO: armazenar arquivos referente ao manifesto na pasta writable/uploads/Manifestos/;
        PARAMETROS: - $protocolo;
        CRIAÇÃO:20/03/2025
        MODIFICADO:
        RESUMO: "sobe" arquivos do manifesto para a pasta de arquivos. 
    ==============================================================================*/
    private function processarUpload($protocolo){
        //veriricar se campo de upload de arquivos foi preenchido
        if($this->request->getFileMultiple('ds_arquivos')){
            //preparando arquivos (se houver) para upload
            $validation = new Validation();
            $tiposPermitidos = ['pdf', 'docx', 'xlsx', 'jpeg', 'jpg', 'png'];
            $uploadPath = WRITEPATH . 'uploads/Estagiarios';
            $arquivosUpload = postFiles(request: $this->request, inputName: 'ds_arquivos', allowedTypes: $tiposPermitidos, uploadPath: $uploadPath, protocolo: $protocolo);
            if (!$arquivosUpload['success']) {
                // Adiciona os erros de arquivo aos erros de validação
                foreach ($arquivosUpload['errors'] as $error) {
                    $validation->setError('ds_arquivos', $error);
                }
                return redirect()->back()->withInput()->with(key: 'errors', message: $validation->getErrors());       
            }else{
                foreach ($arquivosUpload['uploadedFiles'] as $fileName) {
                    $data = [
                        'ds_protocolo' => $protocolo,
                        'ds_nome_arquivo' => $fileName
                    ];
                    // Insere o arquivo na tabela
                    $arquivoModel       =   new \App\Models\ArquivoModel();
                    $arquivoModel->insert($data);
                }
            }
        }
    }
    /*=====================================================================* 
        FUNÇÃO: arquivoPermitido;
        OBJETIVO: Verificar se o arquivo apresentado é uma extensão permitida;
        PARAMETROS: - $arquivo;
        CRIAÇÃO:12/03/2025
        MODIFICADO:
        RESUMO: Verifica se o arquivo apresentado é uma extensão permitida. 
    ==============================================================================*/
    private function arquivoPermitido($arquivo)
    {
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp','pdf'];
        $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
        return in_array(strtolower($extensao), $extensoesPermitidas);
    }
    
   
   /*=====================================================================* 
        FUNÇÃO: buscarArquivosPorProtocolo;
        OBJETIVO: Selecionar arquivos registradas durante o manifesto;
        PARAMETROS: - $protocolo;
        CRIAÇÃO:12/03/2025
        MODIFICADO:
        RESUMO: Buscar as arquivos na pasta que estão relacionadas a um protocolo
                específico. 
    ==============================================================================*/
    private function buscarArquivosPorProtocolo($protocolo){
        $caminho = WRITEPATH . 'uploads/Estagiarios/';
        // Verifica se a pasta existe
        if (!is_dir($caminho)) {
            return [];
        }
        
        // Percorre os arquivos da pasta
        $iterator = new FilesystemIterator($caminho);
       
        foreach ($iterator as $file) {
            $nomeArquivo = $file->getFilename();
            
            // Verifica se o arquivo pertence ao protocolo e se é uma imagem
            if ($file->isFile() && str_starts_with($nomeArquivo, $protocolo) && $this->arquivoPermitido($nomeArquivo)) {
                $arquivos[] = [
                    'nomeArquivo' => $nomeArquivo,
                    'caminho' => base_url("writable/uploads/Estagiarios/" . $nomeArquivo)
                ];
                
            }else{
                $arquivos = [];
            }
        }
        
        return $arquivos;
    }
    public function exibirDados($edital,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'dadosCandidato'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService = new CandidatosService();
        $dadosCandidato = $candidatosService->listarCandidatoId($edital,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
        }
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'dadosCandidato'     => $dadosCandidato,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Dados do Candidato",
        ];

        echo view('layoutDash', $parametros);
    }   

    public function getCandidatosByCargoAndEdital(){
        $idCargo = $this->request->getPost('idCargo');
        $idEdital = $this->request->getPost('idEdital');
        $candidatosModel = new CandidatosModel();
        $candidatos = $candidatosModel->getCandidatosPorEditalCargo($idEdital, $idCargo);
        return $this->response->setJSON($candidatos);
    }

    public function formSituacaoCandidato($edital,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'FormSituacaoCandidato'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService = new CandidatosService();
        $dadosCandidato = $candidatosService->listarCandidatoId($edital,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
        }
        
        $nomeCandidato = $dadosCandidato["candidato"]->ds_nome;
        $idCandidato = $dadosCandidato["idCandidato"];
        $idEdital = $dadosCandidato["idEdital"];
        $idCargo = $dadosCandidato["idCargo"];
        $cargosModel = new CargosModel();
        $nomeCargo = $cargosModel->where('pk_id_cargo', $idCargo)->first()->ds_nome_cargo;
        
        $situacaoService = service('situacaoService');
        $dadosSituacao = $situacaoService->getCandidatoCargoEdital($idEdital,$idCargo,$idCandidato);
        $dadosSituacao ??= new SituacaoDTO();
        
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidato'     => $nomeCandidato,
            'idCandidato'   => $idCandidato,
            'idEdital'      => $idEdital,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $nomeCargo,
            'dadosSituacao' => $dadosSituacao,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Situação do Candidato",
        ];

        echo view('layoutDash', $parametros);
        
    }
    public function registrarSituacao(){
        $idCandidato = $this->request->getPost('id_candidato');
        $idCargo = $this->request->getPost('id_cargo');
        $idEdital = $this->request->getPost('id_edital');
        $situacao = $this->request->getPost('situacao');

        $situacaoDTO = SituacaoDTO::fromArray([
            'fk_id_candidato' => $idCandidato,
            'fk_id_cargo' => $idCargo,
            'fk_id_edital' => $idEdital,
            'situacao' => $situacao
        ]);

        $situacaoService = service('situacaoService');
        $dadosSituacaoExistente = $situacaoService->getCandidatoCargoEdital($idEdital,$idCargo,$idCandidato);
        
        if($dadosSituacaoExistente !== null){
            //atualizar
            $situacaoService->atualizar($dadosSituacaoExistente->id, $situacaoDTO);
            return redirect()->back()->with('mensagemSuccess', 'Situação atualizada com sucesso.');
        }else{
            //salvar
            $situacaoService->salvar($situacaoDTO);
            return redirect()->back()->with('mensagemSuccess', 'Situação registrada com sucesso.');
        }
    }

    
}