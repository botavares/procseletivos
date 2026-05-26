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
							<form id="formServicos" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Telefones/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
								<input type="hidden" name="fk_id_setor" value="<?php echo set_value('fk_id_setor',$idSetor);?>" >
                                <input type="hidden" name="pk_id_telefone" value="<?php echo set_value('pk_id_telefone',$idTelefone);?>" >
								
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Telefone</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_telefone'] ?? ''?></span> 
                                        <input type="text" class="form-control telefones" id="input-Telefone" name="ds_telefone" value="<?php echo set_value('ds_telefone',$dadosTelefone->ds_telefone);?>" >
										<?php if(isset($error['ds_telefone'])):?>
											<span class="text text-danger"><?php echo $error['ds_telefone'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-ds_tags" class="col-form-label col-sm-2">TAGS</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_tags'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-ds_tags" name="ds_tags" value="<?php echo set_value('ds_tags',$dadosTelefone->ds_tags);?>" >
										<?php if(isset($error['ds_tags'])):?>
											<span class="text text-danger"><?php echo $error['ds_tags'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
						</div>
							<div class="card-footer">
								<div class="form-group">
									<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
										<a href="<?php echo base_url("Telefones")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
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