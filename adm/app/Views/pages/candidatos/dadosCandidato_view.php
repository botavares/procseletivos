<div class="content-wrapper">
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header m5">
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
								<?php endif;?>
                            </h3>
							
						</div>
						<div class="card-body col-md-10 col-sm-12 centrado">
                            <form method="POST" action="<?php echo base_url('registrar')?>" enctype="multipart/form-data">
					            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <input type="hidden" name="recurso-aberto" value="true">
                                <input type="hidden" name="action" value="create">

                                <div class="identificacao mb-0">
                                    <div class="form-group col-sm-12">
                                        <label for="">Nome Completo</label></br>
                                        <span class="text-clean "><?= $dadosCandidato['candidato']->ds_nome;?></span>
                                        
                                        <input type="hidden" id="input-nome" name="ds_nome" value="<?= $dadosCandidato['candidato']->ds_nome;?>" readonly>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="">Data de Nascimento</label></br>
                                        <span class="text-clean "><?= date('d/m/Y', strtotime($dadosCandidato['candidato']->ds_nascimento));?></span>
                                        <input type="hidden" id="input-data-nascimento" name="ds_data_nascimento"  value="<?= date('d/m/Y', strtotime($dadosCandidato['candidato']->ds_nascimento));?>" readonly>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="">E-mail</label></br>
                                        <span class="text-clean "><?= $dadosCandidato['candidato']->ds_email;?></span>
                                        <input type="hidden" id="input-email" name="ds_email" value="<?= $dadosCandidato['candidato']->ds_email;?>" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-12 row">
                                    <div class="classificatorios col-md-6 pdd0">
                                        <legend class="mb-0" for="">Experiências</legend>
                                        <div class="form-group col-md-12">
                                            <?php foreach($dadosCandidato['experiencias'] as $experiencia):?>
                                                <label><?= $experiencia->ds_nome_experiencia?> ( <?= $experiencia->ds_tipo_experiencia?>)</label></br>
                                                <input type="text" class="form-control" id="input-experiencia-<?= $experiencia->fk_id_experiencia ?>" name="ds_experiencias[<?= $experiencia->fk_id_experiencia ?>]" placeholder="E-mail" value="<?= $experiencia->ds_quantidade?>" readonly>
                                            <?php endforeach; ?>
                                        </div>
                                        <legend class="mb-0" for="">Escolaridade</legend>
                                        <div class="form-group col-md-12">
                                            <?php foreach($dadosCandidato['escolaridades'] as $escolaridade):?>
                                                <label><?= $escolaridade->ds_nome_escolaridade?></label>
                                                <input type="text" class="form-control" id="input-escolaridade-<?= $escolaridade->fk_id_escolaridade ?>" name="ds_escolaridades[<?= $escolaridade->fk_id_escolaridade ?>]" placeholder="E-mail" value="<?= $escolaridade->ds_quantidade?>" readonly>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="classificatorios col-md-6 pdd0 ">
                                        <legend class="mb-0" for="">Cursos de Aperfeiçoamento</legend>
                                        <div class="form-group  col-md-12">
                                            <?php foreach($dadosCandidato['aperfeicoamentos'] as $aperfeicoamento):?>
                                                <label><?= $aperfeicoamento->ds_nome_curso?></label>
                                                <input type="text" class="form-control" id="input-aperfeicoamento-<?= $aperfeicoamento->fk_id_curso ?>" name="ds_aperfeicoamentos[<?= $aperfeicoamento->fk_id_curso ?>]" placeholder="E-mail" value="<?= $aperfeicoamento->ds_quantidade?>" readonly>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <div class="card-footer">
									<div class="form-group">
										<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
                                            <a class="btn btn-warning col-md-2" href="<?php echo base_url("Candidatos")."/". $dadosCandidato['idEdital']."/".$dadosCandidato['idCargo']?>">Voltar</a>   
											<a class="btn btn-primary col-md-2" href="<?php echo base_url("Recursos")."/". $dadosCandidato['idEdital']."/".$dadosCandidato['idCargo']."/".$dadosCandidato['idCandidato']?>">Aplicar Recurso</a>
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
                                