<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>ÁREA GERENCIAMENTO</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Início</a></li>
						<li class="breadcrumb-item active">cadastros</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">ÁREA DE DADOS</h3>
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
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
					<button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
						<i class="fas fa-times"></i>
					</button>
				</div>
			</div>
			<div class="card-body row">
				<?php if($administrador == 1){?>
					<div class="ferramentas col-lg-12 col-md-12 col-sm-12 col-xs-12 row" id="ferramentas-administrador">

						<div class="icones col-md-3  col-xs-6">
							<a href="<?php echo base_url()."/Editais"?>" class="icone-pessoais" class="btn btn-primary">
								<img src="<?php echo base_url()."/external/img/edital.png"?>"alt="Gerenciar Editais" title="Gerenciar Editais">
								<p class="narrow">Editais</p>
							</a>
						</div>

						<div class="icones col-md-3  col-xs-6">
							<a href="#" data-toggle="modal" data-target="#escolhaEditais" class="icone-pessoais" class="btn btn-primary">
								<img src="<?php echo base_url()."/external/img/convocar.png"?>"alt="Gerenciar Candidatos" title="Gerenciar Candidatos">
								<p class="narrow">Candidatos</p>
							</a>
						</div>

						<div class="icones col-md-3  col-xs-6">
							<a href="#" data-toggle="modal" data-target="#escolhaClassificacoes" class="icone-pessoais" class="btn btn-primary">
								<img src="<?php echo base_url()."/external/img/candidato_selecionado.png"?>"alt="Gerenciar Convocados" title="Gerenciar Convocados">
								<p class="narrow">Classificação</p>
							</a>
						</div>
						<div class="icones col-md-3  col-xs-6">
							<a href="#" data-toggle="modal" data-target="#relatorios" class="icone-pessoais" class="btn btn-primary">
								<img src="<?php echo base_url()."/external/img/relatorios.png"?>"alt="Relatórios" title="Relatórios">
								<p>Relatórios</p>
							</a>
						</div>

				<?php }?>
				</div>
			</div>



		</div>

	</section>

</div>



<footer class="main-footer">
<div class="float-right d-none d-sm-block">

</div>
<strong>Copyright &copy; <?php echo date('Y')?> <a href="https://www.divinópolis.mg.gov.br">Prefeitura Municipal de Divinópolis</a></strong>.
</footer>

<aside class="control-sidebar control-sidebar-dark">

</aside>

</div>
<!-- modais bootstrap para Listar Editais e seus cargos-->
<div class="modal fade" id="escolhaEditais" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    	<div class="modal-dialog" role="document">
        	<div class="modal-content">
            	<div class="modal-header">
                	<h5 class="modal-title" id="modalLabel">Escolha o Edital e o Cargo</h5>
                	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    	<span aria-hidden="true">&times;</span>
                	</button>
            	</div>
            	<div class="modal-body">
                	<form method="post" action="<?= base_url('Candidatos/salvarEscolha') ?>">
                    <!-- CSRF com ID -->
						<input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						<div class="form-group">
							<label for="edital">Edital</label>
							<select class="form-control select-edital" data-target="#select-cargo" name="edital" required>
								<option value="">Selecione um Edital</option>
								<?php foreach ($editais as $edital): ?>
									<option value="<?= $edital->pk_id_edital; ?>">
										<?= substr_replace($edital->ds_numero_edital, '/', -4, 0) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-group">
							<label for="cargo">Selecione o Cargo</label>
							<select class="form-control select-cargo" id="select-cargo" name="cargo" required>
								<option value="">Selecione um cargo</option>
							</select>
						</div>

                    	<button type="submit" class="btn btn-primary">Enviar</button>
                    	<button type="button" class="btn btn-warning" data-dismiss="modal">Sair</button>
                	</form>
            	</div>
        	</div>
    	</div>
	</div>
</div>

<!-- modais bootstrap para Listar Classificações de acordo com edital e seus cargos-->
<div class="modal fade" id="escolhaClassificacoes" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    	<div class="modal-dialog" role="document">
        	<div class="modal-content">
            	<div class="modal-header">
                	<h5 class="modal-title" id="modalLabel">Escolha o Edital e o Cargo</h5>
                	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    	<span aria-hidden="true">&times;</span>
                	</button>
            	</div>
            	<div class="modal-body">
                	<form method="post" action="<?= base_url('Classificacoes/salvarEscolha') ?>">
                    <!-- CSRF com ID -->
						<input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						<div class="form-group">
							<label for="edital">Edital</label>
							<select class="form-control select-edital" data-target="#select-cargo-classificacoes" name="edital" required>
								<option value="">Selecione um Edital</option>
								<?php foreach ($editais as $edital): ?>
									<option value="<?= $edital->pk_id_edital; ?>">
										<?= substr_replace($edital->ds_numero_edital, '/', -4, 0) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-group">
							<label for="cargo">Selecione o Cargo</label>
							<select class="form-control select-cargo" id="select-cargo-classificacoes" name="cargo" required>
								<option value="">Selecione um cargo</option>
							</select>
						</div>

                    	<button type="submit" class="btn btn-primary">Enviar</button>
                    	<button type="button" class="btn btn-warning" data-dismiss="modal">Sair</button>
                	</form>
            	</div>
        	</div>
    	</div>
	</div>
</div>


<div class="modal fade" id="modalErros" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				
				<h5 id="nome-erro" class="modal-title font25 red bold ta-center"> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
        		</button>
        		
        		
      		</div>
      		<div class="modal-body">
        		<p id="descricao-erro" class="font20 "></p>
		  		
			</div>
			<div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal"><strong>Retornar</strong></button>
		
			</div>	
    	</div>
  	</div>
</div>





