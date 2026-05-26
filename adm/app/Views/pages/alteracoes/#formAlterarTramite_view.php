<script src="<?php echo base_url("external/js/ckeditor/ckeditor.js")?>"></script>
	<section class="content-wrapper">
		<div class="container-fluid p0">
			<div class="card">
				<div class="card-header">
				<h1 class="text-center"><?php echo $titulo?></h1>
						<?php if (session()->has('errors')): ?>
    						<div class="alert alert-danger text-center" role="alert">
								<ul class="list-unstyled mb-0">
									<?php foreach (session('errors') as $error): ?>
										<li><?= esc($error) ?></li>
									<?php endforeach; ?>
								</ul>
    						</div>
						<?php endif; ?>
						<?php if (session()->has('mensagemSuccess')): ?>
    						<div class="alert alert-success text-center" role="alert">
									<?= esc(session('mensagemSuccess')) ?>
    						</div>
						<?php endif; ?>
						<?php if (session()->has('mensagemError')): ?>
    						<div class="alert alert-danger text-center" role="alert">
									<?= esc(session('mensagemError')) ?>
    						</div>
						<?php endif; ?>
				</div>
				<div class="card-body">
					<form id="formRespostas" class="formEditor form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Tramites/registrarTramite"?>">
						<input type="hidden" name="action" value="update">
						<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						<div  class="form-group row m0 mbot5">
							<label style="font-size:16px;" for="input-protocolo" class="col-form-label col-md-2 col-sm-12 mb0">Protocolo</label>
							<div class="col-md-10 col-sm-12">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo $dados->ds_protocolo ?></p>
								<input type="hidden" class="form-control" id="input-protocolo" name="ds_protocolo" value="<?php echo $dados->ds_protocolo;?>">
								<input type="hidden" class="form-control" id="input-idmanifesto" name="fk_id_manifesto" value="<?php echo $dados->pk_id_manifesto;?>">
								<input type="hidden" class="form-control" id="input-idtramite" name="pk_id_tramite" value="<?php echo $verificarTramitado->pk_id_tramite;?>">
							</div>
						</div>
						<div  class="form-group row m0 mbot5">
							<label style="font-size:16px;" for="input-tipo_manifesto" class="col-form-label col-md-2 col-sm-12 mb0">Tipo de Manifesto</label>
							<div class="col-md-10 col-sm-12">
								<select class="form-control" id="input-tipo_manifesto" name="ds_tipo_manifesto">
									<option value="<?php echo $dados->pk_id_manifestacao?>"><?php echo $dados->ds_manifestacao ?></option>
									<?php foreach ($tipos as $tipo): ?>
										<option value="<?php echo $tipo->pk_id_manifestacao?>"><?php echo $tipo->ds_manifestacao ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<?php
						if($dados->ds_anonimo==1){?>
							
								<div class="form-group row m0 mbot5">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Manifestante</label>
									<div class="col-md-10 col-sm-12">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_nome?></p>
									</div>
								</div>
								<div class="form-group row m0 mbot5">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Email</label>
									<div class="col-md-10 col-sm-12">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_email?></p>
									</div>
								</div>
							
						<?php } ?>
						<div class="form-group row m0 mbot5">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Data do Manifesto</label>
							<div class="col-md-10 col-sm-12">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo date("d/m/Y", strtotime($dados->ds_datacadastro));?></p>
							</div>
						</div>
						<div class="form-group row  m0 mbot5">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Setor</label>
							<div class="col-md-10 col-sm-12">
								<select class="form-control" id="input-idsetor" name="pk_id_setor">
									<option value="<?php echo $dados->fk_id_setor?>"><?php echo $dados->ds_nome_setor ?></option>
									<?php foreach ($setores as $setor): ?>
										<option value="<?php echo $setor->pk_id_setor?>"><?php echo $setor->ds_nome_setor ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group row m0 mbot5">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Assunto</label>
							<div class="col-md-10 col-sm-12">
								<select class="form-control" id="input-idassunto" name="pk_id_assunto">
									<option value="<?php echo $dados->fk_id_assunto?>"><?php echo $dados->ds_assunto ?></option>
									<?php foreach ($assuntos as $assunto): ?>
										<option value="<?php echo $assunto->pk_id_assunto?>"><?php echo $assunto->ds_assunto ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group row m0">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Relato do Manifestante</label>
							<div style="height:120px;"class="col-md-10 col-sm-12 chanfrado bg-cordepapel scroll-y ">
								<p class="" style="font-size:18px; text-align:justify;text-ident:2em;"><?php echo $dados->ds_texto_manifestacao;?></p>
							</div>
						</div>
						<div class="form-group row m0">
							<label  style="font-size:16px;" for="ds_definicao" class="col-form-label col-sm-2">Observação:</label>
							<div class="col-sm-12">
                           		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_definicao'] ?? ''?></span> 
                                   	<div id="saida">
										<textarea name="ds_texto_observacao" id="ds_texto_observacao"><?php echo $verificarTramitado->ds_texto_observacao?></textarea>
										<script>
											CKEDITOR.replace( 'ds_texto_observacao' );
										</script>
									</div>
									<?php if(isset($error['ds_texto_observacao'])):?>
									<span class="text text-danger"><?php echo $error['ds_texto_observacao'] ?? ''?></span>
									<?php endif ?>
							</div>
						</div>
								
						<div class="card-footer">
							<div class="form-group centralizado row">
								<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
									<button type="submit" class="col-md-8 btn btn-success">Tramitar</button>
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
