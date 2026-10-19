<?php
function gerarLog($dadosLog){
    $diretorio = FCPATH . '/external/logs';
    if (!file_exists($diretorio)){
        // create directory/folder uploads.
        mkdir($diretorio, 0777, true);
    }
    $log_file_data = $diretorio.'/log_' . date('d-M-Y') . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    file_put_contents($log_file_data, $dadosLog . "\n", FILE_APPEND);
}

function checklogged(){
    if (session('logged_in') == true) {
        return true;
    }
}
 
if ( !function_exists('jsonMe')){
    function jsonMe($data=NULL){
        $json =  json_encode($data);
        echo $json;
    }
 }
 if(! function_exists('sendMeAuth')){
	/*=================================================
	Nome:sendMeAuth
	Função: Retona uma sequencia de caracteres para codigo de autenticacao
	Modificação: 23/08/2018
	===================================================*/
	function sendMeAuth(){
        require_once ROOTPATH . 'vendor/autoload.php';
		//require_once APPPATH . 'vendor/autoload.php';
		$numero_de_bytes = 4;
		$round1 = random_bytes($numero_de_bytes);
		$round2 = random_bytes($numero_de_bytes);
		$round3 = random_bytes($numero_de_bytes);
		$round4 = random_bytes($numero_de_bytes);
		
		
		$hexa1 = bin2hex($round1);
		$hexa2 = bin2hex($round2);
		$hexa3 = bin2hex($round3);
		$hexa4 = bin2hex($round4);
		$autenticador = strtoupper($hexa1."-".$hexa2."-".$hexa3."-".$hexa4);
		return $autenticador;
	}
	
}
if(! function_exists('sendMeProtocol')){
	function sendMeProtocol($tabela,$campo){
		
        $protocolo = mt_rand(100000,999999);

        $db  = \Config\Database::connect();
        $builder = $db->table($tabela);
        $builder->select('*');
        $builder->where($campo,$protocolo);
        $query = $builder->get();


        
        if(!$query->getResult()){
			return $protocolo;
            
		}else{
			sendMeProtocol($tabela,$campo);
		}
	}
}
if(!function_exists ('imprimirMPDF')){
	function imprimirMPDF($instancia,$nomeDocumento,$dados){
		require_once FCPATH . 'vendor/autoload.php';
		
		$html = view('impressos/'.$nomeDocumento,$dados);
		$instancia->WriteHTML($html);
		$this->response->setHeader('Content-Type', 'application/pdf');
		$instancia->Output($nomeDocumento.'.pdf','I');
	}
}
if(!function_exists ('imprimir')){
	function imprimir($instancia, $nomeDocumento,$dados){
		require_once FCPATH . 'vendor/autoload.php';
		$html = view('impressos/'.$nomeDocumento, $dados);
		$instancia->loadHtml($html);
		$instancia->loadHtml($html);
		$instancia->render();
        // Adiciona a numeração de página no rodapé
        $canvas = $instancia->getCanvas();
        $font = $instancia->getFontMetrics()->getFont('Helvetica');
        $size = 8;
        $color = [0, 0, 0];
        $width = $canvas->get_width();
        $height = $canvas->get_height();
        $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
        $canvas->page_text($width - 100, $height - 15, $text, $font, $size, $color);
		$instancia->stream($nomeDocumento.'.pdf', [ 'Attachment' => false ]);
	}
	
}
function imageToBase64($path) {
	$path = $path;
	$type = pathinfo($path, PATHINFO_EXTENSION);
	$data = file_get_contents($path);
	$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
	return $base64;
}
/*=====================================================
Nome: getIP
Função: Retorna o IP do cliente
Modificação: 23/08/2018
=====================================================*/
function getIP() {
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	   $ip = $_SERVER['HTTP_CLIENT_IP'];
   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   } else {
	   $ip = $_SERVER['REMOTE_ADDR'];
   }
   return $ip;
}

	/*=================================================
	Nome:mask
	Função: cria uma máscara para a variável desejada
	Modificação: 23/08/2018
	===================================================*/
	function mask($val, $mask){
		$maskared = '';
		$k = 0;
		for($i = 0; $i<=strlen($mask)-1; $i++){
			if($mask[$i] == '#'){
				if(isset($val[$k]))
					$maskared .= $val[$k++];
			}else{
				if(isset($mask[$i]))
				   $maskared .= $mask[$i];
		   }
			}
			return $maskared;
   }
   /*================================================
   Nome: postFile;
   Função: Postar arquivo
   Modificação: 26/09/2024
   ==================================================*/
   
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\RequestInterface;

if (!function_exists('postFile')) {

    /**
     * Função para postar arquivos após validação de formulário
     * 
     * @param RequestInterface $request O objeto da requisição
     * @param string $inputName Nome do campo do arquivo
     * @param array $tiposPermitidos Tipos de arquivos permitidos
     * @param string $uploadPath Caminho onde os arquivos serão salvos
     * @return array
     */
    function postFile(RequestInterface $request, string $inputName, array $tiposPermitidos, string $uploadPath, string $protocolo)
    {
        $file = $request->getFile($inputName);

        // Inicializando retorno padrão
        $result = [
            'success' => false,
            'filePath' => null,
            'error' => null
        ];

        // Verifica se o arquivo foi enviado corretamente
        if (!$file || !$file->isValid()) {
            $result['error'] = 'Ocorreu um erro ao enviar o arquivo.';
            return $result;
        }

        // Validação de tipo de arquivo
        if (!in_array(strtolower((string)$file->getClientExtension()), $tiposPermitidos)) {
            $result['error'] = 'O arquivo enviado não é de um formato permitido.';
            return $result;
        }

        // Garante que o diretório exista
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Tenta mover o arquivo para o caminho de upload
        try {
            $newFileName = $protocolo  . '.' . $file->getClientExtension();

            $fullPath   = rtrim($uploadPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFileName;

            // Remove arquivo antigo
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $file->move($uploadPath, $newFileName);
            $result['success'] = true;
            $result['filePath'] = $uploadPath . '/' . $file->getName();
        } catch (\Exception $e) {
            $result['error'] = 'Erro ao salvar o arquivo: ' . $e->getMessage();
        }

        return $result;
    }
}
if (!function_exists('postFiles')) {

    /**
     * Função para postar múltiplos arquivos
     * 
     * @param RequestInterface $request O objeto da requisição
     * @param string $inputName Nome do campo de arquivo
     * @param array $allowedTypes Tipos de arquivos permitidos
     * @param string $uploadPath Caminho onde os arquivos serão salvos
     * @param string $protocolo Valor para renomear os arquivos
     * @return array
     */
    function postFiles(RequestInterface $request, string $inputName, array $allowedTypes, string $uploadPath, string $protocolo){
       
        $files = $request->getFileMultiple($inputName);
       

        $result = [
            'success' => true,
            'uploadedFiles' => [],
            'errors' => []
        ];

        if (!empty($files[0]->getName())) {
           
            $counter = 1;

            foreach ($files as $file) {
                if ($file->isValid() && ! $file->hasMoved()) {

                    // Verifica se a extensão é permitida
                    if (!in_array($file->getClientExtension(), $allowedTypes)) {
                        $result['success'] = false;
                        $result['errors'][] = "O arquivo {$file->getName()} não é de um formato permitido.";
                        continue;
                    }

                    // Renomeia o arquivo
                    $newFileName = $protocolo . '_' . $counter . '.' . $file->getClientExtension();
                    $counter++;

                    try {
                        // Move o arquivo para o caminho de upload com o novo nome
                        $file->move($uploadPath, $newFileName);
                        $result['uploadedFiles'][] = $uploadPath . '/' . $newFileName;
                    } catch (\Exception $e) {
                        $result['success'] = false;
                        $result['errors'][] = "Erro ao salvar o arquivo {$file->getName()}: " . $e->getMessage();
                    }

                } else {
                    $result['success'] = true;
                    $result['errors'][] = "O arquivo {$file->getName()} não pôde ser enviado.";
                }
            }
        } else {
            $result['success'] = true;
            $result['errors'][] = 'Nenhum arquivo foi enviado.';
        }

        return $result;
    }
}
    if(!function_exists('logarGovBR')) {
        function logarGovBR($destino) {
            //CRIANDO A ARRAY COM OS DADOS DO LOGIN
            $data = array(
                'su' => 'eb501c6432777aac1d8a5208075a8a2b',//$this->request->getVar('user'), // USER
                //'ak' => "4536f180bdc0de3b1cf67f3f9a60ea86", // CHAVE DO APP teste
                //'as' => "7762d57af7e926a4909827b337b05d5b", //secret teste
                'ak' => "cf4fc13d89f312d4825c2c04e61427de", // CHAVE DO APP gov
                'as' => "bf9b62d5e4c957fdfabaddaccf0d4b8a", // SECRET DO APP
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
                    'su'        => 'eb501c6432777aac1d8a5208075a8a2b',//$this->request->getVar('user'),
                    'id'        => $response->usuario->id,
                    'email'     => $response->usuario->email_govbr,
                    'nome'      => $response->usuario->nome,
                    'cpf'       => $response->usuario->cpf,
                    'logged_in' => true
                ];
                $this->session->set($dataSession);
                return redirect()->to($this->request->getVar($destino));
        }
    }

    function gerarNumeroOficio(){
        $db = \Config\Database::connect();
        $anoAtual = date('Y');

        // Conta quantos ofícios já existem para o ano atual
        $query = $db->query("SELECT COUNT(*) AS total FROM tb_manifestos WHERE YEAR(ds_datacadastro) = ?", [$anoAtual]);
        $resultado = $query->getRow();
        $contador = $resultado->total + 1; // Próximo número na sequência

        // Formata o número do ofício como "NÚMERO/ANO"
        $numeroOficio = $contador . '/' . $anoAtual;

        return $numeroOficio;
    }
    if(!function_exists('mesPorExtenso')){ 
        function mesPorExtenso($mes){
            $meses = array(
                1 => 'Janeiro',
                2 => 'Fevereiro',
                3 => 'Março',
                4 => 'Abril',
                5 => 'Maio',
                6 => 'Junho',
                7 => 'Julho',
                8 => 'Agosto',
                9 => 'Setembro',
                10 => 'Outubro',
                11 => 'Novembro',
                12 => 'Dezembro'
            );
            return $meses[$mes];
        }

    }


if (!function_exists('valor_por_extenso')) {

    /**
     * Converte um valor numérico em extenso monetário (Real / Reais).
     * Ex.: 1520.35 -> "um mil quinhentos e vinte reais e trinta e cinco centavos"
     */
    function valor_por_extenso($valor = 0)
    {
        $valor = number_format($valor, 2, '.', '');
        $partes = explode('.', $valor);

        $reais    = (int) $partes[0];
        $centavos = (int) $partes[1];

        $extensoReais = numero_por_extenso($reais);
        $extensoCentavos = numero_por_extenso($centavos);

        // Montagem de reais
        if ($reais == 0) {
            $textoReais = "";
        } elseif ($reais == 1) {
            $textoReais = $extensoReais . " real";
        } else {
            $textoReais = $extensoReais . " reais";
        }

        // Montagem de centavos
        if ($centavos == 0) {
            $textoCentavos = "";
        } elseif ($centavos == 1) {
            $textoCentavos = $extensoCentavos . " centavo";
        } else {
            $textoCentavos = $extensoCentavos . " centavos";
        }

        // Combinações
        if ($reais > 0 && $centavos > 0) {
            return $textoReais . " e " . $textoCentavos;
        } elseif ($reais > 0) {
            return $textoReais;
        } else {
            return $textoCentavos;
        }
    }
}


/**
 * Função auxiliar: converte números inteiros em texto.
 */
if (!function_exists('numero_por_extenso')) {

    function numero_por_extenso($numero)    {
        $unidades = [
            "", "um", "dois", "três", "quatro", "cinco", "seis",
            "sete", "oito", "nove", "dez", "onze", "doze", "treze",
            "quatorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove"
        ];

        $dezenas = [
            "", "", "vinte", "trinta", "quarenta", "cinquenta",
            "sessenta", "setenta", "oitenta", "noventa"
        ];

        $centenas = [
            "", "cem", "duzentos", "trezentos", "quatrocentos",
            "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"
        ];

        if ($numero == 0) {
            return "zero";
        }

        if ($numero < 20) {
            return $unidades[$numero];
        }

        if ($numero < 100) {
            $dez = intval($numero / 10);
            $uni = $numero % 10;

            if ($uni == 0) {
                return $dezenas[$dez];
            }
            return $dezenas[$dez] . " e " . $unidades[$uni];
        }

        if ($numero < 1000) {
            $cen = intval($numero / 100);
            $resto = $numero % 100;

            if ($numero == 100) {
                return "cem";
            }

            if ($resto == 0) {
                return $centenas[$cen];
            }

            return $centenas[$cen] . " e " . numero_por_extenso($resto);
        }

        // Escalas maiores
        $escala = [
            1000000000000 => "trilhão",
            1000000000    => "bilhão",
            1000000       => "milhão",
            1000          => "mil"
        ];

        foreach ($escala as $valorEscala => $nomeEscala) {

            if ($numero < $valorEscala) {
                continue;
            }

            $qtd = intval($numero / $valorEscala);
            $resto = $numero % $valorEscala;

            // pluralização
            if ($qtd > 1) {
                $nomeEscala .= "s";
            }

            if ($resto == 0) {
                return numero_por_extenso($qtd) . " " . $nomeEscala;
            }

            return numero_por_extenso($qtd) . " " . $nomeEscala . " e " . numero_por_extenso($resto);
        }

        return "";
    }
}
    

    if (!function_exists('formatarNumeroEdital')) {
    function formatarNumeroEdital(string $valor = null): ?string{
        if (empty($valor)) {
            return null;
        }

        // Mantém apenas números
        $valor = preg_replace('/\D/', '', $valor);

        // Precisa ter pelo menos 5 caracteres (1 dígito + 4 do ano)
        if (strlen($valor) < 5) {
            return $valor;
        }

        $ano = substr($valor, -4);            // últimos 4 dígitos
        $numero = substr($valor, 0, -4);      // restante

        return $numero . '/' . $ano;
    }

    if (!function_exists('limitaCaracteres')) {
        function limitaCaracteres($texto,$limite){
            
            if(strlen($texto) <= $limite){
                return $texto;
            }else{
                $textoCortado = substr($texto, 0, $limite);
                $ultimaEspaco = strrpos($textoCortado, ' ');
                if($ultimaEspaco !== false){
                    $textoCortado = substr($textoCortado, 0, $ultimaEspaco);
                }
                return $textoCortado . '...';
            }
        }
    }

    function nome_formatado(string $nome): string{
        return mb_convert_case(mb_strtolower($nome, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}