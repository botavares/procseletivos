<script src="<?php echo base_url("external/js/ckeditor/ckeditor.js")?>"></script>
<div class="content-wrapper">
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
							    <h1><?php echo $titulo?></h1>
								<?php if(session()->has('error')):?>
									<div class="porta-mensagem-fixa alert alert-danger col-md-12">
										<ul>
											<?php foreach(session()->getFlashdata('error') as $valueError): ?>
												<li class="text text-white text-center font22"><?php echo $valueError ?></li>
											<?php endforeach ?>
										</ul>
									</div>
								<?php endif ?>
                            </h3>
							
						</div>
						<a href="<?php echo base_url("Candidatos/$edital".'/'."$curso")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formCursos" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Candidatos/convocarCandidato"?>">

								<input type="hidden" name="action" value="create">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <input type="hidden" name="fk_id_candidato" value="<?= esc(set_value('fk_id_candidato', $dados->pk_id_candidato)) ?>">
                                <input type="hidden" name="fk_id_edital" value="<?= esc(set_value('fk_id_edital', $edital ?? '')) ?>">
                                <input type="hidden" name="fk_id_curso"  value="<?= esc(set_value('fk_id_curso', $curso ?? '')) ?>">
                                <input type="hidden" name="ds_email"  value="<?= esc(set_value('fk_id_curso', $dados->ds_email ?? '')) ?>">
                                

                                
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-3">Nome do Candidato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_candidato'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-candidato" 
                                            name="ds_nome_candidato" 
                                            value="<?php echo set_value('ds_nome_candidato',$dados->ds_nome);?>" 
                                            readonly
                                        >
										<?php if(isset($error['ds_nome_candidato'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_candidato'] ?? ''?></span>
										<?php endif ?>
                                       
                                        
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-3">Curso</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_curso'] ?? ''?></span>
                                         <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-curso" 
                                            name="curso" 
                                            value="<?php echo set_value('ds_nome_curso',$cursoNome);?>" 
                                            readonly
                                        >
										<?php if(isset($error['ds_nome_curso'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_curso'] ?? ''?></span>
										<?php endif ?>
                                    </div>
                                </div>

                                <div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-3">Período</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_curso'] ?? ''?></span>
                                         <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-periodo" 
                                            name="periodo" 
                                            value="<?php echo set_value('ds_periodo',$periodo);?>" 
                                            readonly
                                        >
										<?php if(isset($error['ds_nome_curso'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_curso'] ?? ''?></span>
										<?php endif ?>
                                    </div>
                                </div>

								<div class="form-group row m0">
							<label  style="font-size:16px;" for="ds_definicao" class="col-form-label col-sm-2">Texto:</label>
							<div class="col-sm-12">
                           		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_definicao'] ?? ''?></span> 
                                   	<div id="saida">

										
										<textarea name="ds_mensagem" id="ds_mensagem">Olá, <?=$dados->ds_nome?><br>
                                           Você foi selecionado(a) para prosseguir no processo de estágio da Prefeitura.<br>
                                           Solicitamos que procure o setor de Recursos Humanos ou entre em contato pelo telefone (37) 3229-8155 (whatsapp) no prazo de 48 horas a partir do envio desta mensagem 
                                           para confirmar seu interesse na vaga.<br>
                                           O não comparecimento ou manifestação de interesse dentro desse prazo poderá resultar na perda da oportunidade.<br>
                                            <br><br>
                                            Atenciosamente,<br>
                                            Setor de Recursos Humanos<br>
                                            Prefeitura Municipal de Divinópolis<br>
                                        </textarea>
									
										<script>
											CKEDITOR.replace( 'ds_mensagem' );
										</script>
									</div>
									<?php if(isset($error['ds_mensagem'])):?>
									<span class="text text-danger"><?php echo $error['ds_mensagem'] ?? ''?></span>
									<?php endif ?>
							</div>
						</div>
                        <div class="card-footer">
							<div class="form-group centralizado row">
								<div class="col-md-12 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
									<button type="submit" class="col-md-12 btn btn-success">Enviar</button>
								</div>
								
							</div>
						</div>
                                

                                
								
								
								

								
								
								
							</form>
						</div>
    				</div>
				</div>
			</div>	
		</div>	
	</section>
</div>

<div class="modal fade" id="modalErros" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				
				<h5 id="nome-erro" class="modal-title font25 red bold ta-center"> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
        		</button>
        		
        		
      		</div>
      		<div class="modal-body">
        		<p id="descricao-erro" class="font20 "></p>
		  		
			</div>
			<div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal"><strong>Retornar</strong></button>
		
			</div>	
    	</div>
  	</div>
</div>
<script>
	$(document).ready(function(){
		$('#input-termino-contrato').on('blur', function(){
			alert("entrou")
			var dataInicial = $("#input-inicio-contrato").val();
			var splitInicial = dataInicial.split('/');
			var novaDataInicial = new Date (splitInicial[2]+'-'+splitInicial[1]+'-'+splitInicial[0]);
			
			var dataFinal = $("#input-termino-contrato").val();
			var splitFinal = dataFinal.split('/');
			var novaDataFinal = new Date (splitFinal[2]+'-'+splitFinal[1]+'-'+splitFinal[0]);
			
			if(novaDataInicial >= novaDataFinal){
				alert("Data final é mais antiga que data inicial.");
				$(".btnRelatorio").prop('disabled',true)
				exibirModalErro("Data errada","advert","Data final é mais antiga que data inicial.");
			}else{
				$(".btnRelatorio").prop('disabled',false)
			}
		});
	})	
</script>
