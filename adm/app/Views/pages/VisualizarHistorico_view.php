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
					<div class="chanfrado border pdd-5 mrg-bottom-20 pdd10 mbot10">
						<span class="ml-3 mr-3 font18 w400">Manifesto registrado com número de protocolo:</span>
							<span class="green bold font18"><?php echo $dadosManifestos->ds_protocolo; ?></span>
						<br>
						<?php 
							if($dadosManifestos->ds_anonimo == 1){
						?>
								<span class="ml-3 mr-3 font18 w400">Nome do cidadão:</span>
								<span class="blue font18"><?php echo $dadosManifestos->ds_nome; ?></span>
						<?php
							}else{
						?>
						<span class="ml-3 mr-3 font18 w400">Manifestação Anônima:</span>
						<span class="blue font18">Sim</span>
						<?php } ?>

						<br>
						<span class="ml-3 mr-3 font18 w400">Data do manifesto:</span>
							<span class="blue font18"><?php echo date("d/m/Y", strtotime($dadosManifestos->ds_datacadastro));?></span>
						<br>
						<?php if($dadosTramites){?>
							<span class="ml-3 mr-3 font18 w400">Tramitado no dia:</span>
							<span class="blue font18"><?php echo date("d/m/Y", strtotime($dadosTramites[0]->ds_data_tramite)). "   <span class='black'> pelo agente </span>".$dadosTramites[0]->ds_nome_user;?></span>
						<?php
							}else{
								if($manifestoRespondido){
								?>
									<span class="ml-3 mr-3 font18 w400"><strong>Manifesto respondido.</strong></span>
							<?php
								}else{
							?>
									<span class="ml-3 mr-3 font18 w400"><strong>Manifesto em andamento.</strong></span>
							<?php
								}
							}
						?>
						<?php
						if($dadosAlteraTramites){?>
							<span class="ml-3 mr-3 font18 w400">Tramite redirecionado no dia:</span>
							<span class="blue font18"><?php echo date("d/m/Y", strtotime($dadosAlteraTramites[0]->ds_data_alteracao)). "   <span class='black'> pelo agente </span>".$dadosAlteraTramites[0]->ds_nome_user;?></span>
						<?php 
						}
						?>
						<br>
						<span class="ml-3 mr-3 font18 w400">Categoria:</span>
							<span class="blue font18"><?php echo $dadosManifestos->ds_manifestacao;?></span>
						<br>
						<span class="ml-3 mr-3 font18 w400">Feito pelo canal de comunicação:</span>
							<span class="blue font18"><?php echo $dadosManifestos->ds_canal;?></span>
						<br>
						<span class="ml-3 mr-3 font18 w400">Manifesto dirigido ao setor:</span>
							<span class="blue font18"><?php echo $dadosManifestos->ds_nome_setor." / ".$dadosManifestos->ds_nome_secretaria;?></span>
						<br>
						<span class="ml-3 mr-3 font18 w400">Assunto:</span>
							<span class="blue font18"><?php echo $dadosManifestos->ds_assunto;?></span>
					</div>


					<div class="chanfrado border pdd-10 mrg-bottom-20 pdd5 mbot10">						
						<span class="ml-5 mr-3 font18 w600">Manifestação do cidadão:</span><br>
							<span class="ml-5 mr-3 font18 "><?php echo $dadosManifestos->ds_texto_manifestacao;?></span>
						<br>
					</div>
					<div class="chanfrado bg-cordepapel pdd-5 mrg-bottom-20 pdd10">


						<?php 
							foreach($dadosRespostas as $respostas){
								if($respostas->ds_resposta == 1){
						?>		
									<div class="chanfrado bg-azulclaro  mrg-bottom-20 pdd10 mbot10">
										<span class="ml-5 mr-3 font18 w600">Resposta proferida no dia <?php echo date("d/m/Y", strtotime($respostas->ds_data_resposta)). " pelo agente ".$respostas->ds_nome_user;?>:</span><br>	
										<div class="ml-5 mr-3 font18"><p><?php echo $respostas->ds_texto_resposta;?></p></div>
									</div>


							<?php
								}else if($respostas->ds_resposta == 0){
							?>
									<div class="chanfrado bg-lightgray  mrg-bottom-20 pdd10 mbot10">
										<span class="ml-5 mr-3 font18 w600">Contestação proferida no dia <?php echo date("d/m/Y", strtotime($respostas->ds_data_resposta));?>:</span><br>	
										<span class="ml-5 mr-3 font18 "><?php echo $respostas->ds_texto_resposta;?></span>
									</div>
						<?php } 
						}?>

						<?php
							
						?>
						<?php
								if( $manifestoEncerrado){
						?>
									<div class="chanfrado bg-lightred  mrg-bottom-20 pdd25 mbot10">
										<label class="form-label ml-5 mr-3 font18">Manifesto <span class="red">encerrado</span> no dia <?php echo date("d/m/Y", strtotime($manifestoEncerrado->ds_data_analise)). " pelo agente ".$respostas->ds_nome_user?>.</label><br>	
									</div>
									<div class="chanfrado bg-lightred  mrg-bottom-20 pdd25 mbot10">
										<a class = "btn btn-warning"href="<?php echo base_url("Manifestacoes/gerenciarManifestos/") ?>">Voltar</a>
									</div>
						<?php 	
								}else{
						?>	
									<div class="chanfrado bg-lightgreen  mrg-bottom-20 pdd25 mbot10 centra-horizontal">
										<a class = "col-md-4 btn btn-danger mr-3"href="<?php echo base_url("Manifestacoes/encerrarManifesto/".$dadosManifestos->pk_id_manifesto) ?>">Encerrar esse manifesto</a>
										<a class = "col-md-4 btn btn-warning mr-3"href="<?php echo base_url("Manifestacoes/gerenciarManifestos/") ?>">Voltar</a>
						<?php
								}
							
						?>


					</div>
					
				</div>
			</div>
    	</div>
	</section>
