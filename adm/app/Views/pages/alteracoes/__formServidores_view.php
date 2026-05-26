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
							<form id="formServicos" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Servicos/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                
								<input type="hidden" name="pk_id_servico" value="<?php echo $dados->pk_id_servico?>">
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Título do Serviço</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_servico'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-servico" name="ds_nome_servico" value="<?php echo set_value('ds_nome_servico',$dados->ds_nome_servico);?>" required >
										<?php if(isset($error['ds_nome_servico'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_servico'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-classes" class="col-form-label col-sm-2">Classe referência</label>
									<div class="col-sm-10">
										
                                    	<select id="input-classes" class="form-control" name="fk_id_classe" required >
										<?php
										
										switch($dados->fk_id_classe){
												case 1:
													echo '<option value="1" selected>Cidadão</option>';
													break;
												case 2:
													echo '<option value="2" selected>Empresa</option>';
													break;
												case 3:
													echo '<option value="3" selected>Servidor</option>';
													break;
												case 4:
													echo '<option value="4" selected>Global</option>';
													break;
											}
									?>
											<?php foreach($classes as $classe): ?>
												<option value="<?php echo $classe->pk_id_classe ?>" <?php if(old('fk_id_classe')==$classe->pk_id_classe) echo('selected');?>><?php echo $classe->ds_nome_classe ?></option>
											<?php endforeach ?>
											
										</select>
									</div>
								</div>

								<hr>
								<div class="form-group row">
									<label for="input-categorias" class="col-form-label col-sm-2">Categoria</label>
									<div class="col-sm-10">
                                    	<select id="input-categorias" class="form-control" name="fk_id_categoria" required >
										<option value="<?php echo $categoriasId->pk_id_categoria ?>"><?php echo $categoriasId->ds_nome_categoria ?></option>
											<?php foreach($categorias as $categoria): ?>
												<option value="<?php echo $categoria->pk_id_categoria ?>" <?php if(old('fk_id_categoria')==$categoria->pk_id_categoria) echo('selected');?>><?php echo $categoria->ds_nome_categoria ?></option>
											<?php endforeach ?>
											
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-link-servico" class="col-form-label col-sm-2">Link para acesso</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_link_servico'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-link-servico" name="ds_link_servico" value="<?php echo set_value('ds_link_servico',$dados->ds_link_servico);?>" required >
										<?php if(isset($error['ds_link_servico'])):?>
											<span class="text text-danger"><?php echo $error['ds_link_servico'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-descricao-servico" class="col-form-label col-sm-2">Descrição do serviço<br><small>Max. 130 caracteres</small></label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_descricao_servico'] ?? ''?></span> 
										<textarea type="text" class="form-control" maxlength="130" id="input-link-servico" name="ds_descricao_servico" required ><?php echo set_value('ds_descricao_servico',$dados->ds_descricao_servico);?></textarea>
										<?php if(isset($error['ds_descricao_servico'])):?>
											<span class="text text-danger"><?php echo $error['ds_descricao_servico'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<hr>
								<div class="form-group row">
									<label for="input-status" class="col-form-label col-sm-2">Status do serviço</label>
									<div class="col-sm-10">
									<select id="input-status" class="form-control" name="ds_status" required >
											<?php
												switch($dados->ds_status){
													case 1:
														echo '<option value="1" selected>ATIVA</option>';
														break;
													case 2:
														echo '<option value="2" selected>INATIVA</option>';
														break;
													case 3:
														echo '<option value="3" selected>PARALIZADA</option>';
														break;
												}
											?>
											<option value="1" <?php if(old('ds_status')==1) echo('selected'); ($dados->ds_status == 1) ? 'selected' : ''; ?>>ATIVA</option>
											<option value="2" <?php if(old('ds_status')==2) echo('selected'); ($dados->ds_status == 2) ? 'selected' : '';?>>INATIVA</option>
											<option value="3" <?php if(old('ds_status')==3) echo('selected'); ($dados->ds_status == 3) ? 'selected' : '';?>>PARALIZADA</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-destaque" class="col-form-label col-sm-2">Colocar como destaque</label>
									<div class="col-sm-10">

                                    	<select id="input-destaque" class="form-control" name="ds_destaque" required >
											<?php
												switch($dados->ds_destaque){
													case 1:
														echo '<option value="1" selected>SIM</option>';
														break;
													case 0:
														echo '<option value="0" selected>NÃO</option>';
														break;
												}
											?>

											<option value="1" <?php if(old('ds_destaque')==1) echo('selected');?>>SIM</option>
											<option value="0" <?php if(old('ds_destaque')==0) echo('selected');?>>NÃO</option>
										</select>

									</div>
								</div>
							</div>
							<div class="card-footer">
								<div class="form-group">
									<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
										<a href="<?php echo base_url("Servidores")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
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
	</section>
</div>