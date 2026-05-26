function verCpf(campo) {
	var cpf = campo.value; // Recebe o valor digitado no campo
	var globalvar = campo;
	cpf = cpf.replace(/[.-]/gi, "");
	// Aqui começa a checagem do cpf
	var posicao, i, soma, dv, dv_informado;
	var digito = new Array(10);
	dv_informado = cpf.substr(9, 2); // Retira os dois últimos dígitos do número informado
	// Desemembra o número do cpf na array digito
	for (i=0; i<=8; i++) {
		digito[i] = cpf.substr( i, 1);
	}
	// Calcula o valor do 10º dígito da verificação
	posicao = 10;
	soma = 0;
	for (i=0; i<=8; i++) {
		soma = soma + digito[i] * posicao;
		posicao = posicao - 1;
	}
	digito[9] = soma % 11;
	if (digito[9] < 2) {
		digito[9] = 0;
	}else{
       digito[9] = 11 - digito[9];
	}
	// Calcula o valor do 11º dígito da verificação
	posicao = 11;
	soma = 0;
	for (i=0; i<=9; i++) {
		soma = soma + digito[i] * posicao;
		posicao = posicao - 1;
	}
	digito[10] = soma % 11;
	if (digito[10] < 2) {
		digito[10] = 0;
	}else {
		digito[10] = 11 - digito[10];
	}
	// Verifica se os valores dos dígitos verificadores conferem
	dv = digito[9] * 10 + digito[10];
	if (dv != dv_informado) {
    	alert('cpf inválido');
		campo.value = '';
		
		setTimeout('globalvar.focus()',250);
		return false;
	} 
}


function verCNPJ(campo){

	$("#input-cnpj").blur(function(){
		var cnpj = $('#input-cnpj').val()
		cnpj = cnpj.replace(/[^\d]+/g,'');
   		if(cnpj == ''){
			return false;
		}
 	   if (cnpj.length != 14){
    	    return false;
	   }
 	    // Elimina CNPJs invalidos conhecidos
		if (cnpj == "00000000000000" || 
			cnpj == "11111111111111" || 
			cnpj == "22222222222222" || 
			cnpj == "33333333333333" || 
			cnpj == "44444444444444" || 
			cnpj == "55555555555555" || 
			cnpj == "66666666666666" || 
			cnpj == "77777777777777" || 
			cnpj == "88888888888888" || 
			cnpj == "99999999999999"){
				return false;
		}
		// Valida DVs
		tamanho = cnpj.length - 2
		numeros = cnpj.substring(0,tamanho);
		digitos = cnpj.substring(tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
		  soma += numeros.charAt(tamanho - i) * pos--;
		  if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(0))
			return false;
			 
		tamanho = tamanho + 1;
		numeros = cnpj.substring(0,tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
		  soma += numeros.charAt(tamanho - i) * pos--;
		  if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(1)){
			alert('cnpj inválido');
			$("#input-cnpj").val("");
			var globalvar = campo;
			setTimeout("globalvar.focus()",250);
			return false;
		}
		return true;
		
		});
		
}
 