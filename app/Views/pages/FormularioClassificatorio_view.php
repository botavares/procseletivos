
	<div class="container-lg ">
		<div class="col-md-12 row">
			<div class="col-md-12 mb-5 ">
				<div class="main-content pl-sm-3 mt-0" id="main-content">
					<nav class="br-breadcrumb" aria-label="Breadcrumbs">
						<ol class="crumb-list" role="list">
							<li class="crumb home">
								<a class="br-button circle" href="<?php echo base_url("Home")?>">
									<span class="sr-only">Página inicial</span>
									<i class="fas fa-home"></i>
								</a>
							</li>
							<li class="crumb"><i class="icon fas fa-chevron-right"></i><a href="<?php echo base_url("Cadastros")?>">Candidato</a>
							</li>
							<li class="crumb" data-active="active">
								<i class="icon fas fa-chevron-right"></i>
								<span tabindex="0" aria-current="page">Dados Classificatórios (Página Atual)</span>
							</li>
						</ol>
					</nav>
					<div class="mrg-top-10 pdd-5 br-message info">
                    	<div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>
                    	<div class="content" aria-label="" role="alert">
							<span class="message-title">Registrando Dados Classificatórios</span>
							<span class="message-body">
								<p class="saudação"><?php echo $saudacao?>, <?php echo $params['nome'].'!'?></p>
								<p class="saudação">Nesse formulário você irá registrar os seus dados classificatórios para o cargo de <strong><?php echo $cargos->ds_nome_cargo?></strong>.</p>
							</span>
						</div>
					</div>
					<div class="area-botoes mt-4 mb-4">
            			<a  class="br-button primary voltar" href="<?php echo base_url("Cadastros")?>"><i class="fas fa-arrow-left"></i> Voltar</a>
        			</div>

					<!--FORMULÁRIO DE DADOS CLASSIFICATÓRIOS-->

					<section class="bloco-formulario">
						<form id="formulario-dadosformulario" action="<?php echo base_url("Cadastros/registrarDadosClassificatorios")?>" method="post">
							<input type="hidden" value="<?= csrf_hash(); ?>" name="<?= csrf_token(); ?>" id="csrf">
							<input type="hidden" name="idCandidato" value="<?php echo $idCandidato?>">
							<input type="hidden" name="idCargo" value="<?php echo $idCargo?>">
							<input type="hidden" name="idEdital" value="<?php echo $idEdital?>">

							<!-- EXPERIENCIA PROFISSIONAL -->
							 <?php 
							 	if($experienciasClassificatorias){
									echo view('pages/partials/classificatorios/_experiencias');
								}
							?>
									

							<!-- ESCOLARIDADE -->
							 <?php 
							 	if($escolaridadesClassificatorias){
									echo view('pages/partials/classificatorios/_escolaridades');
								} 
							?>

							<!-- CURSOS DE APERFEICOAMENTO -->
							 <?php 
								if($aperfeicoamentoClassificatorios){
									echo view('pages/partials/classificatorios/_aperfeicoamentos');
							 }
							 ?>
							

							<!-- CRITERIOS ADICIONAIS -->
							 <?php 
								if($criteriosAdicionaisClassificatorios){
									echo view('pages/partials/classificatorios/_criteriosAdicionais');
							 }
							?>

							<div class="mrg-bottom-30">
								<fieldset>
									<hr>
									<div class="col-sm-10 col-lg-12">
										<div class="br-button-group">
												<button class="br-button block primary mb-3 " type="submit">Salvar seus dados</button>
											<a href="<?php echo base_url("Cadastros")?>" class="br-button block secondary mb-3" type="reset">Voltar</a>
										</div>
									</div>
								</fieldset>
							</div>
						</form>
					</section>
				</div>
			</div>
		</div>
	</div>
