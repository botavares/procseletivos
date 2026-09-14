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
								<button type="button" id="btnExportarPlanilha" class="btn btn-success btn-sm">
									<i class="fas fa-file-excel"></i> Exportar Planilha
								</button>
							</div>
						</div>

						<div class="card-body p-2">
							<table id="tabela-paginada" class="table table-sm table-bordered table-striped table-hover minhaDataTable mb-0">
								<thead class="thead-light">
									<tr>
										<?php foreach ($titulosTabela as $tituloColuna): ?>
											<th class="text-center"><?php echo esc($tituloColuna) ?></th>
										<?php endforeach; ?>
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
												<td class="text-center"><?= esc($classificacao['nr_total_experiencias']) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_graduacao']) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_posgraduacao']) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_mestrado']) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_doutorado']) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_aperfeicoamentos']) ?></td>
												<td class="text-center"><?= date('d/m/Y', strtotime($classificacao['dt_nascimento'])) ?></td>
												<td class="text-center"><?= esc($classificacao['nr_total_pontos']) ?></td>
											<?php endif; ?>

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
</script>
