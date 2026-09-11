<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?php echo $titulo. " - ". $nomeCargo?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url("Dashboard")?>">Home</a></li>
						<li class="breadcrumb-item active">Classificações</li>
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
		                       			<th>Exibir Dados</th>
        		                	</tr>
                		      	</thead>
                       			<tbody>
									<?php
										foreach($classificacoes as $classificacao){
									?>
								  <tr>
									<?php
										list($ano, $mes, $dia) = explode('-', $classificacao["dt_nascimento"]);
										$dataNascimento = $dia."/".$mes."/".$ano;
									?>
                                    <td align="center"><?= $classificacao["ds_posicao"]?></td>
									<td width="300"align="left"><?= $classificacao["ds_nome_candidato"]?></td>

									<?php if (isset($usaDesempateDinamico) && $usaDesempateDinamico && !empty($configDesempate)): ?>
										<?php foreach ($configDesempate as $config): ?>
											<?php
												$chave = $config->chaveScore();
												$valor = $classificacao['_scores'][$chave] ?? 0;
											?>
											<td align="center"><?= is_numeric($valor) ? number_format((float)$valor, 2, ',', '.') : esc($valor) ?></td>
										<?php endforeach; ?>
										<td align="center"><?= date('d/m/Y', strtotime($classificacao["dt_nascimento"])) ?></td>
										<td align="center"><?= $classificacao["nr_total_pontos"]?></td>
									<?php else: ?>
										<td align="center"><?= $classificacao["nr_total_experiencias"]?></td>
										<td align="center"><?= $classificacao["nr_total_graduacao"]?></td>
										<td align="center"><?= $classificacao["nr_total_posgraduacao"]?></td>
										<td align="center"><?= $classificacao["nr_total_mestrado"]?></td>
										<td align="center"><?= $classificacao["nr_total_doutorado"]?></td>
										<td align="center"><?= $classificacao["nr_total_aperfeicoamentos"]?></td>
										<td align="center"><?= date('d/m/Y', strtotime($classificacao["dt_nascimento"])) ?></td>
										<td align="center"><?= $classificacao["nr_total_pontos"]?></td>
									<?php endif; ?>

									<?php
										$tokenName = csrf_token();
										$tokenHash = csrf_hash();
									?>

									<td align="center">
										<a class="contratar pointer" data-id="<?php echo $classificacao["fk_id_candidato"]?>" 
										href="<?php echo base_url("/Candidatos/exibirDados")."/".$classificacao["fk_id_edital"]."/".$classificacao["fk_id_cargo"]."/".$classificacao["fk_id_candidato"]?>">
										<i class="fas fa-edit"></i></a>
									</td>
								</tr>
						  <?php } ?>
                      </tbody>
                    </table>
							<a href="<?php echo base_url("Dashboard")?>" type="button" class="btn btn-warning col-md-2 mtop10 chanfrado mbot10">Voltar</a>
							<button type="button" id="btnExportarPlanilha" class="btn btn-success col-md-2 mtop10 chanfrado mbot10 float-right">
								<i class="fas fa-file-excel"></i> Exportar Planilha
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<script>
document.getElementById('btnExportarPlanilha').addEventListener('click', function () {
	var table = $('#tabela-paginada').DataTable();
	var dados = [];
	var titulos = [];

	table.columns().every(function (index) {
		if (index < table.columns().count() - 1) {
			titulos.push(this.header().textContent.trim());
		}
	});

	table.rows({ search: 'applied' }).every(function () {
		var row = this.data();
		var linha = {};
		for (var i = 0; i < titulos.length; i++) {
			linha[titulos[i]] = row[i] !== undefined ? $('<div>').html(row[i]).text().trim() : '';
		}
		dados.push(linha);
	});

	var form = document.createElement('form');
	form.method = 'POST';
	form.action = '<?php echo base_url("Classificacoes/exportarTabela/" . $idEdital . "/" . $idCargo) ?>';
	form.style.display = 'none';

	var inputDados = document.createElement('input');
	inputDados.type = 'hidden';
	inputDados.name = 'dados';
	inputDados.value = JSON.stringify(dados);
	form.appendChild(inputDados);

	var inputTitulos = document.createElement('input');
	inputTitulos.type = 'hidden';
	inputTitulos.name = 'titulos';
	inputTitulos.value = JSON.stringify(titulos);
	form.appendChild(inputTitulos);

	var inputCsrf = document.createElement('input');
	inputCsrf.type = 'hidden';
	inputCsrf.name = '<?php echo csrf_token(); ?>';
	inputCsrf.value = '<?php echo csrf_hash(); ?>';
	form.appendChild(inputCsrf);

	document.body.appendChild(form);
	form.submit();
	document.body.removeChild(form);
});
</script>
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
					<form method="POST" action="<?php echo base_url().'/Abrangencias/deletar'?>">
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

