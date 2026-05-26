<div class="content-wrapper">
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header m5">
							<h3 class="card-title">
							    <h1 class="text-danger"><?php echo $titulo?></h1>
								<?php if(session()->has('error')):?>
									<div class="porta-mensagem-fixa alert alert-danger col-md-12">
										<ul>
											<?php foreach(session()->getFlashdata('error') as $valueError): ?>
												<li class="text text-white text-center font22"><?php echo $valueError ?></li>
											<?php endforeach ?>
										</ul>
									</div>
								<?php endif;?>
                            </h3>
							
						</div>
						<div class="card-body col-md-10 col-sm-12 centrado bg-azulfraco chanfrado">
                            <form method="POST" action="<?php echo base_url('Recursos/registrar')?>" enctype="multipart/form-data">
					            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <input type="hidden" name="recurso-aberto" value="true">
                                <input type="hidden" name="action" value="<?= $acao ?>">
                                <input type="hidden" name="pk_id_edital" value="<?= $edital ?>">
                                <input type="hidden" name="pk_id_cargo" value="<?= $cargo ?>">
                                <input type="hidden" name="pk_id_candidato" value="<?= $dadosRecursos['candidato']->pk_id_cadastrado ?>">
                                



                                <div class="identificacao mb-0">
                                    <div class="form-group col-sm-12">
                                        <label for="">Nome Completo</label></br>
                                        <span class="text-clean "><?= $dadosRecursos['candidato']->ds_nome;?></span>
                                        
                                        <input type="hidden" id="input-nome" name="ds_nome" value="<?= $dadosRecursos['candidato']->ds_nome;?>">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="">Protocolo do Recurso</label></br>
                                        <input type="text" id="input-protocolo" class="form-control col-md-2 " name="ds_protocolo"  value="<?= old('ds_protocolo');?>">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="">Data de Nascimento</label></br>
                                        <!--<span class="text-clean "><?= date('d/m/Y', strtotime($dadosRecursos['candidato']->ds_nascimento));?></span>-->
                                        <input type="text" id="input-data-nascimento" class="form-control col-md-2 input-data" name="ds_data_nascimento"  value="<?= date('d/m/Y', strtotime($dadosRecursos['candidato']->ds_nascimento));?>">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="">E-mail</label></br>
                                        <span class="text-clean "><?= $dadosRecursos['candidato']->ds_email;?></span>
                                        <input type="hidden" id="input-email" name="ds_email" value="<?= $dadosRecursos['candidato']->ds_email;?>">
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-12 row">
                                    <div class="classificatorios col-md-6 pdd0">
                                        <legend class="mb-0" for="">Experiências</legend>
                                        <div class="form-group col-md-12">
                                            <?php foreach($dadosRecursos['experiencias'] as $experiencia):?>
                                                <label><?= $experiencia->ds_nome_experiencia?> ( <?= $experiencia->ds_tipo_experiencia?>)</label></br>
                                                <input type="text" class="form-control" id="input-experiencia-<?= $experiencia->fk_id_experiencia ?>" name="ds_experiencias[<?= $experiencia->fk_id_experiencia ?>]" placeholder="E-mail" value="<?= $experiencia->ds_quantidade?>">
                                                
                                            <?php endforeach; ?>
                                        </div>
                                        <legend class="mb-0" for="">Escolaridade</legend>
                                        <div class="form-group col-md-12">
                                            <?php foreach($camposFormularios['escolaridades'] as $campoEscolaridade):?>
                                                <?php foreach($dadosRecursos['escolaridades'] as $escolaridade):?>
                                                    <?php if($campoEscolaridade->ds_tipo_campo == "CHECK" && $escolaridade->fk_id_escolaridade == $campoEscolaridade->fk_id_escolaridade):?>
                                                         <div class="form-group col-md-12 p-0">
                                                            <label><?= $escolaridade->ds_nome_escolaridade?> </label>
                                                            <select class="form-control" name="ds_escolaridades[<?= $escolaridade->fk_id_escolaridade ?>]" required>
                                                                <option value="1" <?= $escolaridade->ds_quantidade == 1 ? 'selected' : '' ?>>SIM</option>
                                                                <option value="0" <?= $escolaridade->ds_quantidade == 0 ? 'selected' : '' ?>>NÃO</option>
                                                            </select>
                                                        </div>
                                                    <?php elseif($campoEscolaridade->ds_tipo_campo == "INPUT" && $escolaridade->fk_id_escolaridade == $campoEscolaridade->fk_id_escolaridade):?>
                                                        <label><?= $escolaridade->ds_nome_escolaridade?></label>
                                                        <input type="text" class="form-control" id="input-escolaridade-<?= $escolaridade->fk_id_escolaridade ?>" name="ds_escolaridades[<?= $escolaridade->fk_id_escolaridade ?>]" placeholder="E-mail" value="<?= $escolaridade->ds_quantidade?>">

                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="classificatorios col-md-6 pdd0 ">
                                        <legend class="mb-0" for="">Cursos de Aperfeiçoamento</legend>
                                        <div class="form-group  col-md-12">
                                            <?php foreach($dadosRecursos['aperfeicoamentos'] as $aperfeicoamento):?>
                                                <label><?= $aperfeicoamento->ds_nome_curso?></label>
                                                <input type="text" class="form-control" id="input-aperfeicoamento-<?= $aperfeicoamento->fk_id_curso ?>" name="ds_aperfeicoamentos[<?= $aperfeicoamento->fk_id_curso ?>]" placeholder="E-mail" value="<?= $aperfeicoamento->ds_quantidade?>">
                                                
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <div class="card-footer">
									<div class="form-group">
										<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
                                            <a class="btn btn-warning col-md-2" href="<?php echo base_url("Candidatos/exibirDados")."/". $dadosRecursos['idEdital']."/".$dadosRecursos['idCargo']."/".$dadosRecursos['idCandidato']?>">Voltar</a>   
                                            <button type="submit" class="btn btn-success  col-md-2">Salvar</button>
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
                                