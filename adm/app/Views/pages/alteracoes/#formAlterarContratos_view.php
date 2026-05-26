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
							<form id="formCursos" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Contratos/alterar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
								<input type="hidden" name="pk_id_contrato" value="<?= esc(set_value('pk_id_contrato', $contrato->pk_id_contrato)) ?>">
                                <input type="hidden" name="fk_id_candidato" value="<?= esc(set_value('fk_id_candidato', $candidato->pk_id_candidato)) ?>">
                                <input type="hidden" name="fk_id_edital" value="<?= esc(set_value('fk_id_edital', $edital ?? '')) ?>">
                                <input type="hidden" name="fk_id_cursoAntigo"  value="<?= esc(set_value('fk_id_cursoAntigo', $curso ?? '')) ?>">
								<input type="hidden" name="fk_id_setorAntigo"  value="<?= esc(set_value('fk_id_setorAntigo', $setor ?? '')) ?>">

                                
								<div class="form-group row">
									<label for="input-termo" class="col-form-label col-sm-3">Número do Termo</label>
									<?php $termo = $contrato->ds_numero_termo."/".$contrato->ds_ano_termo;?>
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
									<label for="input-nome" class="col-form-label col-sm-3">Nome do Candidato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_candidato'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-candidato" 
                                            name="ds_nome_candidato" 
                                            value="<?php echo set_value('ds_nome_candidato',$candidato->ds_nome);?>" 
                                            readonly
                                        >
										<?php if(isset($error['ds_nome_candidato'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_candidato'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                               <div class="form-group row">
									<label for="select-curso" class="col-form-label col-sm-3">Curso</label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['fk_id_curso'] ?? ''?></span>
										<select id="select-curso" class="form-control" name="fk_id_curso" required >
											<option value="<?php echo $curso?>" selected><?php echo $cursoNome?></option>
											<?php foreach($cursosEdital as $valueCurso): ?>
												<option value="<?php echo $valueCurso->pk_id_curso?>" <?php if(old('fk_id_curso')==$valueCurso->pk_id_curso) echo('selected');?>><?php echo $valueCurso->ds_nome_curso?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-select-setor" class="col-form-label col-sm-3">Setor* </label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['fk_id_setor'] ?? ''?></span>
										<select id="input-select-setor" class="form-control" name="fk_id_setor" required >
											<option value="<?php echo $setor?>" selected><?php echo $setorNome?></option>
											<?php foreach($setoresComVagas as $setor): ?>
												<option value="<?php echo $setor->pk_id_setor?>" <?php if(old('fk_id_setor')==$setor->pk_id_setor) echo('selected');?>><?php echo $setor->ds_nome_setor?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-inicio-contrato" class="col-form-label col-sm-3">Início do Contrato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_ingresso'] ?? ''?></span> 
										<?php 
										list($ano, $mes, $dia) = explode("-", $contrato->ds_data_ingresso);
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
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-3">Término do Contrato</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_data_encerramento'] ?? ''?></span> 
                                        <?php 
										list($ano, $mes, $dia) = explode("-", $contrato->ds_data_encerramento);
										$dataEncerramento = $dia."/".$mes."/".$ano;
										?>
										<input 
                                            type="text" 
                                            class="form-control input-data data-final" 
                                            id="input-inicio-contrato" 
                                            name="ds_data_encerramento" 
                                            value="<?php echo set_value('ds_data_encerramento',$dataEncerramento);?>" 
                                            required
                                            placeholder="Data de encerramento do Contrato"
                                        >
                                    </div>
                                </div>
								<div class="form-group row">
									<label for="input-alterar-supervisor" class="col-form-label col-sm-3">Supervisor do Estágio</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_supervisor'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-alterar-supervisor" 
                                            name="ds_supervisor" 
                                            value="<?php echo set_value('ds_supervisor',$contrato->ds_supervisor);?>" 
                                            required
                                            placeholder="Supervisor do Estágio"
                                        >
                                    </div>
                                </div>
								<div class="form-group row">
									<label for="input-cargo-supervisor" class="col-form-label col-sm-3">Cargo do Supervisor</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_cargo_supervisor'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-cargo-supervisor" 
                                            name="ds_cargo_supervisor" 
                                            value="<?php echo set_value('ds_cargo_supervisor',$contrato->ds_cargo_supervisor);?>" 
                                            required
                                            placeholder="Cargo do Supervisor"
                                        >
                                    </div>
                                </div>
								<div class="form-group row">
									<label for="input-faculdade" class="col-form-label col-sm-3">Faculdade</label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['fk_id_instituicao'] ?? ''?></span>
										<select id="select-instituicao" class="form-control" name="fk_id_instituicao" required >
											<option value="<?php echo $instituicao?>" selected><?php echo $instituicaoNome?></option>
											<?php foreach($instituicoes as $instituicao): ?>
												<option value="<?php echo $instituicao->pk_id_instituicao?>" <?php if(old('fk_id_instituicao')==$instituicao->pk_id_instituicao) echo('selected');?>><?php echo $instituicao->ds_nome?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								
								<div class="form-group row">
									<label for="input-nome-orientador" class="col-form-label col-sm-3">Orientador do Curso (faculdade)</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_orientador'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-orientador" 
                                            name="ds_orientador" 
                                            value="<?php echo set_value('ds_orientador',$contrato->ds_orientador);?>" 
                                            required
                                            placeholder="Orientador do Curso na faculdade"
                                        >
                                    </div>
                                </div>

								<div class="form-group row">
									<label for="input-nome-orientador" class="col-form-label col-sm-3">Representante da Faculdade</label>
									<div class="col-sm-9">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_representante_faculdade'] ?? ''?></span> 
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="input-nome-orientador" 
                                            name="ds_representante_faculdade" 
                                            value="<?php echo set_value('ds_representante_faculdade',$contrato->ds_representante_faculdade);?>" 
                                            required
                                            placeholder="Representante da faculdade"
                                        >
                                    </div>
                                </div>

								<div class="form-group row">
									<label for="input-alterar-carga-horaria" class="col-form-label col-sm-3">Carga horária diária</label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['pk_id_auxilio'] ?? ''?></span>
										<select id="input-alterar-carga-horaria" class="form-control" name="fk_id_auxilio" required >
											<option value="<?php echo $auxilio?>" selected><?php echo $auxilioNome." horas"?></option>
											<?php foreach($auxilios as $auxilio): ?>
												<option value="<?php echo $auxilio->pk_id_auxilio?>" <?php if(old('pk_id_auxilio')==$auxilio->pk_id_auxilio) echo('selected');?>><?php echo $auxilio->ds_horas_diarias." horas"?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-ds_turno" class="col-form-label col-sm-3">Turno</label>
									<div class="col-sm-9">
										<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_turno'] ?? ''?></span>
										<?php
										if($contrato->ds_turno == 1){
											$turno = "Manhã";
										}else if($contrato->ds_turno == 2){
											$turno = "Tarde";
										}else{
											$turno = "Manhã/Tarde";
										}
										?>
										
										<select id="input-ds_turno" class="form-control" name="ds_turno" required >
											<option value="<?php echo $contrato->ds_turno?>" selected><?php echo $turno ?></option>
											<option value= "1" <?php if(old('ds_turno')=="1") echo('selected');?>>Manhã</option>
											<option value= "2" <?php if(old('ds_turno')=="2") echo('selected');?>>Tarde</option>
											<option value= "3" <?php if(old('ds_turno')=="3") echo('selected');?>>Manhã/Tarde</option>
										</select>
									</div>
								</div>


                                <p>*Setores que possuem vagas disponíveis para o curso escolhido. Caso não apareça o nome do setor, verificar se o mesmo possui vagas.</p>
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
