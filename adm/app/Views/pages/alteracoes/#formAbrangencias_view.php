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
						<a href="<?php echo base_url("Abrangencias")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formAbrangencias" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Abrangencias/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
								<input type="hidden" name="pk_id_abrangencia" value="<?= $dados->pk_id_abrangencia ?>" />
                                
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Nome da Abrangência</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_abrangencia'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-abrangencia" name="ds_nome_abrangencia" value="<?php echo set_value('ds_nome_abrangencia',$dados->ds_nome_abrangencia);?>" required >
										<?php if(isset($error['ds_nome_abrangencia'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_abrangencia'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-status" class="col-form-label col-sm-2">Status da Abrangência</label>
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
												}
											?>

											<option value="1" <?php if(old('ds_status')==1) echo('selected');?>>ATIVO</option>
											<option value="2" <?php if(old('ds_status')==2) echo('selected');?>>INATIVO</option>
										</select>
									</div>
								</div>
								<div class="card-footer">
									<div class="form-group">
										<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
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