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
							<form id="formSetores" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Setores/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
								<input type="hidden" name="pk_id_setor" value="<?= $dados->pk_id_setor ?>" />
                                
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Nome do Setor</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_setor'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-setor" name="ds_nome_setor" value="<?php echo set_value('ds_nome_setor',$dados->ds_nome_setor);?>" required >
										<?php if(isset($error['ds_nome_setor'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_setor'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Telefone</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_telefone'] ?? ''?></span> 
                                        <input type="text" class="form-control input-telefone" id="input-telefone" name="ds_telefone" value="<?php echo set_value('ds_telefone',$dados->ds_telefone);?>" required >
										<?php if(isset($error['ds_telefone'])):?>
											<span class="text text-danger"><?php echo $error['ds_telefone'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								<div class="form-group row">
									<label for="input-secretaria" class="col-form-label col-sm-2">Secretaria</label>
									<div class="col-sm-10">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['pk_id_secretaria'] ?? ''?></span>
										<select id="input-secretaria" class="form-control" name="pk_id_secretaria" required >
											<option value="<?php echo $dados->fk_id_secretaria?>" selected><?php echo $dados->ds_nome_secretaria?></option>
											<?php foreach($secretarias as $secretaria): ?>
												<option value="<?php echo $secretaria->pk_id_secretaria?>" <?php if(old('pk_id_secretaria')==$secretaria->pk_id_secretaria) echo('selected');?>><?php echo $secretaria->ds_nome_secretaria?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>



                                <div class="form-group row">
									<label for="input-status" class="col-form-label col-sm-2">Status do Setor</label>
									<div class="col-sm-10">
                                    	<select id="input-status" class="form-control" name="ds_status" required >

										<?php
												switch($dados->ds_status){
													case 1:
														echo '<option value="1" selected>ATIVO</option>';
														break;
													case 2:
														echo '<option value="2" selected>INATIVO</option>';
														break;
													case 3:
														echo '<option value="3" selected>PARALIZADO</option>';
														break;
												}
											?>

											<option value="1" <?php if(old('ds_status')==1) echo('selected');?>>ATIVO</option>
											<option value="2" <?php if(old('ds_status')==2) echo('selected');?>>INATIVO</option>
											<option value="3" <?php if(old('ds_status')==3) echo('selected');?>>PARALIZADO</option>
										</select>
									</div>
								</div>
								
								<hr>

								<div class="card-footer">
									<div class="form-group">
										<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
											<a href="<?php echo base_url("Setores")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
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