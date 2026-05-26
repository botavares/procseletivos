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
	

	
	if(!function_exists ('imprimir')){
		function imprimir($instancia, $nomeDocumento,$dados){
			require_once FCPATH . 'vendor/autoload.php';
        	$html = view('impressos/'.$nomeDocumento, $dados);
        	$instancia->loadHtml($html);
        	$instancia->render();
        	$instancia->stream($nomeDocumento.'.pdf', [ 'Attachment' => false ]);
		}
		
	}
	


}
