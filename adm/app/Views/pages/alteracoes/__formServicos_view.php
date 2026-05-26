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
							<form id="formServicos" class="formAdmin form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Servicos/registrar"?>"  enctype="multipart/form-data">

								<input type="hidden" name="action" value="update">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                
								<input type="hidden" name="pk_id_servico" value="<?php echo $dados->pk_id_servico?>">
								<div class="form-group row">
									<label for="input-nome" class="col-form-label col-sm-2">Título do Serviço</label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_nome_servico'] ?? ''?></span> 
                                        <input type="text" class="form-control" id="input-nome-servico" name="ds_nome_servico" value="<?php echo set_value('ds_nome_servico',$dados->ds_nome_servico);?>" required >
										<?php if(isset($error['ds_nome_servico'])):?>
											<span class="text text-danger"><?php echo $error['ds_nome_servico'] ?? ''?></span>
										<?php endif ?>
									</div>
								</div>
                                <div class="form-group row">
									<label for="input-classes" class="col-form-label col-sm-2">Classe referência</label>
									<div class="col-sm-10">
										
                                    	<select id="input-classes" class="form-control" name="fk_id_classe" required >
										<?php
										
										switch($dados->fk_id_classe){
												case 1:
													echo '<option value="1" selected>Cidadão</option>';
													break;
												case 2:
													echo '<option value="2" selected>Empresa</option>';
													break;
												case 3:
													echo '<option value="3" selected>Servidor</option>';
													break;
												case 4:
													echo '<option value="4" selected>Global</option>';
													break;
											}
									?>
											<?php foreach($classes as $classe): ?>
												<option value="<?php echo $classe->pk_id_classe ?>" <?php if(old('fk_id_classe')==$classe->pk_id_classe) echo('selected');?>><?php echo $classe->ds_nome_classe ?></option>
											<?php endforeach ?>
											
										</select>
									</div>
								</div>

								<hr>
								<div class="form-group row">
									<label for="input-categorias" class="col-form-label col-sm-2">Categoria</label>
									<div class="col-sm-10">
                                    	<select id="input-categorias" class="form-control" name="fk_id_categoria" required >
										<option value="<?php echo $categoriasId->pk_id_categoria ?>"><?php echo $categoriasId->ds_nome_categoria ?></option>
											<?php foreach($categorias as $categoria): ?>
												<option value="<?php echo $categoria->pk_id_categoria ?>" <?php if(old('fk_id_categoria')==$categoria->pk_id_categoria) echo('selected');?>><?php echo $categoria->ds_nome_categoria ?></option>
											<?php endforeach ?>
											
										</select>
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group">
										<label for="input-corCategoria0" class="col-form-label col-sm-2">Cidadão será direcionado para:</label>
										<div class="col-lg-10">
											<div class="form-group col-lg-12">
												<div class="custom-check">
													<?php
													if($dados->ds_tipo_servico == 1){
														$checkedArquivo = "";
														$checkedSite = "checked";
													}else if( $dados->ds_tipo_servico == 2){
														$checkedArquivo = "checked";
														$checkedSite = "";
													}else{
														$checkedArquivo = "";
														$checkedSite = "";
													};	
													?>
													<label>
														<input type="radio"
															id="input-direciona-site"
															class="diaSemana"
                                                            name="ds_tipo_servico"
															value="1" <?php echo old('ds_tipo_servico'), $checkedSite?>
														>
                                                        <span style="margin-left:5px; margin-right:5px;">
															Um serviço através de um link definido (URL);
														</span>
													</label>
													</br>
                                                    <label>
														<input type="radio"
															id="input-direciona-arquivo"
															class="diaSemana"
                                                            name="ds_tipo_servico"
															value="2" <?php echo old('ds_tipo_servico') ,$checkedArquivo?>
														>
                                                        <span style="margin-left:5px; margin-right:5px;">
														Fazer download de um arquivo;
														</span>
													</label>
                                                    <!---->
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="boxlink">	
									<div class="form-group row">
										<label for="input-link-servico" class="col-form-label col-sm-2">Link para acesso</label>
										<div class="col-sm-10">
											<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_link_servico'] ?? ''?></span> 
											<input type="text" class="form-control" id="input-link-servico" name="ds_link_servico" value="<?php echo set_value('ds_link_servico',$dados->ds_link_servico);?>" >
											<?php if(isset($error['ds_link_servico'])):?>
												<span class="text text-danger"><?php echo $error['ds_link_servico'] ?? ''?></span>
											<?php endif ?>
										</div>
									</div>
								</div>



								<div class="boxarquivo">
									<div  class="form-group row">
										<label for="input-arquivo-servico" class="col-form-label col-sm-2">Arquivo para Download</label>
										<div class="col-sm-10">
											<?php
												if($nomeArquivo!='#'){
											?>
												<label class="gray">Arquivo Atual: <a class="blue" href="<?php echo $linkArquivo; ?>" target="_blank"><?php echo urldecode($nomeArquivo); ?></a></label><br>
											<?php
												}
											?>
											
											
											<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_arquivo_servico'] ?? ''?></span> 
											<input type="file" name="ds_arquivo_servico" />
											<?php if(isset($error['ds_arquivo_servico'])):?>
												<span class="text text-danger"><?php echo $error['ds_arquivo_servico'] ?? ''?></span>
											<?php endif ?>
										</div>
									</div>
								</div>



								<div class="form-group row">
									<label for="input-status" class="col-form-label col-sm-2">Possui instruções?</label>
									<div class="col-sm-10">
                                    	<select id="input-status" class="form-control" name="ds_possui_instrucao" required >
											<?php
												if($dados->ds_possui_instrucao == 0){
											?>
											<option value="0" <?php if(old('ds_possui_instrucao')==0) echo('selected');?>>NÃO</option>
											<?php
												}else{
											?>
											<option value="1" <?php if(old('ds_possui_instrucao')==1) echo('selected');?>>SIM</option>
											<?php
												 }
											?>
											<option value="0" <?php if(old('ds_possui_instrucao')==0)?>>NÃO</option>
											<option value="1" <?php if(old('ds_possui_instrucao')==1)?>>SIM</option>
											
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-descricao-servico" class="col-form-label col-sm-2">Descrição do serviço<br><small>Max. 130 caracteres</small></label>
									<div class="col-sm-10">
                                    	<span class="text text-danger"><?php echo session()->getFlashdata('errors')['ds_descricao_servico'] ?? ''?></span> 
										<textarea type="text" class="form-control" maxlength="130" id="input-link-servico" name="ds_descricao_servico" required ><?php echo set_value('ds_descricao_servico',$dados->ds_descricao_servico);?></textarea>
										<?php if(isset($error['ds_descricao_servico'])):?>
											<span class="text text-danger"><?php echo $error['ds_descricao_servico'] ?? ''?></span>
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
														echo '<option value="1" selected>ATIVA</option>';
														break;
													case 2:
														echo '<option value="2" selected>INATIVA</option>';
														break;
													case 3:
														echo '<option value="3" selected>PARALIZADA</option>';
														break;
												}
											?>
											<option value="1" <?php if(old('ds_status')==1) echo('selected'); ($dados->ds_status == 1) ? 'selected' : ''; ?>>ATIVA</option>
											<option value="2" <?php if(old('ds_status')==2) echo('selected'); ($dados->ds_status == 2) ? 'selected' : '';?>>INATIVA</option>
											<option value="3" <?php if(old('ds_status')==3) echo('selected'); ($dados->ds_status == 3) ? 'selected' : '';?>>PARALIZADA</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="input-destaque" class="col-form-label col-sm-2">Colocar como destaque</label>
									<div class="col-sm-10">

                                    	<select id="input-destaque" class="form-control" name="ds_destaque" required >
											<?php
												switch($dados->ds_destaque){
													case 1:
														echo '<option value="1" selected>SIM</option>';
														break;
													case 0:
														echo '<option value="0" selected>NÃO</option>';
														break;
												}
											?>

											<option value="1" <?php if(old('ds_destaque')==1) ?>>SIM</option>
											<option value="0" <?php if(old('ds_destaque')==0) ?>>NÃO</option>
										</select>

									</div>
								</div>

								<div class="form-group row">
									<label for="input-fixo_topo" class="col-form-label col-sm-2">Colocar fixo no topo</label>
									<div class="col-sm-10">

                                    	<select id="input_fixo_topo" class="form-control" name="ds_fixo_topo" required >
											<?php
												switch($dados->ds_fixo_topo){
													case 1:
														echo '<option value="1" selected>SIM</option>';
														break;
													case 0:
														echo '<option value="0" selected>NÃO</option>';
														break;
												}
											?>

											<option value="1" <?php if(old('ds_fixo_topo')==1) ?>>SIM</option>
											<option value="0" <?php if(old('ds_fixo_topo')==0) ?>>NÃO</option>
										</select>

									</div>
								</div>


							</div>
							<div class="card-footer">
								<div class="form-group">
									<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1">
										<a href="<?php echo base_url("Servicos")?>" type="button" class="btn btn-warning" data-dismiss="modal">Voltar</a>
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