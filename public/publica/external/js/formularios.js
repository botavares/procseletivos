
$(document).ready(function($){
    $('.resultadoBusca').hide();
	/*----------------------------------------------------------------------
	MASCARAS PARA CAMPOS DE FORMULÁRIOS
	----------------------------------------------------------------------*/
		$('.telfixo').inputmask({"mask": "(99) 9999-9999","placeholder":""});
		$('.telcelular').inputmask({"mask": "(99) 9 9999-9999","placeholder":""});
		$(".telefones").inputmask({mask: ['(99) 9999-9999','(99) 9 9999-9999'], keepStatic: true,placeholder: '' });
		$('.cep').inputmask({"mask": "99999-999","placeholder":""});
		$(".input-data").inputmask("99/99/9999",{ "placeholder": "" });
		$(".documento").inputmask({mask: ['999.999.999-99', '99.999.999/9999-99'], keepStatic: true,placeholder: '' });
		$('.cpf').inputmask({"mask": "999.999.999-99","placeholder":""});
		$('.crm').inputmask({"mask": "999999999","placeholder":""});
		$(".horas").inputmask("99:99",{ "placeholder": "" });
		$(".pressao").inputmask("99/99",{ "placeholder": "" });
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


        $('.outradeficiencia').prop('hidden', true);

        $(".select-deficiencia").on('change', function() {
            var idDeficiencia = $(this).val();
            
            if (idDeficiencia == 8) {
                $('.outradeficiencia').prop('hidden', false);
                setTimeout(function(){
                    $("#input-outradeficiencia").focus() 
                }, 150);
            }else{
                $("#input-outradeficiencia").val('');
                $('.outradeficiencia').prop('hidden', true);
            }
         
        });

        $('.area-alertas').delay(4000).fadeOut(300);	
        
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
    


	$("#input-ceps").on("blur", function () {

    let cep = $(this).val()//.replace(/\D/g, "");
		alert(cep);
    // CEP deve ter 8 dígitos
    if (cep.length !== 9) {
        return;
    }

    $.ajax({
        url: BASE_URL + `/api/cep?cep=${cep}`,
        type: "GET",
        dataType: "json",

        success: function (data) {

            if (!data.success) {
                alert(data.message);
                return;
            }

            $("#input-rua").val(data.data.logradouro);
            $("#input-bairro").val(data.data.bairro);
            $("#input-cidade").val(data.data.cidade);
            $("#select-estado").val(data.data.uf).trigger("change");
        },

        error: function () {
            alert("Erro ao buscar o CEP");
        }
    });

});

    /*=======================================================================================
        Ao clicar no card, redireciona para o link
    ========================================================================================*/
	$('.br-card').on('click', function() {
        var link = $(this).find('.card-content a').attr('href');
        if (link) {
            window.location.href = link;
        }
    });
 /*=======================================================================================
    PERGUNTAS E RESPOSTAS - Exibe a linha de tabela com as respostas ao clicar no botão
    ========================================================================================*/
    $('#tabela-perguntas tbody').on('click', 'tr.toggle-detalhes', function () {
        var tr = $(this).closest('tr');
        var table = $('#tabela-perguntas').DataTable();
        var row = table.row( tr );
        var id = $(this).attr('data-id');
            if(row.child.isShown()){
                // Linha de detalhes já está visível, então fecha-as linhas de detalhes
                row.child.hide();
                
                $('button#'+id).html('<i class="fas fa-chevron-down font-blue"></i>'); 
            } else {
                // Linha de detalhes ainda não está visível, então expandir
                row.child.show();
                $('button#'+id).html('<i class="fas fa-chevron-up  font-blue"></i>');

                
                
                $.ajax({
                    url: path+'/PerguntasFrequentes/buscarRespostas',
                    method: 'GET',
                    data: {q: id},                  // Enviar o ID como parâmetro
                    success: function(resultado) {
                        row.child('<td class="paragrafo" style="font-size:18px; color:blue; border:0;">' + resultado.ds_resposta + '</td>').show();
                    },

                    error: function() {
                        alert('Erro ao carregar as respostas.');
                    }
                });

            }
    });
    
    /*===========================================================================
        EVITANDO QUE AO CLICAR NO ICONE '+' OU '-', A PÁGINA ROLE PARA O RODAPÉ instrucao_view.php
    ===========================================================================*/
    $('.fal').parent().click(function(e) {
        e.preventDefault(); 
    })

	
    /*==================================================================================
       LIMPANDO A CAIXA DE BUSCA CASO PERCA O FOCO
    ===================================================================================*/
    $('.boxBusca').on('blur', function() {
        limparBusca();
    });
    
    function limparBusca(){
        $('#busca-servicos-input').val('');
		$('#busca-telefones-input').val('');
        $('#resultados').empty();
        $('.resultadoBusca').hide();
    }

	$("#input-cpf").on("blur", function () {
    let input = $(this);
    let fullcpf = input.val().trim();
    let pegacpf = fullcpf.replace(/\.|-/g, "");

    // Remove erro anterior
    input.removeClass("is-invalid");
    input.next(".erro-cpf").remove();

    if (pegacpf === "") return;

    let retornoCPF = validarCPF(fullcpf);

    if (retornoCPF === false) {
        input.addClass("is-invalid");

        $("<small class='erro-cpf text-danger'>CPF inválido.</small>")
            .insertAfter(input);

        // Retorna foco ao campo
        setTimeout(() => {
            input.focus();
            input.select();
        }, 50);
    }
});



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
function isLeapYear(year) {
        return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
    }

    function isValidDate(dateStr) {
        // Formato esperado: dd/mm/aaaa
        let regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        let match = dateStr.match(regex);

        if (!match) return false;

        let day = parseInt(match[1], 10);
        let month = parseInt(match[2], 10);
        let year = parseInt(match[3], 10);

        if (year < 1000 || year > 9999) return false;
        if (month < 1 || month > 12) return false;

        let daysInMonth = [
            31,
            isLeapYear(year) ? 29 : 28,
            31,
            30,
            31,
            30,
            31,
            31,
            30,
            31,
            30,
            31
        ];

        return day >= 1 && day <= daysInMonth[month - 1];
    }

    $(".input-data").on("blur", function () {
        let input = $(this);
        let value = input.val().trim();

        // Remove mensagem anterior
        input.removeClass("is-invalid");
        input.next(".erro-data").remove();

        if (value === "") return;

        if (!isValidDate(value)) {
            input.addClass("is-invalid");

            $("<small class='erro-data text-danger'>Data inválida. Use o formato dd/mm/aaaa.</small>")
                .insertAfter(input);
			// Retorna o foco ao campo após um pequeno delay
            setTimeout(() => {
                input.focus();
                input.select();
            }, 50);
        }
    });




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

    

