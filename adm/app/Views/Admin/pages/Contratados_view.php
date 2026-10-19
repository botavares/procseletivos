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
						<li class="breadcrumb-item active">Contratos</li>
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
							<a href="<?php echo base_url("Dashboard")?>" type="button" class="btn btn-warning col-md-2 mtop10 chanfrado mbot10">Voltar</a>
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
							
							<th>Alterações</th>
							<th>Férias</th>
							
								
							<th>Aditivo / Encerrar</th>
						
                        	</tr>
                      	</thead>
                      	<tbody>
                      	
						<?php
						  foreach($dados as $valueContratado){
						?>
							
						  <tr>
							<?php
								list($ano, $mes, $dia) = explode('-', $valueContratado["ds_data_ingresso"]);
								$ingresso = $dia."/".$mes."/".$ano;

								list($ano, $mes, $dia) = explode('-', $valueContratado["ds_data_encerramento"]);
								$encerramento = $dia."/".$mes."/".$ano;
								if($valueContratado["ds_data_encerramento"] <= date('Y-m-d')){
									$cor = "danger";
									if($valueContratado["ds_notificado"] == 1){
										$status = " (notificado)";
									}else{
										$status = " (nao notificado)";
									}
								}else if($valueContratado["ds_data_encerramento"] <= date('Y-m-d', strtotime("+".$diasParaEncerrar." days"))){
									if($valueContratado["ds_notificado"] == 1){
										$status = " (notificado)";
									}else{
										$status = " (nao notificado)";
									}
									$cor = "warning";
								}else{
									$cor = "none";
									$status = "";
								}
							?>
							  
							<td align="left" width="300"><span class="text-<?php echo $cor?>"><?php echo $valueContratado["ds_nome_candidato"]?></span></td>
							<!--<td align="left"><?php echo formatarNumeroEdital($valueContratado["ds_numero_edital"])?></td>-->
							<td align="left"><span class=" text-<?php echo $cor?>"><?php echo $valueContratado["ds_nome_curso"]?></span></td>
							<td align="left" width="300"><span class=" text-<?php echo $cor?>"><?php echo $valueContratado["ds_nome_setor"]?></span></td>
							<td align="left"><span class=" text-<?php echo $cor?>"><?php echo $encerramento." ".$status?></span></td>
							<!--<td align="left"><span class=" text-<?php echo $cor?>"><?php echo $encerramento?></span></td>-->
							<td align="left"><span class=" text-<?php echo $cor?>"><?php echo $valueContratado["diasTrabalhados"]?> dias</span></td>
							<td align="left"><span class=" text-<?php echo $cor?>"><?php echo $valueContratado["feriasDisponiveis"]?> dias</span></td>
							<td align="left"><span class=" text-<?php echo $cor?>"><?php echo $valueContratado["feriasTiradas"]?> dias</span></td>
							

							  <?php
								$tokenName = csrf_token();
								$tokenHash = csrf_hash();
							?>

							
							<td align="center">
								<a class="Alteracoes pointer" data-id="<?php echo $valueContratado["pk_id_contrato"]?>" 
								href="<?php echo base_url("/Contratos/formAlterarContrato")."/".$valueContratado["pk_id_contrato"]?>">
								<i class="fas fa-edit"></i></a>
							</td>
							<td align="center">
								<a class="agendar pointer" data-id="<?php echo $valueContratado["fk_id_candidato"]?>" 
								href="<?php echo base_url("/Ferias/formularioCadastro")."/".$valueContratado["fk_id_candidato"]?>">
								<i class="fas fa-calendar-alt"></i></a>
							</td>
							<td align="center">
								<?php
								    if($valueContratado["ds_status"] == '2'){
								?>
								<span class="text-muted">Encerrado</span>
								<?php
								    } else {
								?>
								<a class="encerrar pointer" data-id="<?php echo $valueContratado["fk_id_candidato"]?>" 
								href="<?php echo base_url("/Contratos/formAditivoRescindir")."/".$valueContratado["pk_id_contrato"]?>">
								<i class="fas fa-file-contract"></i></a>
								<?php
								    }
								?>
									
							</td>
							
						</tr>
						  <?php } ?>
                     
                      </tbody>
                    </table>
					
					<?php if($contratosExpirando):?>
						<a href="<?php echo base_url("Contratos/contratosExpirando")?>" class="btn btn-danger">Contratos Expirando</a>
					<?php endif?>
				</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>		
<footer class="main-footer">
<div class="float-right d-none d-sm-block">

</div>
<strong>Copyright &copy; <?php echo date('Y')?> <a href="https://www.divinópolis.mg.gov.br">Prefeitura Municipal de Divinópolis</a></strong>.
</footer>

<aside class="control-sidebar control-sidebar-dark">

</aside>	
<div class="modal fade" id="modalDeleteItens" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="<?php echo base_url().'/Contratos/deletar'?>">
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

