
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
								if(!empty($experienciaProfissional)){
									foreach($experienciaProfissional as $valueExperiencia){
										
										$idExperiencia = $valueExperiencia->fk_id_experiencia;
										$nomeCampo = "quantidadeExperiencia".$idExperiencia;
										//pegar a array salva ou o zero se não existir
										$quantidadeBanco = $experienciasSalvas[$idExperiencia] ?? 0;
										$valorSelecionado = old($nomeCampo, $quantidadeBanco);

										if($valueExperiencia->ds_obrigatorio == '0'){
											if($valueExperiencia->ds_tipo_campo == 'SELECT'){
												$totalDeAnos = ($valueExperiencia->ds_quantidade_maxima/$valueExperiencia->ds_quantidade_minima);
							?>
												<legend class="font-18 bold mrg-0" >Experiência <?= $valueExperiencia->ds_nome_experiencia?></legend>
												<hr>
												<div class=" mrg-bottom-30">
													<fieldset class="experiencia">
														
														
														<div class="col-sm-10 col-lg-12">
															<label for="select-experiencia" class="text-normal">Informe sua experiência:</label>
															<div class="select-container mt-3">
																<i class="fas fa-search"></i>
                                            					<select id="<?= $nomeCampo?>" class="select-experiencia form-control" name="<?= $nomeCampo?>" required>
																	<option value="0" <?php echo set_select('quantidadeExperiencia','0')?>>Não possui <?php echo $valueExperiencia->ds_tipo_experiencia?></option>
										
																	<?php
																		for($i=1;$i<=$totalDeAnos;$i++){
																			if($i == $totalDeAnos){
																				$textoOption = $i." ou mais ". $valueExperiencia->ds_tipo_experiencia;
																			}else{
																				$textoOption = $i." ".$valueExperiencia->ds_tipo_experiencia;
																			}
																		?>
																		<option value="<?= $i ?>" <?= (string)$valorSelecionado === (string)$i ? 'selected' : '' ?>><?php echo $textoOption?></option>
																		<?php
																		}
																	?>
																</select>
																<i class="fas fa-angle-down"></i>
															</div>
														</div>
													</fieldset>
												</div>
											<?php 
											}
										}
									}
								}
							?>
							
							<?php
if(!empty($escolaridadesClassificatorias)){
?>							 
    <legend class="font-18 bold mrg-0">Escolaridade</legend>
    <hr>
    <div class="mrg-bottom-30">
        <fieldset class="experiencia">
            <div class="br-input mb-4 col-sm-10 col-lg-12">
                <label class="text-normal">Aponte sua escolaridade:</label>

                <?php foreach($escolaridadesClassificatorias as $escolaridade): 
						

                    $idEscolaridade = $escolaridade->fk_id_escolaridade;
                    // checkbox marcado?
                    /*$checked = in_array(
                        $idEscolaridade, 
                        $idsEscolaridadesCandidato ?? []
                    ) ? 'checked' : '';
					*/
                    // quantidade já cadastrada
                    $quantidade = $dadosEscolaridade[$idEscolaridade] ?? 0;
					if($quantidade > 0){
						$checked = 'checked';
					}else{
						$checked = '';
					}
                ?>

                    <!-- CHECKBOX -->
                    <?php if($escolaridade->ds_tipo_campo == "CHECK"): ?>
                        <div class="br-checkbox mt-3">
                            <input 
                                type="checkbox" 
                                id="checkbox-escolaridade<?= $idEscolaridade ?>"
                                name="escolaridade<?= $idEscolaridade ?>"
                                value="1"
                                <?= $checked ?>
                            >
                            <label for="checkbox-escolaridade<?= $idEscolaridade ?>">
                                <?= $escolaridade->ds_nome_escolaridade ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <!-- INPUT NUMÉRICO -->
                    <?php if($escolaridade->ds_tipo_campo == "INPUT"): ?>
                        <div class="mt-3">
                            <label for="input-escolaridade<?= $idEscolaridade ?>" class="text-normal">
                                <?= $escolaridade->ds_nome_escolaridade ?>
                            </label>
                            <input 
                                type="number"
                                min="0"
                                id="input-escolaridade<?= $idEscolaridade ?>"
                                name="escolaridade<?= $idEscolaridade ?>"
                                value="<?= old("escolaridade".$idEscolaridade, $quantidade) ?>"
                                class="form-control mt-2 col-sm-12 col-lg-4"
                            >
                        </div>
						<span class="br-message info pdd-10"><div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>Informe o total de pós-graduação que você possui de acordo com as áreas citadas acima</span>
                    <?php endif; ?>

                <?php endforeach; ?>

            </div>
        </fieldset>
    </div>
<?php
}
?>

    



							<?php
if($aperfeicoamentoClassificatorios){
?>
    <legend class="font-18 bold mrg-0">Cursos de Aperfeiçoamento</legend>
    <hr>
    <div class="mrg-bottom-30">
        <fieldset class="aperfeicoamento">
            <div class="br-input mb-4 col-sm-10 col-lg-12">
                <label class="text-normal">
                    Aponte seu(s) curso(s) de aperfeiçoamento(s):
                </label>

                <?php foreach($aperfeicoamentoClassificatorios as $aperfeicoamento): 

                    $idAperfeicoamento = $aperfeicoamento["fk_id_curso"];

                    // checkbox marcado?
					/*
                    $checked = in_array(
                        $idAperfeicoamento, 
                        $idsAperfeicoamentosCandidato ?? []
                    ) ? 'checked' : '';
					*/
                    // quantidade (caso futuramente tenha INPUT)
                    $quantidade = $dadosAperfeicoamento[$idAperfeicoamento] ?? 0;
					if($quantidade > 0){
						$checked = 'checked';
					}else{
						$checked = '';
					}
                ?>

                    <!-- CHECKBOX -->
                    <?php if($aperfeicoamento["ds_tipo_campo"] == "CHECK"): ?>
                        <div class="br-checkbox mt-3">
                            <input 
                                type="checkbox" 
                                id="aperfeicoamento<?= $idAperfeicoamento ?>"
                                name="aperfeicoamento<?= $idAperfeicoamento ?>"
                                value="<?= $aperfeicoamento["ds_pontuacao_minima"] ?>"
                                <?= $checked ?>
                            >
                            <label for="aperfeicoamento<?= $idAperfeicoamento ?>">
                                <?= $aperfeicoamento["ds_nome_curso"] ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <!-- CASO FUTURAMENTE TENHA INPUT -->
                    <?php if($aperfeicoamento["ds_tipo_campo"] == "INPUT"): ?>
                        <div class=" br-input mt-3">
                            <label for="aperfeicoamento<?= $idAperfeicoamento ?>" class="text-normal">
                                <?= $aperfeicoamento["ds_nome_curso"] ?>
                            </label>
                            <input 
                                type="number"
                                min="0"
                                id="aperfeicoamento<?= $idAperfeicoamento ?>"
                                name="aperfeicoamento<?= $idAperfeicoamento ?>"
                                value="<?= old("aperfeicoamento".$idAperfeicoamento, $quantidade) ?>"
                                class="mt-2 col-sm-12 col-lg-4"
                            >
                        </div>
						<span class="br-message info pdd-10"><div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>Informe o total de cursos de aperfeiçoamentos que você possui de acordo com as áreas citadas acima</span>
                    <?php endif; ?>

                <?php endforeach; ?>

            </div>
        </fieldset>
    </div>
	</div>
<?php
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
