<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use \App\Modules\Breadcrumbs\Breadcrumbs;
use \App\Models\EditaisCargosModel;
use \App\Models\CargosModel;
use \App\Models\ExperienciasModel;
use \App\Models\EscolaridadesModel;
use \App\Models\AperfeicoamentosModel;
use \App\Models\EditaisCandidatosModel;

use \App\Models\CargosExperienciasEditaisModel;
use \App\Models\CargosEscolaridadesEditaisModel;
use \App\Models\CargosAperfeicoamentosEditaisModel;
use \App\Models\ProtocolosModel;

use \App\Models\CadastradosExperienciasModel;
use \App\Models\CadastradosEscolaridadesModel;
use \App\Models\CadastradosAperfeicoamentosModel;

use \App\Models\CandidatosModel;
use \App\Models\EditaisModel;

use \App\Services\DadosContratadoService;
use \App\Services\VerificarContratosService;




    class Cadastros extends BaseController{
        protected $breadcrumbs;
       
    public function __construct(){
        $this->breadcrumbs = new Breadcrumbs();
        $this->breadcrumbs = [
            'breads' => $this->breadcrumbs->buildAuto(),
        ];
    }
    
    public function index($camada1 = '', $camada2 = 'pages', $page = 'OpcoesCadastro'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            throw PageNotFoundException::forPageNotFound();
        }
         //Verificar se há sessão ativa e se o acesso é permitido
        if(!checklogged()){
            $urlToGov = "https://app.prefeituradivinopolis.com.br/app/7ddde5c6897f39b7b139238d0dd94d7f?destino=Cadastros";
            return redirect()->to($urlToGov);
            $dataSession = $this->loginGovBr();
                    /*$dataSession = [
                    'su'        => '1234567',
                    'id'        => '204',
                    'email'     => 'breno.o.tavares@gmail.com',
                    'nome'      => 'Breno Oliveira Tavares',
                    'cpf'       => '03455783686',
                    'logged_in' => true
                ];*/
        }else{
            //buscar dados da sessão
            $dataSession = $_SESSION;

        }

        //Verificar se o candidato já possui registro com o gov.br
        $candidatos         = new CandidatosModel();
        
        $editais            = new EditaisModel();
        $dadosCandidatos    = $candidatos->where('ds_cpf', $dataSession['cpf'])->first();
        
        $editaisECargos     = new EditaisCargosModel();
        $editais            = $editaisECargos->getEditaisAtivosCargos();
        
        $protocoloModels    = new ProtocolosModel();
        $protocolos          = $protocoloModels->protocolosdoCandidato('fk_id_cadastrado', $dataSession['id']);
        //dd($protocolos);
        if($dadosCandidatos){
            if($dadosCandidatos->fk_id_gov == 0 || $dadosCandidatos->fk_id_gov == null){
                //atualizar o fk_id_gov do candidato
                $arrayGov = array(
                    'fk_id_gov' => $dataSession['id']
                );
                $candidatos->update($dadosCandidatos->pk_id_cadastrado, $arrayGov);
            }
            $status = "registrado";
        }else{
            $status = "naoregistrado";
        }
        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'status'        =>  $status,
            'editais'       =>  $editais,
            'candidato'    =>   $dataSession['id'],
            'params'        =>  $dataSession,
            'protocolos'    =>  $protocolos,
            'titulo'        =>  ucfirst('Dados Pessoais e Acadêmicos'),
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view('layoutLogado',$parametros);
    }

    public function dadosCandidato($id, $camada1 = '', $camada2 = 'pages', $page = 'FormularioPessoais'){
        //Verificar se há sessão ativa e se o acesso é permitido
        if(!checklogged()){
            return redirect()->to('Home');
        }else{
            //buscar dados da sessão
            $dataSession = $_SESSION;
        }
       

        //Hora do dia:
        $horaAtual = date('H');
        if ($horaAtual >= 5 && $horaAtual < 12) {
            $saudação = 'Bom dia';
        } elseif ($horaAtual >= 12 && $horaAtual < 18) {
            $saudação = 'Boa tarde';
        } else {
            $saudação = 'Boa noite';
        }
        // Verificar se candidato já possui registro na base de dados
        $candidatos = new \App\Models\CandidatosModel();
        $dadosCandidatos = $candidatos->where('fk_id_gov', $id)->first();
        if (!$dadosCandidatos) {
         $acaoCandidato = "create";

         
         $arrayCandidato = array(
            
            'fk_id_gov' => $dataSession['id'],
            'ds_nome' => $dataSession['nome'],
            'ds_cpf' => $dataSession['cpf'],
            'ds_email' => $dataSession['email'],
         );
         //tranformar array em objeto
         $dados =  json_decode(json_encode($arrayCandidato));
        $arrayBairro = array(
            'ds_nome_bairro' => '',
            'ds_uf' => '',
            'ds_cidade' => '',
            'ds_uf' => '',
         );
         //tranformar array em objeto
         $bairro =  json_decode(json_encode($arrayBairro));
         $arrayDeficiencia = array(
            'pk_id_pne' => 0,
            'ds_deficiencia' => '',
         );
         //tranformar array em objeto
         $deficiencia =  json_decode(json_encode($arrayDeficiencia));

        }else{
            $acaoCandidato = "update";
            $dados = $dadosCandidatos;

            $bairro = $dadosCandidatos->ds_nome_bairro;

            $arrayBairro = array(
                
                'ds_nome_bairro'    => $bairro,
                'ds_uf'             => $dados->ds_uf,
                'ds_cidade'         => $dados->ds_cidade,
             );
             
            //tranformar array em objeto
            $bairro =  json_decode(json_encode($arrayBairro));

            

            $deficiencias = new \App\Models\DeficienciasModel();
            $deficiencia = $deficiencias->where('pk_id_pne', $dados->fk_id_pne)->first();

            $arrayDeficiencia = array(
                'pk_id_pne' => $deficiencia->pk_id_pne,
                'ds_nome_pne' => $deficiencia->ds_nome_pne,
             );
              //tranformar array em objeto
         $deficiencia =  json_decode(json_encode($arrayDeficiencia));

        }
        

        $deficienciasModel  =   new \App\Models\DeficienciasModel();
        $dadosDeficiencias  =   $deficienciasModel->orderBy('pk_id_pne','asc')->findAll();
        
        $parametros = [
            'camada1'                   =>  $camada1,
            'camada2'                   =>  $camada2,
            'pagina'                    =>  $page,
            'params'                    =>  $dataSession,
            'acao'                      =>  $acaoCandidato,
            'dados'                     =>  $dados,
            'idCandidato'               =>  $id,
            'titulo'                    =>  ucfirst('Registrar seus dados pessoais'),
            'dataAtual'                 =>  date('d/m/Y'),
            'saudacao'                  =>  $saudação,
            'bairro'                    =>  $bairro,
            'deficiencias'              =>  $dadosDeficiencias,
            'deficiencia'               =>  $deficiencia
        ];
        echo view('layoutLogado',$parametros);

    }
    public function dadosClassificatorios($edital,$cargo,$id, $camada1 = '', $camada2 = 'pages', $page = 'FormularioClassificatorio'){
        if(!checklogged()){
            return redirect()->to('Home');
        }else{
            //buscar dados da sessão
            $dataSession = $_SESSION;
        }

         //Hora do dia:
         $horaAtual = date('H');
         if ($horaAtual >= 5 && $horaAtual < 12) {
             $saudação = 'Bom dia';
         } elseif ($horaAtual >= 12 && $horaAtual < 18) {
             $saudação = 'Boa tarde';
         } else {
             $saudação = 'Boa noite';
         }

        $dadosCandidatos = new CandidatosModel();
         // preparando dados para preencher o formulário parte classificatoria
        $cargos = new CargosModel();
        $dadosCargo = $cargos->where('pk_id_cargo', $cargo)->first();

        $escolaridades = new EscolaridadesModel();
        $escolaridadesObrigatórias      =   $escolaridades->listarStatusEscolaridades($cargo,'1'); // 1 = Obrigatório
        $escolaridadesClassificatorias	=	$escolaridades->listarStatusEscolaridades($cargo,'0'); // 0 = Classificatório
        
        $aperfeicoamentos = new AperfeicoamentosModel();
        $aperfeicoamentoObrigatorios    =	$aperfeicoamentos->listarStatusAperfeicoamentos($cargo, '1'); // 1 = Obrigatório
		$aperfeicoamentoClassificatorios=	$aperfeicoamentos->listarStatusAperfeicoamentos($cargo,'0'); // 0 = Classificatório
        
        
        //Identificar as experiencias exigidas para o cargo
        $experiencias = new ExperienciasModel();
        $experienciaProfissional   = $experiencias->listarExperienciaDoCargo($edital, $cargo);
        
        //verificar se o candidato já tem a experiencia registrada para esse cargo e edital
        $cadastradosExperiencias = new CadastradosExperienciasModel();
        $dadosExperiencia = $cadastradosExperiencias->where('fk_id_cadastrado', $id)
                                                    ->where('fk_id_cargo', $cargo)
                                                    ->where('fk_id_edital', $edital)
                                                    ->findAll();
        
         
        
        //verificar se o candidato já tem a escolaridade registrada para esse cargo e edital
        $cadastradosEscolaridades = new CadastradosEscolaridadesModel();
        $dadosEscolaridade = $cadastradosEscolaridades->where('fk_id_cadastrado', $id)
                                                    ->where('fk_id_cargo', $cargo)
                                                    ->where('fk_id_edital', $edital)
                                                    ->findAll();
        
        //verificar se o candidato já tem o aperfeicoamento registrado para esse cargo e edital
        $cadastradosAperfeicoamentos = new CadastradosAperfeicoamentosModel();
        $dadosAperfeicoamento = $cadastradosAperfeicoamentos->where('fk_id_cadastrado', $id)
                                                    ->where('fk_id_cargo', $cargo)
                                                    ->where('fk_id_edital', $edital)
                                                    ->findAll();
        
        $experienciasSalvas = [];
        foreach ($dadosExperiencia as $exp) {
            $experienciasSalvas[$exp->fk_id_experiencia] = $exp->ds_quantidade;
        }
        $data['experienciasSalvas'] = $experienciasSalvas;
        


        $idsEscolaridadesCandidato = [];
        foreach ($dadosEscolaridade as $registro) {
            $idsEscolaridadesCandidato[] = $registro->fk_id_escolaridade;
        }
        $idsAperfeicoamentosCandidato = [];
        foreach ($dadosAperfeicoamento as $registro) {
            $idsAperfeicoamentosCandidato[] = $registro->fk_id_curso;
        }
        
        $parametros = [
            'camada1'               =>  $camada1,
            'camada2'               =>  $camada2,
            'pagina'                =>  $page,
            'params'                =>  $dataSession,
            'experienciaProfissional'=>  $experienciaProfissional,
            'experienciasSalvas'     =>  $data['experienciasSalvas'],
            'escolaridadesObrigatórias' =>  $escolaridadesObrigatórias,
            'escolaridadesClassificatorias' =>  $escolaridadesClassificatorias,
            'aperfeicoamentoObrigatorios' =>  $aperfeicoamentoObrigatorios,
            'aperfeicoamentoClassificatorios' =>  $aperfeicoamentoClassificatorios,
            'idsEscolaridadesCandidato'   =>  $idsEscolaridadesCandidato,
            
            'idsAperfeicoamentosCandidato'   =>  $idsAperfeicoamentosCandidato,
            'dadosExperiencia'      =>  $dadosExperiencia,
            'dadosEscolaridade'     =>  $dadosEscolaridade,
            'dadosAperfeicoamento'  =>  $dadosAperfeicoamento,
            'idCandidato'           =>  $dataSession['id'],
            'idCargo'               =>  $cargo,
            'idEdital'              =>  $edital,
            'titulo'                =>  ucfirst('Registrar seus dados acadêmicos'),
            'dataAtual'             =>  date('d/m/Y'),
            'saudacao'              =>  $saudação,
            'cargos'                =>  $dadosCargo,
            
        ];
        echo view('layoutLogado',$parametros);

    }


    public function registrarDadosPessosais(){
    // Instancia o serviço de validação e o Model
    $validation = \Config\Services::validation();
    $candidatos = new \App\Models\CandidatosModel();

    if ($this->request->getMethod() === 'post') {
        // Obtendo valores do formulário
        $post = $this->request->getPost();

        // Formatação da data de nascimento
        list($dia, $mes, $ano) = explode('/', $post['ds_nascimento']);
        $nascimento = "$ano-$mes-$dia";

        // Identificação de ação
        $isCreate = ($post['acao'] ?? '') === "create";

        // Definição de datas
        $dataCadastro = $isCreate ? date('Y-m-d') : null;
        $horaCadastro = $isCreate ? date('H:i:s') : null;
        $dataAltera = !$isCreate ? date('Y-m-d') : null;
        $horaAltera = !$isCreate ? date('H:i:s') : null;

        //retirar os caracteres especiais do cpf
        $post['ds_cpf'] = preg_replace('/[^0-9]/', '', $post['ds_cpf']);

        //retirar os caracteres especiais do cep
        $post['ds_cep'] = preg_replace('/[^0-9]/', '', $post['ds_cep']);

        //retirar os caracteres especiais do celular
        $post['ds_celular'] = preg_replace('/[^0-9]/', '', $post['ds_celular']);

        //retirar os caracteres especiais do telefone fixo
        $post['ds_fixo'] = preg_replace('/[^0-9]/', '', $post['ds_fixo']);


        // Montagem do array de dados
        $dadosCadastros = [
            'fk_id_gov'         => $post['fk_id_gov'] ?? null,
            'ds_nome'           => $post['ds_nome'] ?? null,
            'ds_cpf'            => $post['ds_cpf'] ?? null,
            'ds_nascimento'     => $nascimento,
            'ds_nome_mae'       => $post['ds_nome_mae'] ?? null,
            'fk_id_pne'         => $post['fk_id_pne'] ?? null,
            'ds_outra_pne'      => $post['ds_outra_pne'] ?? null,
            'ds_cep'            => $post['ds_cep'] ?? null,
            'ds_rua'            => $post['ds_rua'] ?? null,
            'ds_numero'         => $post['ds_numero'] ?? null,
            'ds_complemento'    => $post['ds_complemento'] ?? null,
            'ds_nome_bairro'    => $post['ds_nome_bairro'] ?? null,
            'ds_cidade'         => $post['ds_cidade'] ?? null,
            'ds_uf'             => $post['ds_uf'] ?? null,
            'ds_celular'        => $post['ds_celular'] ?? null,
            'ds_fixo'           => $post['ds_fixo'] ?? null,
            'ds_email'          => $post['ds_email'] ?? null,
            'ds_data_cadastro'  => $dataCadastro,
            'ds_hora_cadastro'  => $horaCadastro,
            'ds_data_alteracao' => $dataAltera,
            'ds_hora_alteracao' => $horaAltera,
            'ds_ip_cadastro'    => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        // Define a ação (inserção ou atualização)
        if ($isCreate) {
            
            $resultado = $candidatos->insert($dadosCadastros);
            $id = $candidatos->insertID();
           
        } else {
            $dadosCandidatos = $candidatos->where('fk_id_gov', $post['fk_id_gov'])->first();
            $id = $dadosCandidatos->pk_id_cadastrado ?? null;
            $resultado = $candidatos->update($id, $dadosCadastros);
        }

        if ($resultado) {
            return redirect()->route('Cadastros', [$post['fk_id_gov'] ?? ''])->with('mensagemSuccess', $isCreate ? 'Registro gravado com sucesso' : 'Registro alterado com sucesso');
        } else {
            return redirect()->route('dadosPessoais', [$post['fk_id_gov'] ?? ''])->withInput()->with('errors', $candidatos->errors());
        }
    }
}
public function registrarDadosClassificatorios(){
    if (!$this->request->getMethod() === 'post') {
        return;
    }
    // Obtendo valores do formulário
        $post = $this->request->getPost();
    // Identificação de ação
        $isCreate = ($post['acao'] ?? '') === "create";
    // id essenciais
        $codigoCargo        = $post['idCargo'] ?? null;
        $codigoEdital       = $post['idEdital'] ?? null;
        $codigoCandidato    = $post['idCandidato'] ?? null;
        $candidatosModel = new CandidatosModel();
        $dadosCandidatos = $candidatosModel->where('fk_id_gov', $codigoCandidato)->first();
        $cpfCandidato = $dadosCandidatos->ds_cpf ?? null;
        $cpfCandidato = preg_replace('/[^0-9]/', '', $cpfCandidato);
            
    // Definição de datas
        $dataCadastro = $isCreate ? date('Y-m-d') : null;
        $horaCadastro = $isCreate ? date('H:i:s') : null;
        $dataAltera = !$isCreate ? date('Y-m-d') : null;
        $horaAltera = !$isCreate ? date('H:i:s') : null;

    // adquirindo informação do cargo
        $CargosModel = new CargosModel();
        $dadosCargo = $CargosModel->where('pk_id_cargo', $codigoCargo)->first();
    // adquirindo informação do edital
        $EditaisModel = new EditaisModel();
        $dadosEdital = $EditaisModel->where('pk_id_edital', $codigoEdital)->first();

    //obtendo experiencia do formulário
        $experienciaCargoModel = new CargosExperienciasEditaisModel();
        $experienciaCargo = $experienciaCargoModel->where('fk_id_cargo', $codigoCargo)->findAll();
        $dadosExperiencia = [];
        
        if($experienciaCargo){
            foreach($experienciaCargo as $experiencia){
                if($experiencia->ds_obrigatorio == "0"){
                    if($experiencia->ds_tipo_campo == "INPUT" || $experiencia->ds_tipo_campo == "CHECK" || $experiencia->ds_tipo_campo == "SELECT"){
                        $idExperiencia  = $experiencia->fk_id_experiencia;
                        $status         = $experiencia->ds_obrigatorio;
                        $quantidadeXp   = $post["quantidadeExperiencia".$experiencia->fk_id_experiencia] ?? null;
                    }
                }
                $dadosExperiencias[] = [
                    "id_edital"         => $codigoEdital,
                    "id_cargo" 		    => $codigoCargo,
                    "status" 			=> $status,
                    "id_experiencia"	=> $idExperiencia,
                    "ds_quantidade"	    => $quantidadeXp,
                    "ds_multiplicador"	=> $experiencia->ds_multiplicador,
                ];
            }
        }else{
            $dadosExperiencia = [];
        }
       // obtendo dados de escolaridade
            $escolaridadeCargoModel = new CargosEscolaridadesEditaisModel();
            $escolaridadeCargo = $escolaridadeCargoModel->where('fk_id_cargo', $codigoCargo)
                                                            ->findAll();
            
            if($escolaridadeCargo){
                $dadosEscolaridade = array();
                $esc = 0;
                foreach($escolaridadeCargo as $escolaridade){
                    $idEscolaridade = $escolaridade->fk_id_escolaridade;
                    $status = $escolaridade->ds_obrigatorio;
                    $quantidadeEscolaridade = isset($post["escolaridade".$escolaridade->fk_id_escolaridade]) ? $post["escolaridade".$escolaridade->fk_id_escolaridade] : null;
                    $dadosEscolaridades[$esc]["id_edital"] 		    = $codigoEdital;
                    $dadosEscolaridades[$esc]["id_cargo"] 		    = $codigoCargo;
                    $dadosEscolaridades[$esc]["status"] 			= $status;
                    $dadosEscolaridades[$esc]["id_escolaridade"]	= $idEscolaridade;
                    $dadosEscolaridades[$esc]["ds_quantidade"]      = $quantidadeEscolaridade;
                    $dadosEscolaridades[$esc]["ds_multiplicador"]	= $escolaridade->ds_multiplicador;
                    $esc++;
                }
            }else{
                $dadosEscolaridades = [];
            }
        //obtendo dados de cursos de aperfeiçoamento
            $aperfeicoamentoCargoModel = new CargosAperfeicoamentosEditaisModel();
            $aperfeicoamentoCargo = $aperfeicoamentoCargoModel->where('fk_id_cargo', $codigoCargo)
                                                        ->findAll();
            if($aperfeicoamentoCargo){
                $dadosAperfeicoamento = array();
                $ap = 0;
                foreach($aperfeicoamentoCargo as $aperfeicoamento){
                    $idAperfeicoamento = $aperfeicoamento->fk_id_curso;
                    $status = $aperfeicoamento->ds_obrigatorio;

                    $quantidadeAperfeicoamento = isset($post["aperfeicoamento".$aperfeicoamento->fk_id_curso]) ? $post["aperfeicoamento".$aperfeicoamento->fk_id_curso] : null;
                    
                    $dadosAperfeicoamentos[$ap]["id_edital"] 		    = $codigoEdital;
                    $dadosAperfeicoamentos[$ap]["id_cargo"] 		    = $codigoCargo;
                    $dadosAperfeicoamentos[$ap]["status"] 			= $status;
                    $dadosAperfeicoamentos[$ap]["id_aperfeicoamento"]	= $idAperfeicoamento;
                    $dadosAperfeicoamentos[$ap]["ds_quantidade"]      = $quantidadeAperfeicoamento;
                    $dadosAperfeicoamentos[$ap]["ds_multiplicador"]	= $aperfeicoamento->ds_multiplicador;
                    $ap++;
                }
            }else{
                $dadosAperfeicoamentos = [];
            } 
            $protocoloModels = new ProtocolosModel();
            $protocolos = $protocoloModels->where('fk_id_cadastrado', $codigoCandidato)
                                         ->where('fk_id_cargo', $codigoCargo)
                                         ->where('fk_id_edital', $codigoEdital)
                                         ->first();
            $protocolo = $protocolos->ds_protocolo ?? null;
            if(empty($protocolo)){
                $protocolo = $dadosCargo->fk_id_secretaria.$codigoCargo.$codigoEdital.mt_rand(100000,999999);
            }
            $dadosProtocolo = array(
					"fk_id_cadastrado"		=>	$codigoCandidato,
                    "fk_id_secretaria"		=>	$dadosCargo->fk_id_secretaria ?? null,
                    "fk_id_edital"			=>	$codigoEdital,
                    "fk_id_cargo"			=>	$codigoCargo,
					"ds_cpf_cadastrado"		=>	$cpfCandidato,
					"ds_protocolo"			=>	$protocolo,
					"ds_data_protocolo"		=>	date('Y-m-d'),
					"ds_hora_protocolo"		=>	date('H:i:s'),
					//"ds_ip_protocolo"		=>	$this->request->getUserIP(),
				);
            $dadosCandidato = array(
                "idCandidato" => $codigoCandidato,
                "acao" => $isCreate ? "create" : "update",
            );
            
            //gravando experiencia
                $gravarExperiencia = $this->gravarExperiencia($codigoCandidato, $dadosExperiencias);
            //gravando escolaridade
                $gravarEscolaridade = $this->gravarEscolaridade($codigoCandidato, $dadosEscolaridades);
            //gravando cursos de aperfeiçoamento
                $gravarAperfeicoamento = $this->gravarAperfeicoamento($codigoCandidato, $dadosAperfeicoamentos);
            //gravando protocolo
                $gravarProtocolo = $this->gravarProtocolo($dadosCandidato, $dadosProtocolo);
                
            if ($gravarProtocolo) {
                return redirect()
                                ->route(
                                    'sucessoClassificatorio',[$codigoCandidato ?? '',$codigoCargo ?? '',$codigoEdital ?? '']
                                )->with('mensagemSuccess', 'Registro atualizado com sucesso!');
            }
        }

            
   

    public function gravarExperiencia($idCandidato, $dadosExperiencias){
        $cadastradosExperiencias = new CadastradosExperienciasModel();
        // Excluir registros anteriores do candidato para o cargo e edital se array não estiver vazio
        if(!empty($dadosExperiencias)){
            $cadastradosExperiencias->where('fk_id_cadastrado', $idCandidato)
                                ->where('fk_id_cargo', $dadosExperiencias[0]['id_cargo'])
                                ->where('fk_id_edital', $dadosExperiencias[0]['id_edital'])
                                ->delete();
        }
        
        // Inserir novos registros
        
        foreach ($dadosExperiencias as $experiencia) {
            //gravar apenas se $experiencia->ds_quantidade for maior que zero e não estiver vazio
            if(empty($experiencia['ds_quantidade']) || $experiencia['ds_quantidade'] == 0){
                continue;
            }
            $registro = [
                'fk_id_cadastrado' => $idCandidato,
                'fk_id_cargo' => $experiencia['id_cargo'],
                'fk_id_edital' => $experiencia['id_edital'],
                'fk_id_experiencia' => $experiencia['id_experiencia'],
                'ds_quantidade' => $experiencia['ds_quantidade'],
                'ds_multiplicador' => $experiencia['ds_multiplicador'],
                'ds_obrigatorio' => $experiencia['status']
            ];
            
            if($cadastradosExperiencias->insert($registro)){
                
            }
        }
    }
    public function gravarEscolaridade($idCandidato, $dadosEscolaridades){
        $cadastradosEscolaridades = new CadastradosEscolaridadesModel();
        // Excluir registros anteriores do candidato para o cargo e edital se array estiver cheia
        if(!empty($dadosEscolaridades)){
            $cadastradosEscolaridades->where('fk_id_cadastrado', $idCandidato)
                                ->where('fk_id_cargo', $dadosEscolaridades[0]['id_cargo'])
                                ->where('fk_id_edital', $dadosEscolaridades[0]['id_edital'])
                                ->delete();
        }
        
        // Inserir novos registros
        
        foreach ($dadosEscolaridades as $escolaridade) {
            if(empty($escolaridade["ds_quantidade"]) || $escolaridade['ds_quantidade'] == 0){
                continue;
            }
            $registro = [
                'fk_id_cadastrado' => $idCandidato,
                'fk_id_edital' => $escolaridade['id_edital'],
                'fk_id_cargo' => $escolaridade['id_cargo'],
                'fk_id_escolaridade' => $escolaridade['id_escolaridade'],
                'ds_status' => $escolaridade['status'],
                'ds_quantidade' => $escolaridade['ds_quantidade'],
                'ds_multiplicador' => $escolaridade['ds_multiplicador'],
            ];
            if($cadastradosEscolaridades->insert($registro)){
                
            }
        }
    }
    public function gravarAperfeicoamento($idCandidato, $dadosAperfeicoamentos){
        $cadastradosAperfeicoamentos = new CadastradosAperfeicoamentosModel();
        // Excluir registros anteriores do candidato para o cargo e edital se array estiver cheio
        if(!empty($dadosAperfeicoamentos)){
            $cadastradosAperfeicoamentos->where('fk_id_cadastrado', $idCandidato)
                                ->where('fk_id_cargo', $dadosAperfeicoamentos[0]['id_cargo'])
                                ->where('fk_id_edital', $dadosAperfeicoamentos[0]['id_edital'])
                                ->delete();
        }

        // Inserir novos registros
        
        foreach ($dadosAperfeicoamentos as $aperfeicoamento) {
            if(empty($aperfeicoamento['ds_quantidade']) || $aperfeicoamento['ds_quantidade'] == 0){
                continue;
            }
            $registro = [
                'fk_id_cadastrado'  => $idCandidato,
                'fk_id_edital'      => $aperfeicoamento['id_edital'],
                'fk_id_cargo'       => $aperfeicoamento['id_cargo'],
                'fk_id_curso'       => $aperfeicoamento['id_aperfeicoamento'],
                'ds_quantidade'     => $aperfeicoamento['ds_quantidade'],
                'ds_multiplicador'  => $aperfeicoamento['ds_multiplicador'],
                'ds_status'         => $aperfeicoamento['status']
            ];
            if($cadastradosAperfeicoamentos->insert($registro)){
                
            }
        }
    }

    function gravarProtocolo($dadosCandidato,$dadosProtocolo){
        $candidatosModel = new CandidatosModel();
        $protocoloModel = new ProtocolosModel();

        $protocoloExistente = $protocoloModel->where('fk_id_cadastrado', $dadosProtocolo['fk_id_cadastrado'])
                                            ->where('fk_id_cargo', $dadosProtocolo['fk_id_cargo'])
                                            ->where('fk_id_edital', $dadosProtocolo['fk_id_edital'])
                                            ->first();
        if($protocoloExistente){
            if($protocoloModel
                    ->where([
                            "fk_id_cadastrado" => $dadosProtocolo["fk_id_cadastrado"],
                            "fk_id_secretaria" => $dadosProtocolo["fk_id_secretaria"],
                            "fk_id_cargo" => $dadosProtocolo["fk_id_cargo"],
                            "fk_id_edital" => $dadosProtocolo["fk_id_edital"],
                    ])
                    ->set($dadosProtocolo)
                    ->update()
            ){
                return true;
            }else{
                //direcionar para uma página de erro ou exibir mensagem de erro
                
            }
        }else{
             if($protocoloModel->insert($dadosProtocolo)){
                return true;
             }else{
                //direcionar para uma página de erro ou exibir mensagem de erro
             }
        }
	}

    public function sucessoPessoais($idCandidato, $camada1 = '', $camada2 = 'pages', $page = 'SucessoPessoais'){
        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'idCandidato'   =>  $idCandidato,
            'titulo'        =>  ucfirst('Registro concluído!'),
            'dataAtual'     =>  date('d/m/Y'),
        ];
        
        echo view('layoutLogado',$parametros);
    }

    public function sucessoClassificatorio($idCandidato,$idCargo,$idEdital, $camada1 = '', $camada2 = 'pages', $page = 'SucessoClassificatorio'){
        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'idCandidato'   =>  $idCandidato,
            'idCargo'      =>  $idCargo,
            'idEdital'     =>  $idEdital,
            'titulo'        =>  ucfirst('Registro concluído!'),
            'dataAtual'     =>  date('d/m/Y'),
        ];
        
        echo view('layoutLogado',$parametros);
    }

    /*===========================================================================================================
    VÍNCULOS DE CANDIDATOS A EDITAIS
    ===========================================================================================================*/
    public function vincularCandidatoEdital($id = null, $idEdital = null){
        $modelCandidatos        = new CandidatosModel();       
        $modelEditais           = new EditaisModel();
        $modelEditaisCandidatos = new EditaisCandidatosModel();
        $modelAcademicos        = new AcademicosModel();
        $modelEditaisCursos     = new EditaisCursosModel();

        // Verificar dados do candidato
        $dadosCandidatos = $modelCandidatos->where('fk_id_gov', $id)->first();
        if (!$dadosCandidatos) {
            return redirect()->to('Home');
        }
        // Verificar dados acadêmicos
        $dadosAcademicos = $modelAcademicos->find($dadosCandidatos->pk_id_cadastrado);


        $service = new VinculoCandidatoService();
        $service->registrarVinculoCurso($dadosCandidatos->pk_id_cadastrado, $dadosAcademicos->fk_id_curso);
         if ($service) {
                return redirect()
                    ->route('sucessoAcademicos', [$id])
                    ->with('mensagemSuccess', 'Registro atualizado com sucesso!');
            }

    }
    /*===========================================================================================================
    DISPARA PÁGINA DE ERRO
    ===========================================================================================================*/
    public function Error($idCandidato, $camada1 = '', $camada2 = 'pages', $page = 'PaginaErro'){
        
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'idCandidato'   =>  $idCandidato,
            'titulo'        =>  ucfirst('Erro ao registrar!'),
            'dataAtual'     =>  date('d/m/Y'),
        ];
        
        echo view('layoutLogado',$parametros);
    }

    public function verificar($token, $camada1 = '', $camada2 = 'pages', $page = 'PaginaVerificarContratos'){
        $service = new VerificarContratosService();
        $resultado = $service->verificarContrato($token);
        if($resultado != false){
            $dadosContrato = $resultado;
        }else{
            $dadosContrato = null;
        }

        if(!checklogged()){
            $layout = 'layoutSimples';
        }else{
            $layout = 'layoutLogado';
        }
        $parametros = [
                'camada1'       =>  $camada1,
                'camada2'       =>  $camada2,
                'pagina'        =>  $page,
                'dadosContrato'   =>  $resultado,
                'titulo'        =>  ucfirst('Verificação de Contrato'),
                'dataAtual'     =>  date('d/m/Y'),
            ];
            echo view($layout,$parametros);
        
    }
    public function gerarComprovante($id, $idCargo, $idEdital){
        $modelCargos            = new \App\Models\CargosModel();
        $modelCandidatos        = new \App\Models\CandidatosModel();
        $modelProtocolos        = new \App\Models\ProtocolosModel();
        $modelEditais            = new \App\Models\EditaisModel();

        $dadosCandidatos    = $modelCandidatos->where('fk_id_gov', $id)->first();
        $nomeCargo          = $modelCargos->where('pk_id_cargo', $idCargo)->select('ds_nome_cargo')->first();
        $dadosEdital        = $modelEditais->where('pk_id_edital', $idEdital)->first();
        $dadosProtocolos    = $modelProtocolos->where('fk_id_cadastrado', $id)
                                              -> where('fk_id_cargo', $idCargo)
                                              -> where('fk_id_edital', $idEdital)
                                              ->first();
        
        if(!$dadosProtocolos){
             return redirect()->route('home')->with('mensagemError', 'Protocolo não encontrado para este candidato, cargo e edital.');
            
        }else{
            // Formata o número do edital com a máscara 012026 para 01/2026
            $ano = substr($dadosEdital->ds_numero_edital, -4);            // últimos 4 dígitos → ano
            $numero = substr($dadosEdital->ds_numero_edital, 0, -4);      // o que sobra → número do edital
            // remove zeros à esquerda
            $numero = ltrim($numero, "0");
            $numeroEdital = "Edital " .$numero . '/' . $ano;

        }

		switch($dadosCandidatos->fk_id_pne){
					case 1:
						$def = "não possui deficiência";
						break;
					case 2:
						$def = "possui deficiência auditiva";
						break;
					case 3:
						$def = "possui deficiência física";
						break;
					case 4:
						$def = "possui deficiência múltipla";
						break;
					case 5:
						$def = "possui deficiência mental";
						break;
					case 6:
						$def = "possui deficiência reabilitado";
						break;
					case 7:
						$def = "possui deficiência visual";
						break;
					case 8:
						$def = "possui deficiência: ".$dadosCandidatos->ds_outra_pne;
						break;
			}


        $dompdf     =   new Dompdf();
        $dados = array(
            'brasao'            =>  imageToBase64(ROOTPATH . '/external/img/brasao.png'),
			'fundo'		        =>	imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'edital'            =>  $numeroEdital,
            'dadosPessoais'     =>  $dadosCandidatos,
            'protocolo'         =>  $dadosProtocolos->ds_protocolo,
            'nascimento'        =>  date('d/m/Y', strtotime($dadosCandidatos->ds_nascimento)),
            'nomeCargo'         =>  $nomeCargo->ds_nome_cargo,
            'deficiencia'       =>  $def,
            'dataCadastro'      =>  date('d/m/Y', strtotime($dadosCandidatos->ds_data_cadastro)), 
            'horaCadastro'      =>  date('H:i:s', strtotime($dadosCandidatos->ds_hora_cadastro)),
            //'ip'                =>  $dadosCandidatos->pk_id_cadastrado,
        );
        $nomeDocumento = 'comprovante';
        imprimir($dompdf, $nomeDocumento, $dados);
    }

    
    public function logOut(){
        $this->session->remove('logged_in');
        $su = session()->get('su');
        session()->destroy();
        return redirect()->route('home');
        //return redirect()->route('home');
    }

    
    public function loginGovBr(){
        //CRIANDO A ARRAY COM OS DADOS DO LOGIN
        $data = array(
            'su' => $this->request->getVar('user'), // USER
            //'ak' => "4536f180bdc0de3b1cf67f3f9a60ea86", // CHAVE DO APP teste
            //'as' => "7762d57af7e926a4909827b337b05d5b", //secret teste
            'ak' => "0b7f390e92176b48bdd12a6488dcd547", // CHAVE DO APP gov
            'as' => "04a3ae30dba5b02989d10cb58cd2a9e9", // SECRET DO APP
        );
      
        //CRIANDO A URL COM OS DADOS DO LOGIN
        $url = "https://app.prefeituradivinopolis.com.br/app-valid?" . http_build_query($data);
       

        //INICIAR O CURL
        $cred = curl_init();

        //CRIAR AOS OPÇÕES PARA O CURL
        curl_setopt_array($cred,[
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, // RETORNA A RESPOSTA EM TEXTO
            CURLOPT_HTTPGET => true, // ENVIA AS INFORMAÇOES COMO GET
        ]);

        //EXECUTAR O CURL
        $response = curl_exec($cred);
        //CHECAR SE HOUVE ERRO
        if(curl_errno($cred)){
            $error_msg = curl_error($cred);
            curl_close($cred);
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => $error_msg]);
        }
        curl_close($cred);
        // DECODE DA RESPOSTA
    
      
        //return $this->response->setJSON(['response' => json_decode($response, true)]);

        $response = json_decode($response);
      

        $dataSession = [
            'su'        => $this->request->getVar('user'),
            'id'        => $response->usuario->id,
            'email'     => $response->usuario->email_govbr,
            'nome'      => $response->usuario->nome,
            'cpf'       => $response->usuario->cpf,
            'logged_in' => true
        ];
        $this->session->set($dataSession);
        return redirect()->to($this->request->getVar('destino'));
    }

    

}