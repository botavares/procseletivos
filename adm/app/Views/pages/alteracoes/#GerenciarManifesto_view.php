<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?php echo $titulo?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url("Dashboard")?>">Home</a></li>
						<li class="breadcrumb-item active">Serviços</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
                   		<?php if(session()->has('mensagemError')):?>
						<div class="porta-mensagem alert alert-danger col-md-12">
							<ul>
							<ul>
								<li class="text text-white text-center bold font22"><?php echo session()->getFlashdata('mensagemError')?></li>
							</ul>
							</ul>
						</div>
						<?php endif ?>
                    	<?php if(session()->has('mensagemSuccess')):?>
						<div class="porta-mensagem alert alert-success col-md-12">
							<ul>
								<li class="text text-white text-center bold font22"><?php echo session()->getFlashdata('mensagemSuccess')?></li>
							</ul>
						</div>
						<?php endif ?>
					</div>

					<div class="card-body">
						<table id="tabela-paginada" class=" minhaDataTable table table-bordered table-striped">
                    		<thead>
                       			<tr>
                        	<?php
								for($i = 0,$j=count($titulosTabela);$i<$j;$i++){
							?>
							  <th  class="text-center"><?php echo $titulosTabela[$i]?></th>
								  
							<?php 
								}
							?>
							<th class="text-center">Ver Histórico</th>
							<th class="text-center">Visualizar</th>
							<th class="text-center">Responder</th>
							<th class="text-center">Tramitar</th>

                       		
                            <!-- responder e tramitar deverão partir da página onde mostra o histórico e/ou a visualização do manifesto -->
                        	</tr>
                      	</thead>
                      	<tbody>
                      	
						<?php
						  foreach($manifestos as $valueManifestos){
						?>
							
						  <tr>
							<td align="left" class="font20 text-center"><?php echo $valueManifestos->pk_id_manifesto?></td>
							<td align="left" class="font20 text-center"><?php echo date('d/m/Y', strtotime($valueManifestos->ds_datacadastro))?></td>
                            <td align="left" class="font20 text-center"><?php echo date('d/m/Y', strtotime($valueManifestos->ds_data_analise))?></td>
								<?php
									$tramitado = $valueManifestos->ds_tramitado;
									$respondido = $valueManifestos->ds_respondido;
									$contestado = $valueManifestos->ds_contestado;

									if($tramitado == 1 && $respondido == 1 && $contestado == 0){
										$situacao = "Tramitado e Respondido";
										$cor = "green";
									}else if($tramitado == 1 && $respondido == 0 && $contestado == 0){
										$situacao = "Tramitado";
										$cor = "blue";
									}else if($tramitado == 0 && $respondido == 1 && $contestado == 0){
										$situacao = "Respondido imediato";
										$cor = "green";
									}else if($tramitado == 0 && $respondido == 0 && $contestado == 1){
										$situacao = "Contestado";
										$cor="red";
									}else if($tramitado == 1 && $respondido == 0 && $contestado == 1){
										$situacao = "Contestado";
										$cor="red";
									}else if($tramitado == 0 && $respondido == 0 && $contestado == 0){
										$situacao = "Pendente";
										$cor="orange";
									}
								?>

                            <td align="left" class="<?php echo $cor?> text-center font20"><?php echo $situacao?></td>
							<td align="center" class="font20"><a class="gerarHistorico pointer" data-id="<?php echo $valueManifestos->pk_id_manifesto?>" 
									href="<?php echo base_url("/Manifestacoes/gerarHistorico")."/".$valueManifestos->pk_id_manifesto?>">
									<i class="far fa-hourglass"></i></a>
                            </td>
                            <td align="center" class="font20"><a class="visualizarManifesto pointer" data-id="<?php echo $valueManifestos->pk_id_manifesto?>" 
								href="<?php echo base_url("/Manifestacoes/visualizarManifesto")."/".$valueManifestos->pk_id_manifesto?>">
                                <i class="fas fa-search"></i></a>
                            </td>
							<td align="center" class="font20"><a class="gerarResposta pointer" data-id="<?php echo $valueManifestos->pk_id_manifesto?>" 
								href="<?php echo base_url("/Manifestacoes/gerarResposta")."/".$valueManifestos->pk_id_manifesto?>">
                                <i class="fas fa-comments"></i></a>
                            </td>	
							<td align="center" class="font20"><a class="tramitarManifesto pointer" data-id="<?php echo $valueManifestos->pk_id_manifesto?>" 
								href="<?php echo base_url("/Manifestacoes/gerarTramite")."/".$valueManifestos->pk_id_manifesto?>">
                                <i class="fas fa-exchange-alt"></i></a>
                            </td>
						</tr>
						<?php 
						  } 
						?>
                     
                      </tbody>
                    </table>
				</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>		


<div class="modal fade" id="modalDeleteItens" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="<?php echo base_url().'/Instrucoes/deletar'?>">
						<h5 class="modal-title" id="modalLabel">Realmente deseja excluir:</h5>
						
						<input id="chavePrimaria"type="hidden" name="chavePrimaria" />
						<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						<label id="nomeItem"></label>
						<p class="red">Lembrando que após a exclusão da INSTRUÇÃO, o serviço continuará aparecendo na lista de Serviços avaliados.</p>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
								<button type="submit" class="btn btn-danger botao-refresh">Excluir</button>
							</div>	
					</form>
				</div>
    		</div>
	</div>
</div>		

