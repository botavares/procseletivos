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
						<a href="<?php echo base_url("Ferias")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formFerias" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Ferias/registrar"?>" enctype="multipart/form-data">

								<input type="hidden" name="action" value="create">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
								<input type="hidden" name="pk_id_candidato" value="<?= esc(set_value('pk_id_candidato', $dadosCandidato->pk_id_candidato)) ?>">
								<input type="hidden" name="pk_id_contrato" value="<?= esc(set_value('pk_id_contrato', $dadosContratos->pk_id_contrato)) ?>">
								
								<h3>Nome:		<?= esc($dadosCandidato->ds_nome)?></h3>
								<h5>Curso:		<?= esc($curso)?></h5>
								<h5>Lotação:	<?= esc($setor)?></h5>

								<?php if($totalDiasFerias > 0): ?>
									<div class="alert alert-warning col-md-12">
										<p>O candidato já usufruiu de <strong><?= $totalDiasFerias ?></strong> dias de recesso.</p>
									</div>
								<?php endif; ?>

                                <div class="form-group row">
									<label for="input-dias-ferias" class="col-form-label col-sm-2">Ano referente</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_ano_referente'] ?? ''?></span> 
                                        <input type="text" class="form-control edital" id="input-dias-ferias" name="ds_ano_referente" value="<?php echo set_value('ds_ano_referente',$dadosFerias->ds_ano_referente);?>" required >
										<?php if(isset($error['ds_ano_referente'])):?>
											<span class="text text-danger"><?php echo $error['ds_ano_referente'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-dias-ferias" class="col-form-label col-sm-2">Dias de Recesso</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_dias_ferias'] ?? ''?></span> 
                                        <input type="text" class="form-control edital" id="input-dias-ferias" name="ds_dias_ferias" value="<?php echo set_value('ds_dias_ferias', $totalDiasRestantes);?>" required >
										<?php if(isset($error['ds_dias_ferias'])):?>
											<span class="text text-danger"><?php echo $error['ds_dias_ferias'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

                                <div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Data de Início</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_inicio'] ?? ''?></span>
										<?php
											list($ano, $mes, $dia) = explode('-', $dadosFerias->ds_data_inicio);
											$data_inicio = $dia."/".$mes."/".$ano;
										?>
                                        <input type="text" class="form-control input-data" id="input-nome-inicio" name="ds_data_inicio" value="<?php echo set_value('ds_data_inicio', $data_inicio);?>" required >
										<?php if(isset($error['ds_data_inicio'])):?>
											<span class="text text-danger"><?php echo $error['ds_data_inicio'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Data do Término</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_final'] ?? ''?></span>
										<?php
											list($anof, $mesf, $diaf) = explode('-', $dadosFerias->ds_data_final);
											$data_final = $diaf."/".$mesf."/".$anof;
										?>
                                        <input type="text" class="form-control input-data" id="input-nome-fim" name="ds_data_final" value="<?php echo set_value('ds_data_final', $data_final);?>" required >
										<?php if(isset($error['ds_data_final'])):?>
											<span class="text text-danger"><?php echo $error['ds_data_final'] ?? ''?></span>
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