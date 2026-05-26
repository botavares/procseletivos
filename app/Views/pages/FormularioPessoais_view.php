<main class="d-flex flex-fill mb-5" id="main">
	<div class="container-lg d-flex">
		<div class="col-md-12 row">
			<div class="col-md-12 mb-5 ">
				<div class="main-content pl-sm-3 mt-0" id="main-content">
					<nav class="br-breadcrumb" aria-label="Breadcrumbs">
						<ol class="crumb-list" role="list">
							<li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
							<li class="crumb"><i class="icon fas fa-chevron-right"></i><a href="<?php echo base_url("Cadastros")?>">Candidato</a>
							</li>
							<li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Dados Pessoais (Página Atual)</span>
							</li>
						</ol>
					</nav>
					<?php if (session()->has('errors')): ?>
    					<div class="alert bg-red-vivid-50 font22 branco text-center area-alertas" role="alert">
							<ul class="list-unstyled mb-0">
								<?php foreach (session('errors') as $error): ?>
									<li style="list-style: none"><?= esc($error) ?></li>
								<?php endforeach; ?>
							</ul>
    					</div>
					<?php endif; ?>
					
						<?php if ($acao == 'create'): ?>
							<div class="mrg-top-10 pdd-5 br-message info">
                    			<div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>
                    			<div class="content" aria-label="" role="alert">
									<span class="message-title">Registrando Dados Pessoais</span>
									<span class="message-body">
										<p class="saudação"><?php echo $saudacao?>, <?php echo $params['nome'].'!'?></p>
										<p class="saudação">Chegando agora por aqui? Vamos começar a criar seu cadastro pessoal!</p>
										<p class="saudação">Te lembro que, por você estar tendo acesso a esse cadastro através do GovBR, NUNCA faça o cadastro de estágio para outra pessoa e NUNCA compartilhe seu acesso GovBR para esse motivo (ou por qualquer outro).</p>
									</span>
								</div>
							</div>
						<?php else: ?>
							<div class="mrg-top-10 pdd-5 br-message info">
                    			<div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>
                    			<div class="content" aria-label="" role="alert">
									<span class="message-title">Atualizando Dados Pessoais</span>
									<span class="message-body">
										<p class="saudação"><?php echo $saudacao?>, <?php echo $params['nome'].'!'?></p>
									</span>
								</div>
							</div>
						<?php endif; ?>
					
					<div class="area-botoes mrg-top-10 mrg-bottom-10">
            			<a  class="br-button primary voltar" href="<?php echo base_url("Cadastros")?>"><i class="fas fa-arrow-left"></i> Voltar</a>
        			</div>
					<section class="bloco-formulario">
					<form id="formulario-dadosformulario" action="<?php echo base_url("Cadastros/registrarDadosPessosais")?>" method="post">
							<input type="hidden" value="<?= csrf_hash(); ?>" name="<?= csrf_token(); ?>" id="csrf">
							<input type="hidden" name="acao" value="<?php echo $acao?>">
							<input type="hidden" name="fk_id_gov" value="<?php echo set_value('fk_id_gov',$params['id']);?>">
							<input type="hidden" name="pk_id_cadastrado" value="<?php echo set_value('pk_id_cadastrado',isset($idCandidato) ? $idCandidato : "");?>">
							<input type="hidden" name="ds_data_cadastro" value="<?php echo set_value('ds_data_cadastro',isset($dados->ds_data_cadastro) ? $dados->ds_data_cadastro : "");?>">
							<input type="hidden" name="ds_hora_cadastro" value="<?php echo set_value('ds_hora_cadastro',isset($dados->ds_hora_cadastro) ? $dados->ds_hora_cadastro : "");?>">


							<div class="identificacao mrg-bottom-30">
								<fieldset class="identificacao">
									<legend class="font-18 bold mrg-0" >IDENTIFICAÇÃO</legend>
									<hr>
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-cpf" class="text-nowrap">CPF</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-cpf" type="text" class="cpf"  name = "ds_cpf" placeholder="" value="<?php echo set_value('ds_cpf',$dados->ds_cpf);?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_cpf'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-nome" class="text-nowrap">Nome</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-nome" type="text" name = "ds_nome" placeholder="Nome completo sem abreviações" value="<?php echo set_value('ds_nome',$dados->ds_nome);?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_nome'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-nascimento" class="text-nowrap">Nascimento</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-calendar" aria-hidden="true"></i>
												</div>
												<input class="input-data" id="input-nascimento" type="text" name = "ds_nascimento" placeholder="" value="<?php echo set_value('ds_nascimento',isset($dados->ds_nascimento) ? date('d/m/Y', strtotime($dados->ds_nascimento)) : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_nascimento'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<!--
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-nome-mae" class="text-nowrap">Nome da mãe</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-nome-mae" type="text" name = "ds_nome_mae" placeholder="Nome completo sem abreviações" value="<?php echo set_value('ds_nome_mae',isset($dados->ds_nome_mae) ? $dados->ds_nome_mae : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_nome_mae'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								-->
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<label for="select-deficiencia">Possui alguma deficiência?</label>
										<div class="select-container">
										<i class="fas fa-search"></i>
                                            <select id="select-deficiencia" class="select-deficiencia form-control" name="fk_id_pne" required>
												

												<?php
													if($acao == "create"){
												?>
													<option id="pne0" value="1">Não possuo</option>
												<?php 
													}else{
												?>
													<option id="pne0" value="<?php echo $deficiencia->pk_id_pne?>"><?php echo $deficiencia->ds_nome_pne?></option>
													<?php
													}
												?>

												<?php
													foreach($deficiencias as $deficiencia):
												?>
														<option class="campo" id="<?php echo "deficiencia".$deficiencia->pk_id_pne?>" value="<?php echo $deficiencia->pk_id_pne?><?php echo set_select('fk_id_pne',$deficiencia->pk_id_pne);?>"><?php echo $deficiencia->ds_nome_pne?></option>
												<?php
													endforeach;
												?>
											</select>
											<i class="fas fa-angle-down"></i>
										</div>
										<span class="red"><?php echo session()->getFlashdata('errors')['fk_id_pne'] ?? '' ?></span>
									</div>
								</fieldset>
								<fieldset class="identificacao outradeficiencia">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-outradeficiencia" class="text-nowrap">Outra deficiência</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-outradeficiencia" type="text" name = "ds_outra_pne" placeholder="nome da deficiência" value="<?php echo set_value('ds_outra_pne',isset($dados->ds_outra_pne) ? $dados->ds_outra_pne : "");?>"/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_outra_pne'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
							</div>


							<!-------- ENDERECO -------->
							
							<div class="mrg-bottom-30">
								<legend class="font-18 bold mrg-0" >ENDEREÇO</legend>
								<hr>
								<fieldset class="destinatario">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-cep" class="text-nowrap">CEP de sua residência</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input class="cep" id="input-cep" type="text" name = "ds_cep" placeholder="Cep de sua residência" value="<?php echo set_value('ds_cep',isset($dados->ds_cep) ? $dados->ds_cep : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_cep'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="destinatario">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-rua" class="text-nowrap">Rua</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-rua" type="text" name = "ds_rua" placeholder="Rua de sua residência" value="<?php echo set_value('ds_rua',isset($dados->ds_rua) ? $dados->ds_rua : "");?>"required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_rua'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="destinatario">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-numero" class="text-nowrap">Número</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-numero" type="text" name = "ds_numero" placeholder="Número de sua residência" value="<?php echo set_value('ds_numero',isset($dados->ds_numero) ? $dados->ds_numero : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_numero'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="destinatario">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-complemento" class="text-nowrap">Complemento</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-complemento" type="text" name = "ds_complemento" placeholder="Caso possua complemento (apartamento, lote, etc.)" value="<?php echo set_value('ds_complemento',isset($dados->ds_complemento) ? $dados->ds_complemento : "");?>"/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_complemento'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="destinatario">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-bairro" class="text-nowrap">Bairro do seu endereço</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-bairro" type="text" name = "ds_nome_bairro" placeholder="Bairro de sua residência" value="<?php echo set_value('ds_nome_bairro',isset($dados->ds_nome_bairro) ? $dados->ds_nome_bairro : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_nome_bairro'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="destinatario">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-cidade" class="text-nowrap">Cidade</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-edit" aria-hidden="true"></i>
												</div>
												<input id="input-cidade" type="text" name = "ds_cidade" placeholder="Cidade de residência" value="<?php echo set_value('ds_cidade',isset($dados->ds_cidade) ? $dados->ds_cidade : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_cidade'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<label for="select-estado">Estado</label>
										<div class="select-container">
										<i class="fas fa-search"></i>
										<select id="select-estado" class="select-estado form-control" name="ds_uf" required>
										<?php
													if($acao == "create"){
												?>
													<option id="uf0" value="">Selecione um Estado</option>
												<?php 
													}else{
												?>
													<option id="uf0" value="<?php echo $dados->ds_uf?>"><?php echo $dados->ds_uf?></option>
													<?php
													}
												?>
											<?php 
											$ufs = [
												"AC" => "Acre",
												"AL" => "Alagoas",
												"AP" => "Amapá",
												"AM" => "Amazonas",
												"BA" => "Bahia",
												"CE" => "Ceará",
												"DF" => "Distrito Federal",
												"ES" => "Espírito Santo",
												"GO" => "Goiás",
												"MA" => "Maranhão",
												"MT" => "Mato Grosso",
												"MS" => "Mato Grosso do Sul",
												"MG" => "Minas Gerais",
												"PA" => "Pará",
												"PB" => "Paraíba",
												"PR" => "Paraná",
												"PE" => "Pernambuco",
												"PI" => "Piauí",
												"RJ" => "Rio de Janeiro",
												"RN" => "Rio Grande do Norte",
												"RS" => "Rio Grande do Sul",
												"RO" => "Rondônia",
												"RR" => "Roraima",
												"SC" => "Santa Catarina",
												"SP" => "São Paulo",
												"SE" => "Sergipe",
												"TO" => "Tocantins"
											];

											// Obtendo o valor do campo (se já enviado no formulário)
											$ufSelecionado = set_value('uf', isset($dados->uf) ? $dados->uf : '');

											foreach ($ufs as $sigla => $nome) {
												$selected = ($sigla === $ufSelecionado) ? 'selected' : '';
												echo "<option value=\"$sigla\" $selected>$nome ($sigla)</option>";
											}
											?>
										</select>
										<i class="fas fa-angle-down"></i>
										</div>
										<span class="red"><?php echo session()->getFlashdata('errors')['ds_uf'] ?? '' ?></span>
									</div>
								</fieldset>
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-celular" class="text-nowrap">Celular</label>
											<div class="input-group">
												<div class="input-icon">
												<i class="fas fa-mobile-alt"></i>
												</div>
												<input class="telefones" id="input-celular" name="ds_celular" type="text" placeholder="Telefone de contato" value="<?php echo set_value('ds_celular',isset($dados->ds_celular) ? $dados->ds_celular : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_celular'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-fixo" class="text-nowrap">Fixo</label>
											<div class="input-group">
												<div class="input-icon">
												<i class="fas fa-mobile-alt"></i>
												</div>
												<input class="telefones" id="input-fixo" name="ds_fixo" type="text" placeholder="Telefone de contato" value="<?php echo set_value('ds_fixo',isset($dados->ds_fixo) ? $dados->ds_fixo : "");?>"/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_fixo'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset class="identificacao">
									<div class="col-sm-10 col-lg-12">
										<div class="br-input">
											<label for="input-email" class="text-nowrap">Email</label>
											<div class="input-group">
												<div class="input-icon">
													<i class="fas fa-envelope" aria-hidden="true"></i>
												</div>
												<input id="input-email" type="email" name = "ds_email" placeholder="Seu email mais ativo" value="<?php echo set_value('ds_email',isset($dados->ds_email) ? $dados->ds_email : "");?>" required/>
												<span class="errorMensagem"><?php echo session()->getFlashdata('errors')['ds_email'] ?? '' ?></span>
											</div>
										</div>
									</div>
								</fieldset>
								
							</div>
							<div class="mrg-bottom-30">
								<fieldset>
									<hr>
									<div class="col-sm-10 col-lg-12">
										<div class="br-button-group">
											<?php 
												if($acao == "create"){
											?>
												<button class="br-button block primary mb-3 " type="submit">Salvar seus dados</button>
												<?php
												}else{
											?>
												<button class="br-button block primary mb-3 " type="submit">Alterar seus dados</button>
												<?php
												}
											?>
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
</main>
