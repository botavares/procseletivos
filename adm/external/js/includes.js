/*$(document).ready(function($){*/
$(window).on('load',(function(){
	
	
	
	
	
	// Animate loader off screen
	$(".se-pre-con").fadeOut("slow");


$("#boxanoencaminhado").hide();
	$('.modal').on('hidden.bs.modal', function () {
	$(this).find("input,textarea,select").val('').end();
	$(this).find(".labelData").html('').end();
	$(this).find(".horarios").html('').end();
	$(this).find("#modalAgendarConsulta").html('').end();
	$(this).find(".diaConsultas").html('').end();
	$(this).find(".tbody-desvincular").html('').end();
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












}));