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
						<a href="<?php echo base_url("Seguros")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formSeguros" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Seguros/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
								<input type="hidden" name="pk_id_seguro" value="<?php echo $dados->pk_id_seguro ?>">
                                
								<div class="form-group row">
									<label for="input-seguradora" class="col-form-label col-sm-2">Nome da Seguradora</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_seguradora'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-seguradora" name="ds_seguradora" value="<?php echo set_value('ds_seguradora',$dados->ds_seguradora);?>" required >
										<?php if(isset($error['ds_seguradora'])):?>
											<span class="text text-danger"><?php echo $error['ds_seguradora'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-apolice" class="col-form-label col-sm-2">Número da Apólice</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_numero_seguro'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-apolice" name="ds_numero_seguro" value="<?php echo set_value('ds_numero_seguro',$dados->ds_numero_seguro);?>" required >
										<?php if(isset($error['ds_numero_seguro'])):?>
											<span class="text text-danger"><?php echo $error['ds_numero_seguro'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-apolice" class="col-form-label col-sm-2">Valor da Apólice</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_apolice'] ?? ''?></span> 
                                        <input type="number" step="0.01" class="form-control" id="input-apolice" name="ds_apolice" value="<?php echo set_value('ds_apolice',$dados->ds_apolice);?>" required >
										<?php if(isset($error['ds_apolice'])):?>
											<span class="text text-danger"><?php echo $error['ds_apolice'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-apolice" class="col-form-label col-sm-2">CNPJ</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_cnpj'] ?? ''?></span> 
                                        <input type="text" class="form-control cnpj" id="input-apolice" name="ds_cnpj" value="<?php echo set_value('ds_cnpj',$dados->ds_cnpj);?>" required >
										<?php if(isset($error['ds_cnpj'])):?>
											<span class="text text-danger"><?php echo $error['ds_cnpj'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								
								<hr>

								<div class="card-footer">
									<div class="form-group">
										<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
											<button type="reset" class="btn btn-primary col-md-2">Limpar</button>
											<button type="submit" class="btn btn-success  col-md-2">Salvar</button>
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