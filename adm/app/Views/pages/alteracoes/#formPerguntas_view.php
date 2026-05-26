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
					<form id="formRespostas" class="formEditor form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Perguntas/registrar"?>">
						<input type="hidden" name="action" value="update">
						<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						<input type="hidden" class="form-control" id="input-idPergunta" name="pk_id_pergunta" value="<?php echo $dados->pk_id_pergunta;?>">
				
						<div class="form-group row m0">
							<label  style="font-size:16px;" for="ds_pergunta" class="col-form-label col-sm-2">A pergunta:</label>
							<div class="col-sm-12">
                           		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_pergunta'] ?? ''?></span> 
                                   	<div id="saida">
										<textarea name="ds_pergunta" id="ds_pergunta"><?php echo $dados->ds_pergunta?></textarea>
										<script>
											CKEDITOR.replace( 'ds_pergunta' );
										</script>
									</div>
									<?php if(isset($error['ds_pergunta'])):?>
									<span class="text text-danger"><?php echo $error['ds_pergunta'] ?? ''?></span>
									<?php endif ?>
							</div>
						</div>
						<div class="form-group row m0">
							<label  style="font-size:16px;" for="ds_resposta" class="col-form-label col-sm-2">A resposta:</label>
							<div class="col-sm-12">
                           		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_resposta'] ?? ''?></span> 
                                   	<div id="saida">
										<textarea name="ds_resposta" id="ds_resposta"><?php echo $dados->ds_resposta?></textarea>
										<script>
											CKEDITOR.replace( 'ds_resposta' );
										</script>
									</div>
									<?php if(isset($error['ds_resposta'])):?>
									<span class="text text-danger"><?php echo $error['ds_resposta'] ?? ''?></span>
									<?php endif ?>
							</div>
						</div>
								
						<div class="card-footer">
							<div class="form-group centralizado row">
								<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
									<button type="submit" class="col-md-8 btn btn-success">Salvar</button>
								</div>
								<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
									<a href="<?php echo base_url("Respostas")?>" type="button" class="col-md-8 btn btn-warning" data-dismiss="modal">Voltar</a>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
    	</div>
	</section>
