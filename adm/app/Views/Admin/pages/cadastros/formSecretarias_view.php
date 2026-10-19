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
						<div class="card-body">
							<form id="formSecretarias" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Secretarias/registrar"?>">

								<input type="hidden" name="action" value="create">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Nome da Secretaria</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_secretaria'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome" name="ds_nome_secretaria" value="<?php echo set_value('ds_nome_secretaria');?>" required >
										<?php if(isset($error['ds_nome_secretaria'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_secretaria'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-sigla" class="col-form-label col-sm-2">Sigla da Secretaria</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_sigla_secretaria'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-sigla" name="ds_sigla_secretaria" value="<?php echo set_value('ds_sigla_secretaria');?>" required >
										<?php if(isset($error['ds_sigla_secretaria'])):?>
											<span class="text text-danger"><?php echo $error['ds_sigla_secretaria'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-email" class="col-form-label col-sm-2">Nome do secretário(a)</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_secretario_diretor'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-email" name="ds_secretario_diretor" value="<?php echo set_value('ds_secretario_diretor');?>" required >
										<?php if(isset($error['ds_secretario_diretor'])):?>
											<span class="text text-danger"><?php echo $error['ds_secretario_diretor'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-email" class="col-form-label col-sm-2">Email da Secretaria</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_email_secretaria'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-email" name="ds_email_secretaria" value="<?php echo set_value('ds_email_secretaria');?>" required >
										<?php if(isset($error['ds_email_secretaria'])):?>
											<span class="text text-danger"><?php echo $error['ds_email_secretaria'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-email" class="col-form-label col-sm-2">Telefone da Secretaria</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_telefone_secretaria'] ?? ''?></span> 
                                        <input type="text" class="form-control telefones" id="input-email" name="ds_telefone_secretaria" value="<?php echo set_value('ds_telefone_secretaria');?>" required >
										<?php if(isset($error['ds_telefone_secretaria'])):?>
											<span class="text text-danger"><?php echo $error['ds_telefone_secretaria'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								<hr>

								<div class="card-footer">
									<div class="form-group">
										<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
											<a href="<?php echo base_url("Secretarias")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
											<button type="reset" class="btn btn-primary">Limpar</button>
											<button type="submit" class="btn btn-success">Salvar</button>
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