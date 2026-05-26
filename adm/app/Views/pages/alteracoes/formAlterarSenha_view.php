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
						<a href="<?php echo base_url("Vagas")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="forVagas" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Login/alterarSenha"?>">

								<input type="hidden" name="action" value="create">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <div class="form-group">
                                    <label class="fnt14 col-md-12 col-sm-12 col-xs-12">Digite a senha atual:</label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input type="password" name="senhaAtual" class="form-control" id="input-senhaatual" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="fnt14 col-md-12 col-sm-12 col-xs-12">Digite a nova senha:</label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input type="password" name="senhaNova" class="form-control" id="input-senhanova" required>
                                    </div>
                                </div>
								
								<hr>

								<div class="card-footer">
									<div class="form-group">
										<div class="col-md-12 col-sm-8 col-xs-12 col-md-offset-1">
											
											<button type="submit" class="btn btn-success  col-md-12">Salvar</button>
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