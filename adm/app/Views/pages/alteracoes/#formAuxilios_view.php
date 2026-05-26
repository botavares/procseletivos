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
						<a href="<?php echo base_url("Auxilios")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formAuxilios" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Auxilios/registrar"?>" enctype="multipart/form-data">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <input type="hidden" id="input-id" name="pk_id_auxilio" value="<?php echo set_value('pk_id_auxilio',$dados->pk_id_auxilio);?>">

								<div class="form-group row">
									<label for="input-horas-diarias" class="col-form-label col-sm-2">Carga Horária Diária</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_horas_diarias'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-horas-diaras" name="ds_horas_diarias" value="<?php echo set_value('ds_horas_diarias',$dados->ds_horas_diarias);?>" required >
										<?php if(isset($error['ds_horas_diarias'])):?>
											<span class="text text-danger"><?php echo $error['ds_horas_diarias'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-valor-bolsa" class="col-form-label col-sm-2">Valor da Bolsa Auxílio (R$)</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_valor_bolsa'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-horas-diaras" name="ds_valor_bolsa" value="<?php echo set_value('ds_valor_bolsa',number_format($dados->ds_valor_bolsa, 2, ',', '.'));?>" required >
										<?php if(isset($error['ds_valor_bolsa'])):?>
											<span class="text text-danger"><?php echo $error['ds_valor_bolsa'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-transporte" class="col-form-label col-sm-2">Valor do Auxílio Transporte (R$)</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_valor_transporte'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-transporte" name="ds_valor_transporte" value="<?php echo set_value('ds_valor_transporte',number_format($dados->ds_valor_transporte, 2, ',', '.'));?>" required >
										<?php if(isset($error['ds_valor_transporte'])):?>
											<span class="text text-danger"><?php echo $error['ds_valor_transporte'] ?? ''?></span>
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