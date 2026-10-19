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
						<li class="breadcrumb-item active">Instituições</li>
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
							<h3 class="card-title m0">
								<a href="<?php echo base_url("/Instituicoes/formularioCadastro")?>" class="ta-center btn btn-primary" >
									<p>Adicionar Instituicao</p>
								</a>
							</h3>
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
						  foreach($instituicoes as $valueInstiuicoes){
						?>
							
						  <tr>
							  
							<td align="left" class="font20"><?php echo $valueInstiuicoes->ds_nome?></td>
                            <td align="center"><?php echo $valueInstiuicoes->ds_cidade?></td>
							<td align="center"><?php echo $valueInstiuicoes->ds_email?></td>
							<td align="center"><?php echo mask($valueInstiuicoes->ds_telefone,"(##)####-####")?></td>
							<td align="center">
								<a class="editarBoxes pointer" data-id="<?php echo $valueInstiuicoes->pk_id_instituicao?>" href="<?php echo base_url("/Instituicoes/formularioAlteracao")."/".$valueInstiuicoes->pk_id_instituicao?>"><i class="fas fa-edit"></i></a>
							</td>
							<td align="center"><a class="deleteItem pointer" data-id="<?php	echo $valueInstiuicoes->pk_id_instituicao.'|'.$valueInstiuicoes->ds_nome?>"> <i class="fa fa-trash"></i></a>
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


<div class="modal fade" id="modalDeleteItens" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="<?php echo base_url().'/Instituicoes/deletar'?>">
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

