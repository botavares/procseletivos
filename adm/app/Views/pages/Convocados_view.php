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
						<li class="breadcrumb-item active">Convocados</li>
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
							<?php
							if(isset($idEdital)) {
							?>	
								<a href="<?php echo base_url("Candidatos/$idEdital".'/'."$idCurso")?>" type="button" class="btn btn-warning col-md-2 mtop10 chanfrado mbot10">Voltar</a>
							<?php
								} else {
							?>
								<a href="<?php echo base_url("Dashboard")?>" type="button" class="btn btn-warning col-md-2 mtop10 chanfrado mbot10">Voltar</a>
							<?php
							}
							?>
							
                    		<?php if(session()->has('mensagemError')):?>
								<div class="porta-mensagem alert alert-danger col-md-12 font20 ta-center">
									<?= esc(session('mensagemError')) ?>
								</div>
							<?php endif ?>
                    		<?php if(session()->has('mensagemSuccess')):?>
								<div class="porta-mensagem alert alert-success col-md-12 font20 ta-center">
									<?= esc(session('mensagemSuccess')) ?>
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
							  <th  align="center"><?php echo $titulosTabela[$i]?></th>
								  
							<?php 
								}
							?>
							
                       		<th>Comparecimento</th>

							<th>Contratar/Encaminhar</th>
						
                        	</tr>
                      	</thead>
                      	<tbody>
                      	
						<?php
						  foreach($convocados as $valueConvocados){
						?>
							
						  <tr>
							<?php
								list($ano, $mes, $dia) = explode('-', $valueConvocados->ds_nascimento);
								$dataNascimento = $dia."/".$mes."/".$ano;

								list($anoC, $mesC, $diaC) = explode('-', $valueConvocados->ds_data);
								$dataConvocacao = $diaC."/".$mesC."/".$anoC;
								if($valueConvocados->ds_interesse == '1'){
									$statusInteresse = "<i class='fas fa-thumbs-up green' title='Candidato demonstrou interesse pelo email'></i>";
								}else{
									$statusInteresse = "";
								}
							?>
							
							<td align="center"><?php echo $valueConvocados->ds_periodo?></td>
							<td align="left"><?php echo $valueConvocados->ds_nome." ".$statusInteresse?></td>
							<td align="center"><?php echo $dataNascimento?></td>
							<td align="left"><?php echo mask($valueConvocados->ds_celular, '(##) #####-####')?></td>
							<td align="left"><?php echo $valueConvocados->ds_email?></td>
							<td align="left"><?php echo $valueConvocados->ds_nome_curso?></td>
							<td align="center"><?php echo $dataConvocacao?></td>

							  <?php
								$tokenName = csrf_token();
								$tokenHash = csrf_hash();
							?>

							<td align="center">
								<input type="checkbox" class="comparecimento" data-id="<?php	echo $valueConvocados->pk_id_candidato?>" <?php if($valueConvocados->ds_comparecimento == '1'){echo 'checked';}?> />
								<input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
							</td>
						
							<td id="coluna-contratar" align="center">
								<a class="contratar pointer" data-id="<?php  echo $valueConvocados->pk_id_candidato?>" 
								href="<?php echo base_url("/Contratos/formContratar")."/".$valueConvocados->pk_id_candidato?>">
								<i class="fas fa-edit"></i></a>
							</td>

							
						</tr>
						  <?php } ?>
                     
                      </tbody>
                    </table>
					<p class="text-danger">Obs: Após 48 horas da data e hora da convocação, o sistema realizará a "desconvocação" automática dos candidatos que não se manifestaram.</p>
					
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
					<form method="POST" action="<?php echo base_url().'/Convocados/deletar'?>">
						<h5 class="modal-title" id="modalLabel">Realmente deseja excluir:</h5>
						<input id="chavePrimaria"type="hidden" name="chavePrimaria" />
						<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
						<label id="nomeItem"></label>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
								<button type="submit" class="btn btn-danger botao-refresh">Excluir</button>
							</div>	
					</form>
				</div>
    		</div>
	</div>
</div>


</div>	

