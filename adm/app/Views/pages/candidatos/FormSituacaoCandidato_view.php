<div class="content-wrapper">
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
							    <h1><?php echo $titulo?></h1>
								<?php if(session()->has('mensagemError')):?>
								<div class="porta-mensagem alert alert-danger col-md-12 font20 ta-center">
									<?= esc(session('mensagemError')) ?>
								</div>
							<?php endif ?>
                    		<?php if(session()->has('mensagemSuccess')):?>
								<div class="porta-mensagem alert alert-success col-md-12 font20 ta-center">
									<?= esc(session('mensagemSuccess')) ?>
								</div>
							<?php endif ?>
                            </h3>
							
						</div>
						<a href="<?php echo base_url("Candidatos/".$idEdital."/".$idCargo)?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formSituacaoCandidato" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Candidatos/registrarSituacao"?>">

								<input type="hidden" name="action" value="create">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
								<input type="hidden" name="id_candidato" value="<?php echo $idCandidato ?>">
								<input type="hidden" name="id_cargo" value="<?php echo $idCargo ?>">
								<input type="hidden" name="id_edital" value="<?php echo $idEdital ?>">
                                
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Candidato</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-Cargo" name="ds_nome" value="<?php echo set_value('ds_nome',$candidato);?>" readonly >
										<?php if(isset($error['ds_nome'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Cargo</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-Cargo" name="ds_nome" value="<?php echo set_value('ds_nome',$nomeCargo);?>" readonly >
										<?php if(isset($error['ds_nome'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Situação</label>
									<div class="col-sm-10">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['situacao'] ?? ''?></span> 
                                        <select class="form-control" name="situacao" required>
                                            <option value="1" <?= $dadosSituacao->situacao == 1 ? 'selected' : '' ?>>CONVOCADO</option>
											<option value="2" <?= $dadosSituacao->situacao == 2 ? 'selected' : '' ?>>CONTRATADO</option>
											<option value="3" <?= $dadosSituacao->situacao == 3 ? 'selected' : '' ?>>DESCLASSIFICADO</option>
                                        </select>
                                    </div>
									<?php if(isset($error['situacao'])):?>
										<span class="text text-danger"><?php echo $error['situacao'] ?? ''?></span>
									<?php endif ?>
								</div>
								
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