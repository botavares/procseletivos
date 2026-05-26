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
								<?php if(session()->has('success')):?>
								<div class="porta-mensagem alert alert-success col-md-12">
									<span class="text text-white text-center bold font22"><?php echo session()->getFlashdata('success')?></span>
								</div>
							<?php endif ?>
                            </h3>
							
						</div>
						<div class="card-body">
							<form id="formSolicitacao" class="form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/GrupoSolicitacoes/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

								
									<input type="hidden" name="pk_id_gruposolicitacao" value="<?php echo $dados->pk_id_gruposolicitacao?>">
										
                                
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Nome do Grupo de Solicitações</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_grupo_solicitacao'] ?? ''?></span> 
										<input type="text" class="form-control" id="input-nome-gruposolicitacao" name="ds_nome_grupo_solicitacao" value="<?php echo set_value('ds_nome_grupo_solicitacao',$dados->ds_nome_grupo_solicitacao);?>" required >
										<?php if(isset($error['ds_nome_grupo_solicitacao'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_grupo_solicitacao'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-status" class="col-form-label col-sm-2">Status do Grupo de Solicitações</label>
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

								<hr>

								
								
							<div class="card-footer">
								<div class="form-group">
									<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
										<a href="<?php echo base_url("GrupoSolicitacoes")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
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