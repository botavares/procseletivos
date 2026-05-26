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
						<div class="card-body col-md-10 col-sm-12 centrado">
                            <form method="POST" action="<?php echo base_url('Manifestacoes/registrar')?>" enctype="multipart/form-data">
					            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <input type="hidden" name="canais-alternativos" value="true">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="pk_id_manifesto" value="<?php echo $dados->pk_id_manifesto;?>">
                                
                                <input type="hidden" name="ds_protocolo" value="<?php echo $dados->ds_protocolo;?>">
                                
					            <div class="form-group col-sm-12">
						            <label for="">A manifestação será anônima?</label>
						            <div id="" class="custom-control custom-radio">
							            <input class="custom-control-input" type="radio" id="sim-anonimo" name="ds_anonimo" value="0"
                                        <?php echo  ($dados->ds_anonimo == 0) ? 'checked' : ''; ?>  required>
							            <label for="sim-anonimo" class="custom-control-label">Sim</label>
						            </div>
                                    <div id="" class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="nao-anonimo" name="ds_anonimo" value="1"
                                        <?php echo  ($dados->ds_anonimo == 1) ? 'checked' : ''; ?>  required>
                                        <label for="nao-anonimo" class="custom-control-label">Não</label>
                                    </div>
					            </div>

                                <div class="identificacao mrg-bottom-30">
                                    <div class="form-group col-sm-12">
                                        <label for="">Nome Completo</label>
                                        <input type="text" class="form-control" id="input-nome" name="ds_nome" 
                                        placeholder="Nome Completo" value="<?php echo set_value('ds_nome', $dados->ds_nome);?>">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="">E-mail</label>
                                        <input type="email" class="form-control" id="input-email" name="ds_email" 
                                        placeholder="E-mail" value="<?php echo set_value('ds_email', $dados->ds_email);?>">
                                    </div>
                                </div>
                                <hr>
							    <div class="form-group col-md-12">
                                    <label for="">Canal de Utilizado</label>
						            <div class="form-group col-md-12 row">
							           
                                        <?php
                                            foreach($canais as $valueCanais){
                                                if( $valueCanais->pk_id_canal == $dados->fk_id_canal){
                                                    $checked = 'checked';
                                                }else{
                                                    $checked = '';
                                                }
                                        ?>
                                            <div id="<?php echo 'input-canais'.$valueCanais->pk_id_canal?>"  class="col-md-3 col-sm-12 custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" 
                                                id="<?php echo $valueCanais->ds_canal?>" 
                                                name="fk_id_canal" 
                                                value="<?php echo $valueCanais->pk_id_canal?>"
                                                <?php echo $checked; ?>
                                                required
                                                >
                                                <label for="<?php echo $valueCanais->ds_canal?>" class="custom-control-label">
                                                    <?php echo $valueCanais->ds_canal ?>
                                                </label>
                                            </div>
			        				    <?php }?>
                                    </div>
						        </div>
                                <hr>
                                <div class="form-group col-sm-12">
                                    <label for="">Tipo de Manifestação</label>
                                    <div class="form-group col-md-12 row">
                                        <?php
                                            foreach($tiposManifestos as $valueManifestos){
                                                if( $valueManifestos->pk_id_manifestacao == $dados->ds_tipo_manifesto){
                                                    $checked = 'checked';
                                                }else{
                                                    $checked = '';
                                                }
                                        ?>
                                        
                                        <div id="" class="col-md-3 col-sm-12 custom-control custom-radio">
                                            <input class="custom-control-input" 
                                            type="radio" 
                                            id="<?php echo $valueManifestos->ds_manifestacao?>" 
                                            name="ds_tipo_manifesto" 
                                            value="<?php echo $valueManifestos->pk_id_manifestacao?>"
                                            <?php echo $checked; ?>
                                            required>
                                            <label for="<?php echo $valueManifestos->ds_manifestacao?>" class="custom-control-label"><?php echo $valueManifestos->ds_manifestacao?></label>
                                        </div>
                                    <?php }?>
                                </div>
					        </div>
                            <hr>
                            <div class="form-group">
                                <label for="">Setor Referente</label>
                                <select id="input-setor" class="form-control select-setor" name="fk_id_setor" required >
                                    <option value="<?php echo $setor->pk_id_setor?>" selected><?php echo $setor->ds_nome_setor?></option>
                                    <?php foreach($setores as $valueSetores){?>
                                        <option value="<?php echo $valueSetores->pk_id_setor?>" <?php if(old('fk_id_setor')==$valueSetores->pk_id_setor) echo('selected');?>><?php echo $valueSetores->ds_nome_setor?></option>
                                    <?php }?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Selecione o assunto</label>
                                <select id="select-assuntos-list" class="select-assunto form-control" name="fk_id_assunto" required>
                                    <option value="<?php echo $assunto->pk_id_assunto?>" selected><?php echo $assunto->ds_assunto?></option>
                                    <option id="assunto0" value="">Selecione um assunto</option>
                                </select>
                            </div>
	

                            <div class="form-group">
                                <label for="ds_descricao">Descricão</label>
                                <textarea class="form-control" id="ds_descricao" name="ds_texto_manifestacao" rows="3" placeholder="Descricão" required><?php echo set_value('ds_texto_manifestacao', $dados->ds_texto_manifestacao);?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="arquivo">Anexo</label>
                                <input class="upload-input" id="multiple-files" name="ds_arquivos[]" type="file" aria-hidden="null" aria-label="enviar arquivo" multiple/>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="<?php echo base_url("Dashboard")?>" class="btn btn-warning">Voltar</a>
                            </div>
				        </form>
                    </div>
    			</div>
			</div>
		</div>		
	</section>
</div>
                                