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
						<li class="breadcrumb-item active">Editais</li>
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
							<a href="<?php echo base_url("/Editais/formularioCadastro")?>" type="button" class="btn btn-success col-md-2 mtop10 chanfrado mbot10">Criar Edital</a>
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
							
                       		<th>Alterar</th>
						
							<th>Excluir</th>
						
                        	</tr>
                      	</thead>
                      	<tbody>
                      	
						<?php
						  foreach($editais as $valueEditais){
						?>
							
						  <tr>
							  
							<td align="center"><?php echo formatarNumeroEdital($valueEditais->ds_numero_edital)?></td>
							<td align="center"><?php echo date('d/m/Y', strtotime($valueEditais->ds_data_inicial));?></td>
							<td align="center"><?php echo date('d/m/Y', strtotime($valueEditais->ds_data_termino));?></td>
							<?php
								$status = ($valueEditais->ds_status == 1) ? 'Ativo' : 'Encerrado';
							?>
							<td align="center"><?php echo $status;?></td>
							  <?php
								$tokenName = csrf_token();
								$tokenHash = csrf_hash();
							?>

							<td align="center">
								<a class="editaEdital pointer" data-id="<?php echo $valueEditais->pk_id_edital?>" 
								href="<?php echo base_url("/Editais/formularioAlteracao")."/".$valueEditais->pk_id_edital?>">
								<i class="fas fa-edit"></i></a>
							</td>
							<td align="center"><a class="deleteItem pointer" data-id="<?php	echo $valueEditais->pk_id_edital.'|'.$valueEditais->ds_numero_edital?>"> <i class="fa fa-trash"></i></a>
							</td>
							
						</tr>
						  <?php } ?>
                     
                      </tbody>
                    </table>
					<a href="<?php echo base_url("Dashboard")?>" type="button" class="btn btn-warning col-md-2 mtop10 chanfrado mbot10">Voltar</a>
					<?php if($tipo == 'ativos'): ?>
						<a href="<?php echo base_url("Editais/encerrados")?>" type="button" class="btn btn-info col-md-2 mtop10 chanfrado mbot10">Editais Encerrados</a>
					<?php else: ?>
						<a href="<?php echo base_url("Editais")?>" type="button" class="btn btn-info col-md-2 mtop10 chanfrado mbot10">Editais ATIVOS</a>							
					<?php endif; ?>
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
					<form method="POST" action="<?php echo base_url().'/Editais/deletar'?>">
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

