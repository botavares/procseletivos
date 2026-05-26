	<section class="content-wrapper">
		<div class="container-fluid p0">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">
					    <h1><?php echo $titulo?></h1>
						<?php if (session()->has('errors')): ?>
    						<div class="alert alert-danger text-center" role="alert">
								<ul class="list-unstyled mb-0">
									<?php foreach (session('errors') as $error): ?>
										<li><?= esc($error) ?></li>
									<?php endforeach; ?>
								</ul>
    						</div>
						<?php endif; ?>
						<?php if (session()->has('mensagemError')): ?>
    						<div class="alert alert-danger text-center" role="alert">
									<?= esc(session('mensagemError')) ?>
    						</div>
						<?php endif; ?>
                    </h3>
				</div>
				<div class="card-body">

					<form id="formRespostas" class="formEditor form-horizontal col-md-12 col-lg-12" method="POST" action="<?php echo base_url()."/Manifestacoes/registrarResposta"?>">
						<input type="hidden" name="action" value="create">
						<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						
                        <div  class="form-group row m0">
							<label style="font-size:16px;" for="input-protocolo" class="col-form-label col-md-2 col-sm-12 mb0">Protocolo</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo $dados->ds_protocolo ?></p>

							</div>
						</div>
                        <div  class="form-group row m0 bg-lightgray">
							<label style="font-size:16px;" for="input-tipo_manifesto" class="col-form-label col-md-2 col-sm-12 mb0">Manifestante</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo ($dados->ds_anonimo==1) ? "Identificado" : "Anônimo" ?></p>
							</div>
						</div>

						
                        
						
						<?php
						if($dados->ds_anonimo==1){?>
							
								<div class="form-group row m0">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Nome</label>
									<div class="col-md-10 col-sm-12 centra-vertical">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_nome?></p>
									</div>
								</div>
								<div class="form-group row m0 bg-lightgray">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Email</label>
									<div class="col-md-10 col-sm-12 centra-vertical">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_email?></p>
									</div>
								</div>
                                <div class="form-group row m0">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Bairro</label>
									<div class="col-md-10 col-sm-12 centra-vertical">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $bairro?></p>
									</div>
								</div>
                                <div class="form-group row m0 bg-lightgray">
									<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Telefone</label>
									<div class="col-md-10 col-sm-12 centra-vertical">
										<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_telefone?></p>
									</div>
								</div>
							
						<?php } ?>
                        <div  class="form-group row m0">
							<label style="font-size:16px;" for="input-tipo_manifesto" class="col-form-label col-md-2 col-sm-12 mb0">Categoria</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo $dados->ds_manifestacao ?></p>
							</div>
						</div>
                        <div  class="form-group row m0 bg-lightgray">
							<label style="font-size:16px;" for="input-tipo_manifesto" class="col-form-label col-md-2 col-sm-12 mb0">Canal de comunicação</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo $canal?></p>
							</div>
						</div>

                        

						<div class="form-group row m0">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Data do Manifesto</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo date("d/m/Y", strtotime($dados->ds_datacadastro));?></p>
							</div>
						</div>
						<div class="form-group row  m0 bg-lightgray">
                        
							<label style="font-size:16px;" for="input-tipo_manifesto" class="col-form-label col-md-2 col-sm-12 mb0">Área da Prefeitura</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff"><?php echo $secretaria?></p>
							</div>
						</div>
						<div class="form-group row m0">
						
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Setor Direcionado</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_nome_setor;?></p>
							</div>
						</div>
						<div class="form-group row m0 bg-lightgray">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-sm-2">Assunto</label>
							<div class="col-md-10 col-sm-12 centra-vertical">
								<p style="font-size:16px; text-align:left;color:#007bff;"><?php echo $dados->ds_assunto;?></p>
							</div>
						</div>
						<div class="form-group row m0 mtop10 mbot10">
							<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Relato do Manifestante</label>
							<div style="height:120px;"class="col-md-10 col-sm-12 chanfrado bg-cordepapel scroll-y ">
								<p class="" style="font-size:18px; text-align:justify;text-ident:2em;"><?php echo $dados->ds_texto_manifestacao;?></p>
							</div>
						</div>
						<div>
							<?php 
								if($fotos){
							?>
							<div class="form-group row m0 mtop10 mbot10">
								<label style="font-size:16px;" for="input-classes" class="col-form-label col-md-2 col-sm-12">Arquivos em anexo</label>
								<div style="height:120px;"class="col-md-10 col-sm-12 chanfrado">
									<ul>
										<?php
											foreach($fotos as $foto){
										?>
											<li class="bold"><a href="<?php echo esc($foto["caminho"])?>" target="_blank"><?php echo $foto["nomeArquivo"]?> </a></li>
										<?php } ?>
									</ul>
								</div>

							<?php
								}
							?>
						</div>
    					<div class="card-footer">
							<?php
								if($dados->ds_respondido == 1){
									$caminhoResposta = base_url("Respostas/alterarResposta/".$dados->pk_id_manifesto."");
									$textoBotaoResposta = "Alterar Última Resposta";

									if($verificarRespondido->ds_data_alteracao_resposta ===  '0000-00-00'){
										$infoResposta = "Respondido em: ". date("d/m/Y", strtotime($verificarRespondido->ds_data_resposta))." pelo agente ".$agenteResposta."</br>";
										
									}else{
										$infoResposta = "Respondido em: ". date("d/m/Y", strtotime($verificarRespondido->ds_data_resposta)).". </br> ".
										"Resposta alterada em: ". date("d/m/Y", strtotime($verificarRespondido->ds_data_alteracao_resposta))." pelo agente ".$agenteResposta."</br>";
									}

									
								}else{
									$caminhoResposta = base_url("Respostas/gerarResposta/".$dados->pk_id_manifesto."");
									$textoBotaoResposta = "RESPONDER Manifestação";
									$infoResposta = "";
								}

								if($dados->ds_tramitado == 1){
									$caminhoTramite = base_url("Tramites/alterarTramite/".$dados->pk_id_manifesto."");
									$textoBotaoTramite = "Alterar Trâmite";
									if($verificarTramitado->ds_data_alteracao === '0000-00-00'){
										$infoTramite = "Tramitado em: ". date("d/m/Y", strtotime($verificarTramitado->ds_data_tramite))." pelo agente ". $agenteTramite."</br>";
										
									}else{
										$infoTramite = "Tramitado em: ". date("d/m/Y", strtotime($verificarTramitado->ds_data_tramite))." pelo agente ".$agenteTramite."</br>".
										"Trâmite alterado em: ". date("d/m/Y", strtotime($verificarTramitado->ds_data_alteracao))." pelo agente ".$agenteTramiteAlt."</br>";
									}
									
								}else{
									$caminhoTramite = base_url("Tramites/gerarTramite/".$dados->pk_id_manifesto."");
									$textoBotaoTramite = "TRAMITAR Manifestação";
									$infoTramite = "";
								}
								if($dados->ds_encerrado == 1){
									$infoEncerrado = "Encerrado em ". date("d/m/Y", strtotime($verificarEncerrado->ds_data_analise))." pelo agente ". $agenteEncerra."</br>";
								}else{
									$infoEncerrado = "";
								}	

							?>
							<div class="form-groupo centralizado row">
                                <div class=" col-md-4 col-sm-12 col-xs-12 col-md-offset-1  centra-horizontal">
                                    <a href="<?php echo $caminhoTramite?>" type="button" class="btn btn-primary col-md-12"><?php echo $textoBotaoTramite?></a>
								</div>
								<div class=" col-md-4 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
                                    <a href="<?php echo $caminhoResposta?>" type="button" class="btn btn-info text-center col-md-12"><?php echo $textoBotaoResposta?></a>
								</div>
								<div class="col-md-4 col-sm-12 col-xs-12 col-md-offset-1 centra-horizontal">
                                    <a href="<?php echo base_url("Manifestacoes/gerenciarManifestos")?>" type="button" class="btn btn-warning text-center col-md-12"><?php echo "Voltar"?></a>
								</div>
								<ol>
									<li><?php echo $infoTramite?></li>
									<li><?php echo $infoResposta?></li>
									<li><?php echo $infoEncerrado?></li>
								</ol>
							</div>
						</div>
					</div>
				</form>
			</div>
    	</div>
	</section>
