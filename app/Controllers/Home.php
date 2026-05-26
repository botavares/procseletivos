<?php

namespace App\Controllers;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Exceptions\PageNotFoundException;
use \App\Models\EditaisModel;
use \App\Models\CandidatosModel;
use \App\Models\EditaisCandidatosModel;
class Home extends BaseController
{
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Capa'){

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        //Editais
        if(!checklogged()){
            $layout = 'layoutSimples';
            $labeldoCaption = "Cadastrar";
            $dataSession = null;
            $edital = null;
            $status = "naoregistrado";
        }else{
            $layout = 'layoutLogado';
            $labeldoCaption = "Seus dados";
            $dataSession = $_SESSION;
            $cadastrados         = new CandidatosModel();
            $editais            = new EditaisModel();
            $editaisCandidatos  = new EditaisCandidatosModel();
            $dadosCandidatos = $cadastrados->where('pk_id_cadastrado', $dataSession['id'])->first();
            if($dadosCandidatos){
            $idCurso = $dadosCandidatos->fk_id_curso;
            
            $editaisCursos = $editais->editaisPorCurso($idCurso);
            $edital = null;
            if($editaisCursos){
                //verificar se candidato já possui vínculo com o edital
                $verificaVinculo = $editaisCandidatos->verificarVinculos($dataSession['id'], $editaisCursos->fk_id_edital);
                if(!$verificaVinculo){
                    //armazenar o id do edital para o candidato ter condições de vincular ao edital
                    $edital = $editaisCursos->fk_id_edital;
                    $status = "naovinculado";
                }else{
                    $ano = substr($editaisCursos->ds_numero_edital, -4);            // últimos 4 dígitos → ano
                    $numero = substr($editaisCursos->ds_numero_edital, 0, -4);      // o que sobra → número do edital
                    // remove zeros à esquerda
                    $numero = ltrim($numero, "0");
                    $edital = $numero . '/' . $ano;
                    $status = "vinculado";
                    $idCandidato = $dataSession['id'];
                }
            }else{
                $edital = null;
                $status = "naovinculado";
            }
        }else{
            $edital = null;
            $status = "naoregistrado";
        }
        }


        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'params'        =>  $dataSession,
            'pagina'        =>  $page,
            'edital'        =>  $edital,
            'status'        =>  $status,
            //'assinatura'    =>  "SECRETARIA MUNICIPAL DE PLANEJAMENTO, GESTÃO, CIÊNCIA E TECNOLOGIA",
            "labelCaption"  =>  $labeldoCaption,
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view($layout,$parametros);

    }

    public function acessarGovBr(){
        if(!checklogged()){
            $urlToGov = "https://app.prefeituradivinopolis.com.br/app/7ddde5c6897f39b7b139238d0dd94d7f?destino=Home";
            return redirect()->to($urlToGov);
            $dataSession = $this->loginGovBr();
        }
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
	public function buscarServicos(){
        if(isset($_GET['q'])){
            $busca = $_GET['q'];
            $servicosModel = new \App\Models\ServicosModel();
            $dataBusca = $servicosModel->listarServicos($busca);
            echo json_encode($dataBusca);
        }
    }
	
}
