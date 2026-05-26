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
							<form id="formUnidades" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Unidades/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                
								<input type="hidden" name="pk_id_unidade" value="<?php echo $dados->pk_id_unidade?>">
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Nome do unidade</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_unidade'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-unidade" name="ds_nome_unidade" value="<?php echo set_value('ds_nome_unidade',$dados->ds_nome_unidade);?>" required >
										<?php if(isset($error['ds_nome_unidade'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_unidade'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-rua-unidade" class="col-form-label col-sm-2">Rua de localização</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_rua_unidade'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-unidade" name="ds_rua_unidade" value="<?php echo set_value('ds_rua_unidade',$dados->ds_rua_unidade);?>" required >
										<?php if(isset($error['ds_rua_unidade'])):?>
											<span class="text text-danger"><?php echo $error['ds_rua_unidade'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-numero-unidade" class="col-form-label col-sm-2">número do endereço</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_numero_unidade'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-numero-unidade" name="ds_numero_unidade" value="<?php echo set_value('ds_numero_unidade',$dados->ds_numero_unidade);?>" required >
										<?php if(isset($error['ds_numero_unidade'])):?>
											<span class="text text-danger"><?php echo $error['ds_numero_unidade'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-ruas-unidade" class="col-form-label col-sm-2">CEP</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger cep"><?php echo session()->getFlashdata('errors')['ds_cep_unidade'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-rua-unidade" name="ds_rua_unidade" value="<?php echo set_value('ds_cep_unidade',$dados->ds_cep_unidade);?>" required >
										<?php if(isset($error['ds_cep_unidade'])):?>
											<span class="text text-danger"><?php echo $error['ds_cep_unidade'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-ruas-unidade" class="col-form-label col-sm-2">Bairro</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_cep_unidade'] ?? ''?></span> 
										<input type="text" class="form-control" id="input-bairro-unidade" name="ds_bairro_unidade" value="<?php echo set_value('ds_bairro_unidade',$dados->ds_bairro_unidade);?>" required >
										<?php if(isset($error['ds_cep_unidade'])):?>
											<span class="text text-danger"><?php echo $error['ds_cep_unidade'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>					
								<hr>
								
								
								<div class="form-group row">
									<label for="input-horario-inicio" class="col-form-label col-sm-2">Horário de início</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_hora_inicio'] ?? ''?></span> 
										<input type="text" class="form-control" id="input-inicio-unidade" name="ds_hora_inicio" value="<?php echo set_value('ds_hora_inicio',$dados->ds_hora_inicio);?>" required >
										<?php if(isset($error['ds_hora_inicio'])):?>
											<span class="text text-danger"><?php echo $error['ds_hora_inicio'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>	
								<div class="form-group row">
									<label for="input-horario-inicio" class="col-form-label col-sm-2">Horário de Encerramento</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_hora_encerra'] ?? ''?></span> 
										<input type="text" class="form-control" id="input-inicio-unidade" name="ds_hora_encerra" value="<?php echo set_value('ds_hora_encerra',$dados->ds_hora_encerra);?>" required >
										<?php if(isset($error['ds_hora_encerra'])):?>
											<span class="text text-danger"><?php echo $error['ds_hora_encerra'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

							</div>
							<div class="card-footer">
								<div class="form-group">
									<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
										<a href="<?php echo base_url("Unidades")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
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