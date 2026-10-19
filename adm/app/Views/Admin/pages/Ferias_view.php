<div  class="content-wrapper" >
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

	<section class="content"  style=" height:85%;">
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
									if($i == 1){
										$largura = 200;
									}else{
										$largura = 100;
									}
							?>

							  <th  align="center" width="<?php echo $largura?>"><?php echo $titulosTabela[$i]?></th>
								  
							<?php 
								}
							?>
                       		<th>Alterar</th>
						
							<th>Excluir</th>
						
                        	</tr>
                      	</thead>
                      	<tbody>
                      	
						<?php
						  foreach($ferias as $valueFerias){
						?>
							
						  <tr>
							  
							<td align="left" width="350"><?php echo $valueFerias->ds_nome?></td>
							<td align="left"><?php echo $valueFerias->ds_ano_referente?></td>
							<?php
							list($ano, $mes, $dia) = explode('-', $valueFerias->ds_data_inicio);
							$mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
							$dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
							$dataInicio = $dia . '/' . $mes . '/' . $ano;
							?>
							<td align="center"><?php echo $dataInicio?></td>
							<?php
							list($ano, $mes, $dia) = explode('-', $valueFerias->ds_data_final);
							$mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
							$dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
							$dataFinal = $dia . '/' . $mes . '/' . $ano;
							?>
							<td align="center"><?php echo $dataFinal?></td>
							<td align="center"><?php echo $valueFerias->ds_dias_ferias?></td>
							<?php
							if($valueFerias->ds_status == 1){
								$status = "Usufruída";
								$cor = "green";
							}else{
								$status = "Nao Usufruída";
								$cor = "red";
							}
						
							?>
							<td align="center"><span class="<?php echo $cor?>"><?php echo $status?></span></td>
							
							<td align="center">
								<a class="editarFerias pointer" data-id="<?php echo $valueFerias->pk_id_ferias?>" 
								href="<?php echo base_url("/Ferias/formularioAlteracao")."/".$valueFerias->pk_id_ferias?>">
								<i class="fas fa-edit"></i></a>
							</td>
							<td align="center"><a class="deleteItem pointer" data-id="<?php	echo $valueFerias->pk_id_ferias.'|'.$valueFerias->ds_nome?>"> <i class="fa fa-trash"></i></a>
							</td>
							
						</tr>
						  <?php } ?>
                     
                      </tbody>
                    </table>
					
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
					<form method="POST" action="<?php echo base_url().'/Ferias/deletar'?>">
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

