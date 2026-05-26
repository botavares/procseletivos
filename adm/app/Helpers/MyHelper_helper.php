<?php

if(! function_exists('sendMeAuth')){
	/*=================================================
	Nome:sendMeAuth
	Função: Retona uma sequencia de caracteres para codigo de autenticacao
	Modificação: 23/08/2018
	===================================================*/
	function sendMeAuth(){
		require_once FCPATH . 'vendor/autoload.php';
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
	
    if(! function_exists('sendMeProtocol')){
        function sendMeProtocol($model,$campo){
            $protocolo = mt_rand(1000000000,9999999999);
			$modelProtocol = $model;
			$teste = $modelProtocol->where($campo,$protocolo)->find();
            if(!$teste){
                return $protocolo;
            }else{
                sendMeProtocol($model,$campo);
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
        	$instancia->render();
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

	if(!function_exists('gerarCSRF')){
		function gerarCSRF(){
			$security = Services::security();
			return $security->getCSRFHash();
		}
	}
		

}
