<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?php echo esc($titulo) ?></h1>
					<?php if (!empty($nomeCargo)): ?>
						<small class="text-muted">Cargo: <?php echo esc($nomeCargo) ?></small>
					<?php endif; ?>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url('Dashboard') ?>">Home</a></li>
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
					<?php if (session()->has('mensagemError')): ?>
						<div class="alert alert-danger alert-dismissible fade show mb-3 py-2">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?= esc(session('mensagemError')) ?>
						</div>
					<?php endif; ?>
					<?php if (session()->has('mensagemSuccess')): ?>
						<div class="alert alert-success alert-dismissible fade show mb-3 py-2">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?= esc(session('mensagemSuccess')) ?>
						</div>
					<?php endif; ?>

					<div class="card card-outline card-primary">
						<div class="card-header d-flex justify-content-start align-items-center py-2">
							<div class="d-flex">
								<a href="<?php echo base_url('Dashboard') ?>" class="btn btn-warning btn-sm mr-2">
									<i class="fas fa-arrow-left"></i> Voltar
								</a>
								<button type="button" id="btnExportarPlanilha" class="btn btn-success btn-sm mr-2">
									<i class="fas fa-file-excel"></i> Exportar Planilha
								</button>
								<button type="button" id="btnExportarPlanilhaPcd" class="btn btn-success btn-sm mr-2" style="display:none;">
									<i class="fas fa-file-excel"></i> Exportar Planilha PCD
								</button>
								<div class="custom-control custom-switch ml-2" style="padding-top: 5px;">
									<input type="checkbox" class="custom-control-input" id="togglePcd">
									<label class="custom-control-label" for="togglePcd">Apenas PCD</label>
								</div>
							</div>
						</div>

						<div class="card-body p-2">
							<table id="tabela-paginada" class="table table-sm table-bordered table-striped table-hover minhaDataTable mb-0">
								<thead class="thead-light">
									<tr>
										<th class="text-center">Posição</th>
										<th class="text-center">Candidato</th>

										<?php if (isset($usaDesempateDinamico) && $usaDesempateDinamico && !empty($configDesempate)): ?>
											<?php foreach ($configDesempate as $config): ?>
												<th class="text-center"><?= esc($config->descricao ?: $config->tipoCriterio) ?></th>
											<?php endforeach; ?>
											<th class="text-center">Nascimento</th>
											<th class="text-center">Total de Pontos</th>
										<?php else: ?>
											<?php foreach ($colunasDinamicas as $col): ?>
												<?php if ($col['tipo'] !== 'fixo'): ?>
													<th class="text-center"><?= esc($col['label']) ?></th>
												<?php endif; ?>
											<?php endforeach; ?>
											<th class="text-center">Nascimento</th>
											<th class="text-center">Total de Pontos</th>
										<?php endif; ?>

										<th class="text-center">PCD</th>
										<th class="text-center" style="width: 100px;">Exibir Dados</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($classificacoes as $classificacao): ?>
										<tr>
											<td class="text-center"><?= esc($classificacao['ds_posicao']) ?></td>
											<td style="min-width: 280px; white-space: nowrap;"><?= esc($classificacao['ds_nome_candidato']) ?></td>

											<?php if (isset($usaDesempateDinamico) && $usaDesempateDinamico && !empty($configDesempate)): ?>
												<?php foreach ($configDesempate as $config): ?>
													<?php
														$chave = $config->chaveScore();
														$scoreData = $classificacao['_scores'][$chave] ?? null;
														$valor = is_array($scoreData) ? ($scoreData['nr_valor'] ?? 0) : ($scoreData ?? 0);
													?>
														<td class="text-center"><?= is_numeric($valor) ? number_format((float)$valor, 2, ',', '.') : esc($valor) ?></td>
												<?php endforeach; ?>
												<td class="text-center"><?= date('d/m/Y', strtotime($classificacao['dt_nascimento'])) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_pontos']) ?></td>
											<?php else: ?>
												<?php $candDados = $dadosDinamicos[$classificacao['fk_id_candidato']] ?? []; ?>
												<?php foreach ($colunasDinamicas as $col): ?>
													<?php if ($col['tipo'] !== 'fixo'): ?>
														<td class="text-center">
															<?php
																$valor = $candDados[$col['chave']] ?? 0;
																echo is_numeric($valor) ? number_format((float)$valor, 2, ',', '.') : esc($valor);
															?>
														</td>
													<?php endif; ?>
												<?php endforeach; ?>
												<td class="text-center"><?= date('d/m/Y', strtotime($classificacao['dt_nascimento'])) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_pontos']) ?></td>
											<?php endif; ?>

											<td class="text-center"><?= (($classificacao['ds_possui_pne'] ?? 0) == 1) ? 'SIM' : 'NÃO' ?></td>

											<td class="text-center">
												<a class="btn btn-info btn-sm" 
												   href="<?php echo base_url('Candidatos/exibirDados/' . $classificacao['fk_id_edital'] . '/' . $classificacao['fk_id_cargo'] . '/' . $classificacao['fk_id_candidato']) ?>"
												   title="Exibir Dados">
													<i class="fas fa-user"></i>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<script>
document.getElementById('btnExportarPlanilha').addEventListener('click', function () {
	window.location.href = '<?php echo base_url("Classificacoes/exportarXlsx/" . $idEdital . "/" . $idCargo) ?>';
});

document.getElementById('btnExportarPlanilhaPcd').addEventListener('click', function () {
	window.location.href = '<?php echo base_url("Classificacoes/exportarXlsx/" . $idEdital . "/" . $idCargo . "?pcd=1") ?>';
});

$(document).ready(function () {
	const tabela = document.getElementById('tabela-paginada');
	const pcdColIndex = tabela.rows[0].cells.length - 2;

	$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
		if (!$('#togglePcd').is(':checked')) return true;
		const valor = (data[pcdColIndex] || '').trim();
		return valor === 'SIM';
	});

	document.getElementById('togglePcd').addEventListener('change', function () {
		const mostrarApenasPcd = this.checked;
		$('#tabela-paginada').DataTable().draw();

		// Alterna visibilidade dos botões
		document.getElementById('btnExportarPlanilha').style.display = mostrarApenasPcd ? 'none' : '';
		document.getElementById('btnExportarPlanilhaPcd').style.display = mostrarApenasPcd ? '' : 'none';
	});
});
</script>
