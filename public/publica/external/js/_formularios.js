
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
	
		
	
    
     // Quando o usuário digita no campo de busca
     $('#busca-servicos-input').on('keyup', function() {
        if($(this).val().length >= 3) {
            $('.resultadoBusca').show();
        // Obtém o valor do campo de busca
             var query = $(this).val();

        
            // Envia a requisição AJAX para o servidor PHP
            $.ajax({
                url:  path+'/Home/buscarServicos',
                type: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function(resultados) {
                    // Limpa a lista de resultados
                    $('#resultados').empty();
                    // Adiciona os novos resultados à lista
                    resultados.forEach(function(resultado) {
                        $('#resultados').append('<li>' +'<a style="color:#000;font-weight:300;font-size:18px;" href="'+resultado.ds_link_servico+'" target="_blank'+ resultado.id_servico +'">'+ resultado.ds_nome_servico + '</a></li>');
                    });
                }
            });
        }

        if($(this).val().length <= 0) {
            
            limparBusca();
            
        }
    });

	$('#busca-telefones-input').on('keyup', function() {
        if($(this).val().length >= 3) {
            $('.resultadoBusca').show();
        // Obtém o valor do campo de busca
             var query = $(this).val();

        
            // Envia a requisição AJAX para o servidor PHP
            $.ajax({
                url:  path+'/Telefones/buscarTelefones',
                type: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function(resultados) {
                    // Limpa a lista de resultados
                    $('#resultados').empty();
                    // Adiciona os novos resultados à lista
                    resultados.forEach(function(resultado) {
						var formato = new StringMask("(99) 9999-9999");
						var telefone = formato.apply(resultado.ds_telefone);
                        $('#resultados').append(
							
							'<div class="col-lg-3 col-md-6">'+
								'<a href="#">'+
									'<div class="feature-box box-style">'+
									'<h3 style="" class="text-center font-18 bold mrg-top-0">'+
										resultado.ds_nome_setor+
										
									'</h3>'+
										'<div class="box-content-style feature-content">'+
											'<h4 style="font-size: 24px; color:red;" class="text-center">'+
											telefone+
											'</h4>'+
											'<p class="text-center">'+
											resultado.ds_secretaria+
											'</p>'+
										'</div>'+
									'</div>'+
								'</a>'+
							'</div> '
                        
						)
							
                    });
                }
            });
        }
        if($(this).val().length <= 0) {
            
            limparBusca();
            
        }
    });
	

	
	$('#click').on('click', (function(e){
		e.preventDefault();
		var value = $(this).attr('data-value');
		
		$.ajax({
			url:  path+'/Microsservicos/contarServicos',
			type: 'POST',
			data: { q: value },
			dataType: 'json',
			success: function(resultados) {
				$('#counter').text(response.count);
			}
		});
	}));
	

	
    function limparBusca(){
        $('#busca-servicos-input').val('');
		$('#busca-telefones-input').val('');
        $('#resultados').empty();
        $('.resultadoBusca').hide();
    }
	});