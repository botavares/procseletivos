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
						<a href="<?php echo base_url("Vagas")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formSetores" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Vagas/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
								<input type="hidden" name="pk_id_vaga" value="<?= $dados->pk_id_vaga ?>" />
                                
								<div class="form-group row">
									<label for="input-setor" class="col-form-label col-sm-2">Nome do Setor</label>
									<div class="col-sm-10">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['pk_id_setor'] ?? ''?></span>
										<select id="input-altera-setor" class="form-control" name="pk_id_setor" required >
											<option value="<?php echo $dados->fk_id_setor?>" selected><?php echo $dados->ds_nome_setor?></option>
											<?php foreach($setores as $setor): ?>
												<option value="<?php echo $setor->pk_id_setor?>" <?php if(old('pk_id_setor')==$setor->pk_id_setor) echo('selected');?>><?php echo $setor->ds_nome_setor?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-curso" class="col-form-label col-sm-2">Curso</label>
									<div class="col-sm-10">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['pk_id_curso'] ?? ''?></span>
										<select id="input-altera-curso" class="form-control" name="pk_id_curso" required >
											<option value="<?php echo $dados->fk_id_curso?>" selected><?php echo $dados->ds_nome_curso?></option>
											<?php foreach($cursos as $curso): ?>
												<option value="<?php echo $curso->pk_id_curso?>" <?php if(old('pk_id_curso')==$curso->pk_id_curso) echo('selected');?>><?php echo $curso->ds_nome_curso?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								
								<div class="form-group row">
									<label for="input-altera-vagas" class="col-form-label col-sm-2">Vagas</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_vagas'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-altera-vagas" name="ds_vagas" value="<?php echo set_value('ds_vagas',$dados->ds_vagas);?>" required >
										<?php if(isset($error['ds_vagas'])):?>
											<span class="text text-danger"><?php echo $error['ds_vagas'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-altera-responsavel" class="col-form-label col-sm-2">Responsável</label>
									<div class="col-sm-10">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_responsavel'] ?? ''?></span>
										<input type="text" class="form-control" id="input-altera-responsavel" name="ds_responsavel" value="<?php echo set_value('ds_responsavel',$dados->ds_responsavel);?>" required >
										<?php if(isset($error['ds_responsavel'])):?>
											<span class="text text-danger"><?php echo $error['ds_responsavel'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								<div class="form-group row">
									<label for="input-altera-telefone" class="col-form-label col-sm-2">Telefone Responsável</label>
									<div class="col-sm-10">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_telefone'] ?? ''?></span>
										<input type="text" class="form-control telefones" id="input-altera-telefone" name="ds_telefone" value="<?php echo set_value('ds_telefone',$dados->ds_telefone);?>" required >
										<?php if(isset($error['ds_telefone'])):?>
											<span class="text text-danger"><?php echo $error['ds_telefone'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="col-sm-12">
									<label for="input-altera-observacao" class="col-form-label col-sm-2">Observação</label>
                           			<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_definicao'] ?? ''?></span> 
                                   		<div id="saida">
											<textarea name="ds_observacao" id="input-altera-observacao">
												<?php echo set_value('ds_observacao',$dados->ds_observacao);?>
                                        	</textarea>
											<script>
												CKEDITOR.replace( 'ds_observacao' );
											</script>
										</div>
										<?php if(isset($error['ds_observacao'])):?>
										<span class="text text-danger"><?php echo $error['ds_observacao'] ?? ''?></span>
										<?php endif ?>
								</div>


                                
								
								<hr>

								<div class="card-footer">
									<div class="form-group">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<button type="submit" class="btn btn-success col-md-12">Salvar</button>
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