<!--<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>-->
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
						<div class="card-body">
							<form id="formInstrucoes" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Instrucoes/registrar"?>">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                
                                <div class="form-group row">
									<label for="input-classes" class="col-form-label col-sm-2"></label>
									<div class="col-sm-10">
										<label style="font-size:28px; text-align:center;color:#007bff"><?php echo $servicos->ds_nome_servico ?></label>
										<input type="hidden" class="form-control" id="input-idservico" name="fk_id_servico" value="<?php echo $servicos->pk_id_servico;?>">
										<input type="hidden" class="form-control" id="input-idinstrucao" name="pk_id_instrucao" value="<?php echo $dados->pk_id_instrucao;?>">
									</div>
								</div>
								<?php
									
								?>
									 <div class="form-group row">
									<label for="input-grupo" class="col-form-label col-sm-2">Grupo de solicitações</label>
									<div class="col-sm-10">
                                    	<select id="input-grupo" class="form-control" name="fk_id_gruposolicitacao" required >
											<?php if($solicitacao == ""){?>
												<option></option>
											<?php
												}else{?>
													<option value="<?php echo $solicitacao->pk_id_gruposolicitacao?>"><?php echo $solicitacao->ds_nome_grupo_solicitacao?></option>
											<?php
													 }
											?>
													<?php foreach($grupos as $valueGrupos){?>
															<option value="<?php echo $valueGrupos->pk_id_gruposolicitacao?>" <?php if(old('fk_id_gruposolicitacao')==1);?>><?php echo $valueGrupos->ds_nome_grupo_solicitacao?></option>
														<?php
													  	}
													 
													?>
										</select>
									</div>
								</div>
								

									<div class="form-group row">
										<label for="ds_definicao" class="col-form-label col-sm-2">Definição</label>
										<div class="col-sm-10">
                                    		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_definicao'] ?? ''?></span> 
                                        	<div id="saida">
												<textarea name="ds_definicao" id="ds_definicao"><?php echo $dados->ds_definicao ?></textarea>
													
													<script>
														CKEDITOR.replace( 'ds_definicao' );
													</script>
											</div>
											<?php if(isset($error['ds_definicao'])):?>
												<span class="text text-danger"><?php echo $error['ds_definicao'] ?? ''?></span>
											<?php endif ?>
										</div>
									</div>




									<div class="form-group row">
										<label for="ds_entrada_protocolo" class="col-form-label col-sm-2">Documentações</label>
										<div class="col-sm-10">
                                    		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_entrada_protocolo'] ?? ''?></span> 
                                        	<div id="saida">
												<textarea name="ds_entrada_protocolo" id="ds_entrada_protocolo"><?php echo $dados->ds_entrada_protocolo ?></textarea>
													
													<script>
														CKEDITOR.replace( 'ds_entrada_protocolo' );
													</script>
											</div>
											<?php if(isset($error['ds_entrada_protocolo'])):?>
												<span class="text text-danger"><?php echo $error['ds_entrada_protocolo'] ?? ''?></span>
											<?php endif ?>
										</div>
									</div>
								
									<div class="form-group row">
										<label for="ds_saida_protocolo" class="col-form-label col-sm-2">Legislação</label>
										<div class="col-sm-10">
                                    		<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_saida_protocolo'] ?? ''?></span> 
                                        	<div id="saida">
												<textarea name="ds_saida_protocolo" id="ds_saida_protocolo"><?php echo $dados->ds_saida_protocolo ?></textarea>
													
													<script>
														CKEDITOR.replace( 'ds_saida_protocolo' );
													</script>
											</div>
											<?php if(isset($error['ds_saida_protocolo'])):?>
												<span class="text text-danger"><?php echo $error['ds_saida_protocolo'] ?? ''?></span>
											<?php endif ?>
										</div>
											</div>
								<hr>
								<div class="form-group row">
									<label for="input-perguntas" class="col-form-label col-sm-2">perguntas Frequentes</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_perguntas_frequentes'] ?? ''?></span> 
                                        <div id="perguntas">
											<textarea name="ds_perguntas_frequentes" id="ds_perguntas_frequentes"><?php echo $dados->ds_perguntas_frequentes ?></textarea>
												
												<script>
													CKEDITOR.replace( 'ds_perguntas_frequentes' );
												</script>
										</div>
										<?php if(isset($error['ds_perguntas_frequentes'])):?>
										<span class="text text-danger"><?php echo $error['ds_perguntas_frequentes'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Prazo:</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_prazo'] ?? ''?></span> 
										<input type="text" class="form-control" id="input-prazo" name="ds_prazo" value="<?php echo set_value('ds_prazo',$dados->ds_prazo);?>" required >
										<?php if(isset($error['ds_prazo'])):?>
											<span class="text text-danger"><?php echo $error['ds_prazo'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								
								
								


								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Taxas:</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_taxas'] ?? ''?></span> 
                                        <div id="responsavel">
											<textarea name="ds_taxas" id="ds_taxas"><?php echo $dados->ds_taxas ?></textarea>
												
												<script>
													CKEDITOR.replace( 'ds_taxas' );
												</script>
										</div>
										<?php if(isset($error['ds_taxas'])):?>
											<span class="text text-danger"><?php echo $error['ds_taxas'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Responsável:<small><span class="text text-danger"> Pessoa ou setor</span></small></label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_responsavel'] ?? ''?></span> 
                                        <div id="responsavel">
											<textarea name="ds_responsavel" id="ds_responsavel"><?php echo $dados->ds_responsavel ?></textarea>
												
												<script>
													CKEDITOR.replace( 'ds_responsavel' );
												</script>
										</div>
										<?php if(isset($error['ds_responsavel'])):?>
											<span class="text text-danger"><?php echo $error['ds_responsavel'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Contato:</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_contato'] ?? ''?></span> 
                                         <div id="responsavel">
											<textarea name="ds_contato" id="ds_contato"><?php echo $dados->ds_contato ?></textarea>
												
												<script>
													CKEDITOR.editorConfig = function( config ) {
														// Plugins extras
														config.extraPlugins = 'pastefromword';
														
													}

													CKEDITOR.replace( 'ds_contato' );

												</script>
										</div>
										<?php if(isset($error['ds_contato'])):?>
											<span class="text text-danger"><?php echo $error['ds_contato'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>

								
								<hr>
								<div class="form-group row">
									<label for="input-status" class="col-form-label col-sm-2">Status do serviço</label>
									<div class="col-sm-10">
                                    	<select id="input-status" class="form-control" name="ds_status" required >
											<?php
												switch($dados->ds_status){
													case 1:
														$valor = 1;
														$status = "ATIVO";
														break;
													case 2:
														$valor = 2;
														$status = "INATIVO";
														break;
													case 3:
														$valor = 3;
														$status = "PARALIZADO";
														break;
												}
												?>
											<option value="<?php echo $valor ?>" <?php if(old('ds_status')==$valor) ?> ><?php  echo $status ?></option>
											<option value="1" <?php if(old('ds_status')==1) ?> >ATIVO</option>
											<option value="2" <?php if(old('ds_status')==2) ?>>INATIVO</option>
											<option value="3" <?php if(old('ds_status')==3) ?>>PARALIZADO</option>
										</select>
									</div>
								</div>
								<?php
									
									?>
							</div>
							<div class="card-footer">
								<div class="form-group">
									<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
										<a href="<?php echo base_url("Instrucoes")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
										<button type="reset" class="btn btn-primary">Limpar</button>
										<button type="submit" class="btn btn-success">Salvar</button>
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