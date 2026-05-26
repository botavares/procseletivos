$(document).ready(function($){
	$("#box-abrangencia").hide();
	//$("#box-cursos").hide();
	$('.porta-mensagem').delay(3000).fadeOut(300);	


	/*----------------------------------------------------------------------
	MASCARAS PARA CAMPOS DE FORMULÁRIOS
	----------------------------------------------------------------------*/
	$('.telfixo').inputmask({"mask": "(99) 9999-9999","placeholder":""});
	$('.telcelular').inputmask({"mask": "(99) 9 9999-9999","placeholder":""});
	$(".telefones").inputmask({mask: ['(99) 9999-9999','(99) 9 9999-9999'], keepStatic: true,placeholder: '' });
	$('.documento').inputmask({mask: ['9999999999', '999.999.999-99', '99.999.999/9999-99'], keepStatic: true,placeholder: '' });
	$('.cep').inputmask({"mask": "99999-999","placeholder":""});
	$('.convenio').inputmask({mask: ['99/9999', '999/9999', '9999/9999'], keepStatic: true,placeholder: '' });
	$(".input-data").inputmask("99/99/9999",{ "placeholder": "" });
	$('.edital').inputmask({mask: ['9999', '9/9999', '99/9999', '999/9999'], keepStatic: true,placeholder: '' });
	$('.cnpj').inputmask({"mask": "99.999.999/9999-99","placeholder":""});
	$('.cpf').inputmask({"mask": "999.999.999-99","placeholder":""});
	$(".horas").inputmask("99:99",{ "placeholder": "" });
	$(".dinheiro").inputmask('decimal', {
		'alias': 'numeric',
		'groupSeparator': '.',
		'autoGroup': true,
		'digits': 2,
		'radixPoint': ",",
		'digitsOptional': false,
		'allowMinus': false,
		'prefix': 'R$ ',
		'placeholder': ''
	});
	/*===================================================
		TECLA ENTER ASSUME COMPORTAMENTO DE TECLA TAB
		===================================================*/
		 var caixasInput = $("input");
			function pressKey(ev){
				   if(ev.keyCode === 13){
					  var caixaAtual = caixasInput.index(this);
					  if(caixasInput[caixaAtual+1] !== null){
						  caixasInput[caixaAtual+1].focus();
					}
					ev.preventDefault();
					 return false;
				  }
				return true;
			}
			$("input").keypress(pressKey);
		
	/*===============================================================
		COLOCAR MENSAGEM NO CAMPO FILE
		===============================================================*/
		$('.fupload').html('<b>Arquivo Selecionado:</b>');
		$('#postarArquivo').change(function() {
			 $('.fupload').html('<b>Arquivo Selecionado:</b>' + $(this).val());
		});
	/*===============================================================
		MODAL QUE EXIBE MENSAGEM DE ERRO
	===============================================================*/
	function exibirModalErro(descErro, tipoMensagem,mensagem,campo){
		$("#modalErros").modal({ backdrop: 'static' });
		
		$("#nome-erro").html(descErro);
		var icone;
		if(tipoMensagem == "advert") {
			icone ="<i class='amarelo fas fa-exclamation-circle fa-5x'></i>";	
		}else{
			icone ="<i class='red fas fa-exclamation-circle fa-5x'></i>"
		}
		$("#icone").html(icone);
		$("#descricao-erro").html(mensagem);
		setTimeout(function(){
			$(campo).focus() 
		}, 150);
	}
	/*=======================================================================================
	UTILITÁRIOS - LIMPAR DADOS DE MODAL ASSIM QUE FECHÁ-LOS
	=======================================================================================*/
	$('.modal').on('hidden.bs.modal', function () {
		$(this).find("input,textarea,select").val('').end();
	   $(this).find(".labelData").html('').end();
   });

   /*===============================================================
		//LIMPAR DADOS DE MODAL ASSIM QUE FECHÁ-LOS
		===============================================================*/
		$('.modal').on('hidden.bs.modal', function () {
			$(this).find("input,textarea,select").val('').end();
			$(this).find(".labelData").html('').end();
			$(this).find(".horarios").html('').end();
			$(this).find("#modalAgendarConsulta").html('').end();
			$(this).find(".diaConsultas").html('').end();
			$(this).find(".tbody-desvincular").html('').end();
		});
		$('.modal').on('shown.bs.modal', function () {
   			 $.getJSON(path + '/seguranca/csrf', function (response) {
        		var csrfElement = $('#csrf');
        		var csrfKey = Object.keys(response).find(k => k.startsWith('csrf_'));
        		if (csrfKey) {
            		csrfElement.attr('name', csrfKey);
            		csrfElement.val(response[csrfKey]);
        		}
    		});
		});

   /*=====================================================================================
	MODAL QUE DISPARA A DELEÇÃO DE REGISTROS
	====================================================================================*/

	$("#tabela-paginada").on("click",".deleteItem",function(){
		var corte = $(this).attr('data-id').split('|');
		var chavePrimaria = corte[0];
		var nomeItem = corte[1];
		
		$.ajax({
			data:{chavePrimaria:chavePrimaria,nomeItem:nomeItem},
			success: function(data){
				$("#modalDeleteItens").modal({ backdrop: 'static' });
				$("#chavePrimaria").val(chavePrimaria);
				$("#nomeItem").html(nomeItem);
			}
		});			 
	});
	/*==============================================================================
	PREENCHE RELAÇÃO DE CURSOS DE ACORDO COM AS VAGAS E CURSOS
	===============================================================================*/
	abrangenciaOuCursos();
	$('#modo-cursos').on('change', function () {
		abrangenciaOuCursos();
	})
	$('#modo-abrangencia').on('change', function () {
		abrangenciaOuCursos();
	})

	/*===============================================================================
	ALTERNA ENTRE OS BOXES ENTRE ENCERRAMENTO OU PRORROGAÇÃO DE CONTRATO
	================================================================================*/
	prorrogarOuEncerrar();
	mudarSupervisor();
	mudarCargaHoraria();
	mudarSetor();

	$('#modo-prorrogar').on('change', function () {
		prorrogarOuEncerrar();
	})
	$('#modo-encerrar').on('change', function () {
		prorrogarOuEncerrar();
	})
	/*==============================================================================
	MARCAR/DESMARCAR SE O ADITIVO DO ESTAGIÁRIO TERÁ SUPERVISOR
	===============================================================================*/
	function mudarSupervisor(){
		if($("#chk-supervisor").is(":checked")){
			$(".box-supervisor").show();
			$("#input-supervisor").prop("required", true);
		}else{
			$(".box-supervisor").hide();
			$("#input-supervisor").prop("required", false);
			$("#input-supervisor").val('');
		}
	}
	function mudarCargaHoraria(){
		if($("#chk-carga-horaria").is(":checked")){
			$(".box-carga-horaria").show();
			$("#input-carga-horaria").prop("required", true);
		}else{
			$(".box-carga-horaria").hide();
			$("#input-carga-horaria").prop("required", false);
			$("#input-carga-horaria").val('');
		}
	}
	function mudarSetor(){
		if($("#chk-setor").is(":checked")){
			$(".box-setor").show();
			$("#input-setor").prop("required", true);
		}else{
			$(".box-setor").hide();
			$("#input-setor").prop("required", false);
			$("#input-setor").val('');
		}
	}


	$("#chk-setor").on("change", function(){
		mudarSetor();
	});

	$("#chk-carga-horaria").on("change", function(){
		mudarCargaHoraria();
	});
	$("#chk-supervisor").on("change", function(){
		mudarSupervisor();
	});
			
	
	/*==============================================================================
	PREENCHE RELAÇÃO DE SETORES DE ACORDO COM AS VAGAS E CURSOS
	===============================================================================*/
	$('#select-cargo').on('change', function () {
		var idCargo = $(this).val();
		var idEdital = $('#input-edital').val();
		var csrfElement = $('#csrf');
		var csrfName = csrfElement.attr('name');
		var csrfHash = csrfElement.val();

		if (idCargo === '') {
			$('#input-setor').html('<option value="">Selecione um setor</option>');
			return;
		}

		$.ajax({
			url: path + '/Candidatos/getCandidatosByCargoAndEdital',
			dataType: 'json',
			type: 'POST',
			data: {
				[csrfName]: csrfHash,
				idCargo: idCargo,
				idEdital: idEdital
			},
			success: function (response) {
				//console.log("Resposta recebida:", response); // DEBUG
				updateCSRF(response);

				var options = '';
				for (var i = 0; i < response.setores.length; i++) {
					options += '<option value="' + response.setores[i].pk_id_setor + '">' + response.setores[i].ds_nome_setor + '</option>';
				
				}
				
				$('#input-setor').html(options); // atualiza o select correto
			},
			error: function (xhr, status, error) {
				console.error("Erro AJAX:", status, error);
			}
		});
	});


	$('.input-data').on('blur', function() {
		var isData = validarData($(this).val());
		if(isData == false){
			exibirModalErro("Data errada","advert","Data inválida.",this);
		}

		
	})
	/*==============================================================================
	MARCAR QUE O CANDIDATO COMPARECEU
	===============================================================================*/
	
	$("#tabela-paginada").on("change",".comparecimento",function(){
		var idCandidato = $(this).data('id');
		var comparecimento = $(this).is(":checked") ? 1 : 0;
		
		var csrfName = $('#csrf').attr('name');
		var csrfHash = $('#csrf').val();
		$.ajax({
			url: path + '/Convocados/atualizarComparecimento',
			type: 'POST',
			dataType: 'json',
			data:{idCandidato:idCandidato,comparecimento:comparecimento,[csrfName]:csrfHash},
			success: function(data){
	
				updateCSRF(data);
			},
			error: function () {
				alert('Erro ao atualizar comparecimento. Verifique sua sessão ou token CSRF.');
			}
		});			 
	});

	

	/*==============================================================================
	PREENCHE RELAÇÃO DE CURSOS DE ACORDO COM EDITAL SELECIONADO
	===============================================================================*/
	$('.select-edital').on('change', function () {
		var numeroEdital = $(this).val();
		var targetSelect = $(this).data('target');
		var csrfElement = $('#csrf');
		var csrfName = csrfElement.attr('name');
		var csrfHash = csrfElement.val(); // use .val()
		
		if (numeroEdital === '') {
			$(targetSelect).html('<option value="">Selecione um cargo</option>');
			return;
		}

		$.ajax({
			url: path + '/Editais/getCargosByEdital',
			dataType: 'json',
			type: 'POST',
			data: {
				[csrfName]: csrfHash,
				idEdital: numeroEdital
			},
		success: function (response) {
			updateCSRF(response);

		// Monta os options
		var options = '<option value="">Selecione um cargo</option>';
		for (var i = 0; i < response.cargos.length; i++) {
			options += '<option value="' + response.cargos[i].pk_id_cargo + '">' +
						response.cargos[i].ds_nome_cargo + '</option>';
		}
		$(targetSelect).html(options);
	},

				error: function () {
					alert('Erro ao buscar cursos. Verifique sua sessão ou token CSRF.');
				}
			});
	});

	
	function abrangenciaOuCursos(){
        if($('#modo-abrangencia').is(':checked')){
           // $('#box-cursos').hide();
			$('#box-abrangencia').show();
            
			/*$('#input-nome').val('');
            $('#input-email').val('');
            $('#input-nome').prop('required', false);
            $('#input-email').prop('required', false);
           */
        }
        if($('#modo-cursos').is(':checked')){
            $('#box-cursos').show();
			$('#box-abrangencia').hide();
            /*
			$('#input-senha').val('');
            $('#input-confirmar-senha').val('');
            
            $('#input-nome').prop('required', true);
            $('#input-email').prop('required', true);
            */
        }
    }

	/*==============================================================================================
	FUNÇÃO PARA TROCAR BOXES ENTRE ENCERRAR OU PRORROGAR CONTRATOS
	==============================================================================================*/
	function prorrogarOuEncerrar(){
		if($('#modo-prorrogar').is(':checked')){
            $('#box-encerrar').hide();
			$('#box-prorrogar').show();

			$("#input-data-aditivo").prop("disabled", false).show();
			$("#input-data-aditivo").removeAttr("required").show();


			 $('#box-encerrar').find('input:text, input:password, textarea').val('');
			// Limpa campos de seleção (select)
			 $('#box-encerrar').find('select').val('');
			// Desmarca botões de rádio e checkboxes
			 $('#box-encerrar').find('input:radio, input:checkbox').prop('checked', false);
			 //colocar no campo de 'nova data de encerramento' a data atual mais 365 dias
			 // Seleciona o campo dentro da div box-prorrogacao
			var campoProrroga = $('#box-prorrogar').find('input[name="ds_data_prorrogacao"]');
			var campoDataAtual = $('#box-prorrogar').find('input[name="ds_data_aditivo"]');

			// Cria uma nova data (hoje)
			var dataAtual = new Date();
			var dataAcrescentada = new Date();
			// Adiciona 365 dias
			dataAcrescentada.setDate(dataAtual.getDate() + 365);

			// Formata a data no padrão d/m/Y para a prorrogacao
			var anoProrrogado = dataAcrescentada.getFullYear();
			var mesProrrogado = String(dataAcrescentada.getMonth() + 1).padStart(2, '0');
			var diaProrrogado = String(dataAcrescentada.getDate()).padStart(2, '0');


			var ano = dataAtual.getFullYear();
			var mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
			var dia = String(dataAtual.getDate()).padStart(2, '0');

			var dataProrrogada = diaProrrogado + '/' + mesProrrogado + '/' + anoProrrogado;
			var dataAtual = dia + '/' + mes + '/' + ano;


			// Define o valor no campo
			campoProrroga.val(dataProrrogada);
			campoDataAtual.val(dataAtual);
			


        }
        if($('#modo-encerrar').is(':checked')){
			$("#input-data-aditivo").prop("disabled", true).hide();
			$("#input-data-aditivo").removeAttr("required").hide();
            $('#box-encerrar').show();
			$('#box-prorrogar').hide();
			
           	$('#box-prorrogar').find('input:text, input:password, textarea').val('');
			// Limpa campos de seleção (select)
			$('#box-prorrogar').find('select').val('');
			// Desmarca botões de rádio e checkboxes
			$('#box-prorrogar').find('input:radio, input:checkbox').prop('checked', false);
        }
	}

	function updateCSRF(response) {
		var csrfElement = $('#csrf');
		var csrfKey = Object.keys(response).find(k => k.startsWith('csrf_'));
		if (csrfKey) {
			//console.log("Atualizando CSRF:", csrfKey, response[csrfKey]); // DEBUG
			csrfElement.attr('name', csrfKey);
			csrfElement.val(response[csrfKey]);
		}
	}

	/*===============================================================
		VALIDADOR DE CPF
	===============================================================*/
	function validarCPF(valorCpf){
		
		var exp = /\.|\-/g;
		var cpf = valorCpf.replace(exp,'').toString();
			
		if(cpf.length == 11 ){	
			var v = [];
			//Calcula o primeiro dígito de verificação.
			v[0] = 1 * cpf[0] + 2 * cpf[1] + 3 * cpf[2];
			v[0] += 4 * cpf[3] + 5 * cpf[4] + 6 * cpf[5];
			v[0] += 7 * cpf[6] + 8 * cpf[7] + 9 * cpf[8];
			v[0] = v[0] % 11;
			v[0] = v[0] % 10;

			//Calcula o segundo dígito de verificação.
			v[1] = 1 * cpf[1] + 2 * cpf[2] + 3 * cpf[3];
			v[1] += 4 * cpf[4] + 5 * cpf[5] + 6 * cpf[6];
			v[1] += 7 * cpf[7] + 8 * cpf[8] + 9 * v[0];
			v[1] = v[1] % 11;
			v[1] = v[1] % 10;

			//Retorna Verdadeiro se os dígitos de verificação são os esperados.

			  if ((v[0] != cpf[9]) || (v[1] != cpf[10])) {
				  return false;
				  /*exibirModalErro("CPF inválido","advert","Verifique os números do CPF.");
				  setTimeout(function() { $(".validarCPF").focus() }, 150);*/
			  }else if (cpf[0] == cpf[1] && cpf[1] == cpf[2] && cpf[2] == cpf[3] && cpf[3] == cpf[4] && cpf[4] == cpf[5] && cpf[5] == cpf[6] && cpf[6] == cpf[7] && cpf[7] == cpf[8] && cpf[8] == cpf[9] && cpf[9] == cpf[10]){
				   return false;
				  /*exibirModalErro("CPF inválido","advert","Verifique os números do CPF.");
				  setTimeout(function() { $(".validarCPF").focus() }, 150);*/
			  }else{
				  return true
			  }       
		}else {
			 return false;
			/*exibirModalErro("CPF inválido","advert","Verifique os números do CPF.");
			setTimeout(function() { $(this).focus() }, 150);*/
		}
	};
	function validarData(valorData) {
		// Verifica se o valor foi informado e está no formato básico esperado
		//if (!valorData || typeof valorData !== 'string') return false;

		const partes = valorData.split('/');
		if (partes.length !== 3) return false;

		const [diaStr, mesStr, anoStr] = partes;
		const dia = parseInt(diaStr, 10);
		const mes = parseInt(mesStr, 10);
		const ano = parseInt(anoStr, 10);

		// Valida números básicos
		if (isNaN(dia) || isNaN(mes) || isNaN(ano)) return false;
		if (ano < 1000 || mes < 1 || mes > 12 || dia < 1) return false;

		// Define a quantidade de dias em cada mês
		const diasNoMes = [31, (ehAnoBissexto(ano) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

		// Verifica se o dia é válido para o mês
		if (dia > diasNoMes[mes - 1]) return false;

		// Cria o objeto Date para comparar com a data atual
		const dataInserida = new Date(ano, mes - 1, dia);
		const hoje = new Date();

		// Impede datas futuras
		//if (dataInserida > hoje) return false;

		// Caso tudo esteja certo
		return true;
	}



	// Função auxiliar para verificar se o ano é bissexto
	function ehAnoBissexto(ano) {
		return (ano % 4 === 0 && (ano % 100 !== 0 || ano % 400 === 0));
	}




	$('.data-final').on('blur', function(){
			var dataInicial = $(".data-inicio").val();
			var idInicio = $(".data-inicio").attr('id');
			var splitInicial = dataInicial.split('/');
			var novaDataInicial = new Date (splitInicial[2]+'-'+splitInicial[1]+'-'+splitInicial[0]);
			var dataFinal = $(".data-final").val();
			var splitFinal = dataFinal.split('/');
			var novaDataFinal = new Date (splitFinal[2]+'-'+splitFinal[1]+'-'+splitFinal[0]);
			
			if(novaDataInicial >= novaDataFinal){
				/*$(".btnRelatorio").prop('disabled',true)*/
				exibirModalErro("Data errada","advert","Data final é mais antiga que data inicial.",$("#" + idInicio));
				$('#modalErro').one('hidden.bs.modal', function() {
        			$("#" + idInicio).focus();
    			});

			}else{
				$(".btnRelatorio").prop('disabled',false)
			}
		});


});