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
						<a href="<?php echo base_url("Contratos")?>" type="button" class="btn btn-warning col-md-2 mlef20 mtop10 chanfrado" data-dismiss="modal">Voltar</a>
						<div class="card-body">
							<form id="formCursos" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Contratos/aditivarRescindir"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
								<input type="hidden" name="pk_id_contrato" value="<?= esc(set_value('pk_id_contrato', $dadosContrato->pk_id_contrato)) ?>">
                                <input type="hidden" name="fk_id_candidato" value="<?= esc(set_value('fk_id_candidato', $contratado->pk_id_candidato)) ?>">


                                
								<div class="form-group row">
									<label for="input-termo" class="col-form-label col-sm-3">Número do Termo</label>
									<?php $termo = $dadosContrato->ds_numero_termo."/".$dadosContrato->ds_ano_termo;?>
									<input 
												type="hidden" 
												name="ds_numero_termo" 
												value="<?php echo set_value('ds_numero_termo',$dadosContrato->ds_numero_termo ?? '');?>"
											>
											<input 
												type="hidden"
												name = "ds_ano_termo"
												value = "<?php echo set_value('ds_ano_termo',$dadosContrato->ds_ano_termo ?? '');?>"
											>
									<div class="col-sm-9">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-termo" 
                                            name="numero_termo" 
                                            value="<?php echo set_value('numero_termo',$termo);?>" 
                                            readonly
                                        >
										<?php if(isset($error['ds_nome_candidato'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_candidato'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-nome-candidato" class="col-form-label col-sm-3">Nome do Candidato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_candidato'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-candidato" 
                                            name="ds_nome_candidato" 
                                            value="<?php echo set_value('ds_nome_candidato',$contratado->ds_nome);?>" 
                                            readonly
                                        >
										<?php if(isset($error['ds_nome_candidato'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_candidato'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-nome-candidato" class="col-form-label col-sm-3">Curso do Candidato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_candidato'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-curso" 
                                            name="ds_nome_curso" 
                                            value="<?php echo set_value('ds_nome_curso',$nomeCurso);?>" 
                                            readonly
                                        >
										<?php if(isset($error['ds_nome_candidato'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_candidato'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                              
								<div class="form-group row">
									<label for="input-nome-setor" class="col-form-label col-sm-3">Setor </label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_setor'] ?? ''?></span>
										 <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-setor" 
                                            name="ds_nome_setor" 
                                            value="<?php echo set_value('ds_nome_candidato',$nomeSetor);?>" 
                                            readonly
                                        >
										

									</div>
								</div>
                                <div class="form-group row">
									<label for="input-inicio-contrato" class="col-form-label col-sm-3">Início do Contrato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_ingresso'] ?? ''?></span> 
										<?php 
										list($ano, $mes, $dia) = explode("-", $dadosContrato->ds_data_ingresso);
										$dataIngresso = $dia."/".$mes."/".$ano;
										?>
                                        <input 
                                            type="text" 
                                            class="form-control input-data data-inicio" 
                                            id="input-inicio-contrato" 
                                            name="ds_data_ingresso" 
                                            value="<?php echo set_value('ds_data_ingresso',$dataIngresso);?>" 
                                            required
                                            placeholder="Data de Início do Contrato"
											readonly
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
									<label for="input-encerra-contrato" class="col-form-label col-sm-3">Término do Contrato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_encerramento'] ?? ''?></span> 
                                        <?php 
										list($ano, $mes, $dia) = explode("-", $dadosContrato->ds_data_encerramento);
										$dataEncerramento = $dia."/".$mes."/".$ano;
										?>
										<input 
                                            type="text" 
                                            class="form-control input-data data-final" 
                                            id="input-encerra-contrato" 
                                            name="ds_data_encerramento" 
                                            value="<?php echo set_value('ds_data_encerramento',$dataEncerramento);?>" 
                                            required
                                            placeholder="Data de encerramento do Contrato"
											readonly
                                        >
                                    </div>
                                </div>
								<div class="form-group row">
                                    <label for="modo-prorrogar" class="col-form-label col-sm-3">Escolha Opção</label>

									<div class="col-sm-2 custom-control custom-radio">
							            <input class="custom-control-input" type="radio" id="modo-prorrogar" name="ds_modo" value="1"<?php echo  set_radio('ds_modo', '1'); ?>  required>
							            <label for="modo-prorrogar" class="custom-control-label">Aditivo</label>
						            </div>

                                    <div id="" class="col-sm-2 custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="modo-encerrar" name="ds_modo" value="0"<?php echo  set_radio('ds_modo', '0');  ?>checked required>
                                        <label for="modo-encerrar" class="custom-control-label">Encerrar</label>
                                    </div>
					            </div>

								<!-- PRORROGAR -->
							<div id="box-prorrogar">
								<div class="form-group row">
									<label for="input-data-aditivo" class="col-form-label col-sm-3">Data do Aditivo</label>
									<!-- Fazer com que o sistema alerta que a nova data do sistema extrapola 730 dias ou dois anos-->
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_prorrogacao'] ?? ''?></span> 
										<input 
											type="text" 
											class="form-control input-data data-inicio" 
											id="input-data-aditivo" 
											name="ds_data_prorrogacao" 
											value="<?php echo set_value('ds_data_prorrogacao',$dadosContrato->ds_data_prorrogacao ?? '');?>" 
											
											placeholder="Data de Início do Contrato"
										>
									</div>
								</div>

								<input type="hidden" name="ds_data_aditivo">

								<!-- checar se vai ter alteração de carga horária -->
								<div class="form-group row">
									<label for="chk-carga-horaria" class="col-form-label col-sm-3"></label>
									<div class="custom-check col-sm-9">
										<label>
											<input 
												type="checkbox" 
												id="chk-carga-horaria" 
												class="cargahoraria-checkbox" 
												name="chek-carga-horaria"
												value="1" 
												<?php echo set_radio('chek-carga-horaria', '1')?>
											><span style="margin-left:5px;">Irá mudar Carga Horária</span>
										</label>
									</div>
								</div>
								<div class="form-group  box-carga-horaria row">
									<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_carga_horaria'] ?? ''?></span> 
									<label for="input-carga-horaria" class="col-form-label col-sm-3">Horas diárias</label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_carga_horaria'] ?? ''?></span> 
										<select name="ds_carga_horaria" class="form-control" id="input-carga-horaria">
											<option value="" selected>Selecione</option>
												<?php foreach ($dadosAuxilio as $cargaHoraria):?>
													<option value="<?php echo $cargaHoraria->pk_id_auxilio?>" <?php if(old('ds_carga_horaria')==$cargaHoraria->pk_id_auxilio) echo('selected');?>><?php echo $cargaHoraria->ds_horas_diarias." horas diarias";?></option>
												<?php endforeach;?>
										</select>
									</div>
								</div>
								<!-- checar se vai ter alteração de supervisor -->
								<div class="form-group row">
									<label for="input-supervisor" class="col-form-label col-sm-3"></label>
									<div class="custom-check col-sm-9">
										<label>
											<input 
												type="checkbox" 
												id="chk-supervisor" 
												class="supervisor-checkbox" 
												name="chk_supervisor"
												value="1" 
												<?php echo set_radio('chk_supervisor', '1')?>
											><span style="margin-left:5px;">Irá mudar Supervisor</span>
										</label>
									</div>
	                        	</div>
								<div class="form-group  box-supervisor row">
									<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_supervisor'] ?? ''?></span> 
									<label for="input-supervisor" class="col-form-label col-sm-3">Supervisor</label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_supervisor'] ?? ''?></span> 
										<input 
												type="text" 
												class="form-control" 
												id="input-supervisor" 
												name="ds_supervisor" 
												value="<?php echo set_value('ds_supervisor',$dadosContrato->ds_supervisor ?? '');?>" 
												
												placeholder="Nome do novo Supervisor"
											>
									</div>
								</div>
								<!-- checar se vai ter alteração de setor -->
								<div class="form-group row">
									<label for="chk-setor" class="col-form-label col-sm-3"></label>
									<div class="custom-check col-sm-9">
										<label>
											<input 
												type="checkbox" 
												id="chk-setor" 
												class="setor-checkbox" 
												name="chk_setor"
												value="1" 
												<?php echo set_radio('chk_setor', '1')?>
											><span style="margin-left:5px;">Irá mudar Setor</span>
										</label>
									</div>
								</div>
								
								<div class="form-group  box-setor row">
									<span class="text text-danger"><?php echo session()->getFlashdata('errors')['fk_id_setor'] ?? ''?></span> 
									<label for="input-setor" class="col-form-label col-sm-3">Setores com vagas </label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['fk_id_setor'] ?? ''?></span> 
										<select name="fk_id_setor" class="form-control" id="input-setor">
											<option value="" selected>Selecione</option>
												<?php foreach ($vagas as $vaga):?>
													<option value="<?php echo $vaga->pk_id_setor?>" <?php if(old('fk_id_setor')==$vaga->pk_id_setor) echo('selected');?>><?php echo $vaga->ds_nome_setor;?></option>
												<?php endforeach;?>
										</select>
									</div>
								</div>

                            </div>
							
							

							<!-- ENCERRAR -->
							<div id="box-encerrar">
									<div class="form-group row">
										<label for="input-hipotese" class="col-form-label col-sm-3">Hipótese de Encerramento </label>
										<div class="col-sm-9">
											<span class="text text-danger"><?php echo session()->getFlashdata('errors')['pk_id_motivo'] ?? ''?></span>
											<select id="input-hipotese" class="form-control" name="pk_id_motivo" >
												<?php foreach($motivos as $motivo): ?>
													<option value="<?php echo $motivo->pk_id_motivo?>" <?php if(old('pk_id_motivo')==$motivo->pk_id_motivo) echo('selected');?>><?php echo $motivo->ds_descricao_motivo?></option>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label for="input-data-baixa" class="col-form-label col-sm-3">Extinção do Contrato</label>
										<div class="col-sm-9">
											<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_baixa'] ?? ''?></span> 
											<?php 
											$dataBaixa = date("d/m/Y");
											?>
											<input 
												type="text" 
												class="form-control input-data" 
												id="input-data-baixa" 
												name="ds_data_baixa" 
												value="<?php echo set_value('ds_data_baixa',$dataBaixa);?>" 
												
												placeholder="Data da extinção do Contrato"
											>
										</div>
									</div>
								</div>

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
