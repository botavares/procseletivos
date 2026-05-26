<script src="<?php echo base_url("external/js/ckeditor/ckeditor.js")?>"></script>
	<section class="content-wrapper">
		<div class="container-fluid p0">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">
					    <h1><?php echo $titulo?></h1>
						<?php if (session()->has('errors')): ?>
    						<div class="alert alert-danger text-center" role="alert">
								<ul class="list-unstyled mb-0">
									<?php foreach (session('errors') as $error): ?>
										<li><?= esc($error) ?></li>
									<?php endforeach; ?>
								</ul>
    						</div>
						<?php endif; ?>
						<?php if (session()->has('mensagemError')): ?>
    						<div class="alert alert-danger text-center" role="alert">
									<?= esc(session('mensagemError')) ?>
    						</div>
						<?php endif; ?>
                    </h3>
				</div>
				<div class="card-body">
					<form id="formRespostas" class="formEditor form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Manifestacoes/registrarResposta"?>">
						<input type="hidden" name="action" value="update">
						<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						
						<div  class="form-group row m0">
							<label style="font-size:16px;" for="input-protocolo" class="col-form-label col-md-2 col-sm-12 mb0">Protocolo</label>
							<div class="col-md-10 col-sm-12">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo $dados->ds_protocolo ?></p>
								<input type="hidden" class="form-control" id="input-protocolo" name="ds_protocolo" value="<?php echo $dados->ds_protocolo;?>">
								<input type="hidden" class="form-control" id="input-idmanifesto" name="fk_id_manifesto" value="<?php echo $dados->pk_id_manifesto;?>">
								<input type="hidden" class="form-control" id="input-idresposta" name="pk_id_resposta" value="<?php echo $verificarRespondido->pk_id_resposta;?>">
							</div>
						</div>
						<div  class="form-group row m0">
							<label style="font-size:16px;" for="input-tipo_manifesto" class="col-form-label col-md-2 col-sm-12 mb0">Tipo de Manifesto</label>
							<div class="col-md-10 col-sm-12">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo $dados->ds_manifestacao ?></p>
							</div>
						</div>
						<?php
						if($dados->ds_anonimo==1){?>
							
								<div class="form-group row m0">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Manifestante</label>
									<div class="col-md-10 col-sm-12">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_nome?></p>
									</div>
								</div>
								<div class="form-group row m0">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Email</label>
									<div class="col-md-10 col-sm-12">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_email?></p>
									</div>
								</div>
							
						<?php } ?>
						<div class="form-group row m0">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Data do Manifesto</label>
							<div class="col-md-10 col-sm-12">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo date("d/m/Y", strtotime($dados->ds_datacadastro));?></p>
							</div>
						</div>
						<div class="form-group row  m0">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Setor</label>
							<div class="col-md-10 col-sm-12 chanfrado">
								<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_nome_setor;?></p>
							</div>
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-sm-2">Assunto</label>
							<div class="col-md-10 col-sm-12 chanfrado">
								<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_assunto;?></p>
							</div>
						</div>
						<div class="form-group row m0">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Relato do Manifestante</label>
							<div style="height:120px;"class="col-md-10 col-sm-12 chanfrado bg-cordepapel scroll-y ">
								<p class="" style="font-size:18px; text-align:justify;text-ident:2em;"><?php echo $dados->ds_texto_manifestacao;?></p>
							</div>
						</div>
						<div class="form-group row m0">
							<label  style="font-size:16px;" for="ds_definicao" class="col-form-label col-sm-2">Responder:</label>
							<div class="col-sm-12">
                           		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_definicao'] ?? ''?></span> 
                                   	<div id="saida">
										<textarea name="ds_texto_resposta" id="ds_texto_resposta"><?php echo $verificarRespondido->ds_texto_resposta?></textarea>
										<script>
											CKEDITOR.replace( 'ds_texto_resposta' );
										</script>
									</div>
									<?php if(isset($error['ds_texto_resposta'])):?>
									<span class="text text-danger"><?php echo $error['ds_texto_resposta'] ?? ''?></span>
									<?php endif ?>
							</div>
						</div>
								
						<div class="card-footer">
									<div class="form-group centralizado row">
										<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
											<button type="submit" class="col-md-8 btn btn-success">Responder</button>
										</div>
										<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
											<a href="<?php echo base_url("Manifestacoes/gerenciarManifestos")?>" type="button" class="col-md-8 btn btn-warning" data-dismiss="modal">Voltar</a>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
    			</div>
	</section>
