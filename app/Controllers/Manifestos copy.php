<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use chillerlan\QRCode\{QRCode, QROptions};
use App\Modules\Breadcrumbs\Breadcrumbs;



    class Manifestos extends BaseController{
        protected $breadcrumbs;
    public function __construct(){
        $this->breadcrumbs = new Breadcrumbs();
        $this->breadcrumb = [
            'breads' => $this->breadcrumbs->buildAuto(),
        ];
		
    }
    

    public function index($camada1 = '', $camada2 = 'pages', $page = 'ManifestosEscolher'){
        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            throw PageNotFoundException::forPageNotFound();
        }
         //Verificar se há sessão ativa e se o acesso é permitido
        if(!checklogged()){
           //$urlToGov = "https://app.prefeituradivinopolis.com.br/app/eb501c6432777aac1d8a5208075a8a2b";
            $urlToGov = "https://dev.prefeituradivinopolis.com.br/app/c96e645df51d57e00bc78541415b0c91";
            return redirect()->to($urlToGov);
            $credenciais = $this->loginGovBr();
            $dataSession = [
                'su'        => $this->request->getGet('user'),
                'id'        => $credenciais->usuario->id,
                'email'     => $credenciais->usuario->email_govbr,
                'nome'      => $credenciais->usuario->nome,
                'cpf'       => $credenciais->usuario->cpf,
                'logged_in' => true
            ];
            $this->session->set($dataSession);
        }else{
            //buscar dados da sessão
            $dataSession = [
                'id'    => session('id'),
                'email' => session('email'),
                'cpf'   => session('cpf'),
                'nome'  => session('nome'),
            ];
        }
        $Manifestacoes    =   new \App\Models\ManifestacoesModel();
        $dadosManifestacoes = $Manifestacoes->listarManifestacoes();
        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'params'        =>  $dataSession,
            'manifestacoes' =>  $dadosManifestacoes,
            'titulo'        =>  ucfirst('Formas de Manifestar'),
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view('layoutLogado',$parametros);
       
       
        

        
            

    }
    public function formulario($id, $camada1 = '', $camada2 = 'pages', $page = 'FormularioManifestos'){
        //Verificar se há sessão ativa e se o acesso é permitido
        $credenciais = $this->loginGovBr();
        if($credenciais["acesso"] == '1'){
            $parametros = [
                'camada1'       =>  $camada1,
                'camada2'       =>  $camada2,
                'pagina'        =>  $page,
                'params'        =>  $credenciais,
                'id'            =>  $id,
                'titulo'        =>  ucfirst('Formulário para Manifestar'),
                'dataAtual'     =>  date('d/m/Y'),
            ];
            echo view('layoutLogado',$parametros);

        }

    }

    public function loginGovBr(){
        //CRIANDO A ARRAY COM OS DADOS DO LOGIN
        $data = array(
            'su' => $this->request->getVar('user'), // USER
            'ak' => "4536f180bdc0de3b1cf67f3f9a60ea86", // CHAVE DO APP teste
            'as' => "7762d57af7e926a4909827b337b05d5b", //secret teste
            //'ak' => "cf4fc13d89f312d4825c2c04e61427de", // CHAVE DO APP gov
            //'as' => "bf9b62d5e4c957fdfabaddaccf0d4b8a", // SECRET DO APP
        );

        //CRIANDO A URL COM OS DADOS DO LOGIN
        $url = "https://dev.prefeituradivinopolis.com.br/app-valid?" . http_build_query($data);

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
      return $response;
        

        
    }


    public function logOut(){
        $this->session->remove('logged_in');
        session()->destroy();
        return redirect()->route('home');
    }
}