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
							<form id="formSecretarias" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Instituicoes/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <input type="hidden" name="pk_id_instituicao" value="<?php echo $dados->pk_id_instituicao;?>">
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Nome da Instituição</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome" name="ds_nome" value="<?php echo set_value('ds_nome',$dados->ds_nome);?>" required >
										<?php if(isset($error['ds_nome'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-cnpj" class="col-form-label col-sm-2">Convênio</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_numero_convenio'] ?? ''?></span> 
                                        <input type="text" class="form-control convenio" id="input-convenio" name="ds_numero_convenio" value="<?php echo set_value('ds_numero_convenio',$dados->ds_numero_convenio);?>" required >
										<?php if(isset($error['ds_numero_convenio'])):?>
											<span class="text text-danger"><?php echo $error['ds_numero_convenio'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

                                <div class="form-group row">
									<label for="input-cnpj" class="col-form-label col-sm-2">CNPJ</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_cnpj'] ?? ''?></span> 
                                        <input type="text" class="form-control cnpj" id="input-cnpj" name="ds_cnpj" value="<?php echo set_value('ds_cnpj',$dados->ds_cnpj);?>" required >
										<?php if(isset($error['ds_cnpj'])):?>
											<span class="text text-danger"><?php echo $error['ds_cnpj'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-rua" class="col-form-label col-sm-2">Rua</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_rua'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-rua" name="ds_rua" value="<?php echo set_value('ds_rua',$dados->ds_rua);?>" required >
										<?php if(isset($error['ds_rua'])):?>
											<span class="text text-danger"><?php echo $error['ds_rua'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-rua" class="col-form-label col-sm-2">Número</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_numero'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-rua" name="ds_numero" value="<?php echo set_value('ds_numero',$dados->ds_numero);?>" required >
										<?php if(isset($error['ds_numero'])):?>
											<span class="text text-danger"><?php echo $error['ds_numero'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-complemento" class="col-form-label col-sm-2">Complemento</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_complemento'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-complemento" name="ds_complemento" value="<?php echo set_value('ds_complemento',$dados->ds_complemento);?>" >
										<?php if(isset($error['ds_complemento'])):?>
											<span class="text text-danger"><?php echo $error['ds_complemento'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-cep" class="col-form-label col-sm-2">CEP</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_cep'] ?? ''?></span> 
                                        <input type="text" class="form-control cep" id="input-cep" name="ds_cep" value="<?php echo set_value('ds_cep',$dados->ds_cep);?>" required >
										<?php if(isset($error['ds_cep'])):?>
											<span class="text text-danger"><?php echo $error['ds_cep'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-bairro" class="col-form-label col-sm-2">Bairro</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_bairro'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-bairro" name="ds_bairro" value="<?php echo set_value('ds_bairro',$dados->ds_bairro);?>" required >
										<?php if(isset($error['ds_bairro'])):?>
											<span class="text text-danger"><?php echo $error['ds_bairro'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-cidade" class="col-form-label col-sm-2">Cidade</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_cidade'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-cidade" name="ds_cidade" value="<?php echo set_value('ds_cidade',$dados->ds_cidade);?>" required >
										<?php if(isset($error['ds_cidade'])):?>
											<span class="text text-danger"><?php echo $error['ds_cidade'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-estado" class="col-form-label col-sm-2">Estado</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_estado'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-estado" name="ds_estado" value="<?php echo set_value('ds_estado',$dados->ds_estado);?>" required >
										<?php if(isset($error['ds_estado'])):?>
											<span class="text text-danger"><?php echo $error['ds_estado'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-email" class="col-form-label col-sm-2">Email</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_email'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-email" name="ds_email" value="<?php echo set_value('ds_email',$dados->ds_email);?>" required >
										<?php if(isset($error['ds_email'])):?>
											<span class="text text-danger"><?php echo $error['ds_email'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-email" class="col-form-label col-sm-2">Telefone</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_telefone'] ?? ''?></span> 
                                        <input type="text" class="form-control telefones" id="input-email" name="ds_telefone" value="<?php echo set_value('ds_telefone',$dados->ds_telefone);?>" required >
										<?php if(isset($error['ds_telefone'])):?>
											<span class="text text-danger"><?php echo $error['ds_telefone'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								<hr>

								<div class="card-footer">
									<div class="form-group">
										<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
											<a href="<?php echo base_url("Instituicoes")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
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