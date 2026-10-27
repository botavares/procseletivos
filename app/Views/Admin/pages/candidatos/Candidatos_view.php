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
						<li class="breadcrumb-item"><a href="<?php echo base_url_adm('Dashboard') ?>">Home</a></li>
						<li class="breadcrumb-item active">Candidatos</li>
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
								<a href="<?php echo base_url_adm('Dashboard') ?>" class="btn btn-warning btn-sm mr-2">
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
									<?php foreach ($titulosTabela as $tituloColuna): ?>
										<th class="text-center"><?php echo esc($tituloColuna) ?></th>
									<?php endforeach; ?>
										<th class="text-center">PCD</th>
										<th class="text-center" style="width: 100px;">Exibir Dados</th>
										<th class="text-center" style="width: 100px;">Situação</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($candidatos as $valueCandidato): ?>
										<tr data-pcd="<?= (($valueCandidato['ds_possui_pne'] ?? 0) == 1) ? '1' : '0' ?>">
                                        <td align="center"><?php echo formatarNumeroEdital($valueCandidato['ds_numero_edital']) ?></td>
                                        <td align="center"><?php echo date('d/m/Y', strtotime($valueCandidato['ds_data_cadastro'])) ?></td>
                                        <td align="left" style="min-width: 280px; white-space: nowrap;"><?php echo esc($valueCandidato['ds_nome']) ?></td>
                                        <td align="center"><?php echo date('d/m/Y', strtotime($valueCandidato['ds_nascimento'])) ?></td>
                                        <td align="left"><?php echo mask($valueCandidato['ds_celular'], '(##) #####-####') ?></td>
                                        <td align="left"><?php echo esc($valueCandidato['ds_email']) ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-secondary btn-sm" 
                                               href="<?php echo base_url_adm('Recursos/' . $valueCandidato['fk_id_edital'] . '/' . $valueCandidato['fk_id_cargo'] . '/' . $valueCandidato['pk_id_cadastrado']) ?>"
                                               title="Recursos">
                                                <i class="fas fa-gavel"></i>
                                            </a>
                                        </td>
                                        <td class="text-center"><?= (($valueCandidato['ds_possui_pne'] ?? 0) == 1) ? 'SIM' : 'NÃO' ?></td>
										<td class="text-center">
											<a class="btn btn-info btn-sm" 
											   href="<?php echo base_url_adm('Candidatos/exibirDados/' . $valueCandidato['fk_id_edital'] . '/' . $valueCandidato['fk_id_cargo'] . '/' . $valueCandidato['pk_id_cadastrado']) ?>"
											   title="Exibir Dados">
												<i class="fas fa-user"></i>
											</a>
										</td>
										<td class="text-center">
											<a class="btn btn-primary btn-sm" 
											   href="<?php echo base_url_adm('Candidatos/formSituacaoCandidato/' . $valueCandidato['fk_id_edital'] . '/' . $valueCandidato['fk_id_cargo'] . '/' . $valueCandidato['pk_id_cadastrado']) ?>"
											   title="Situação do Candidato">
												<i class="fas fa-clipboard-check"></i>
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
	window.location.href = '<?php echo base_url_adm("Candidatos/exportarXlsx/" . $idEdital . "/" . $idCargo) ?>';
});

document.getElementById('btnExportarPlanilhaPcd').addEventListener('click', function () {
	window.location.href = '<?php echo base_url_adm("Candidatos/exportarXlsx/" . $idEdital . "/" . $idCargo . "?pcd=1") ?>';
});

$(document).ready(function () {
	// Captura o índice da coluna PCD
	const tabela = document.getElementById('tabela-paginada');
	let pcdColIndex = -1;
	if (tabela.rows.length > 0) {
		const headerCells = tabela.rows[0].cells;
		for (let i = 0; i < headerCells.length; i++) {
			if ((headerCells[i].textContent || '').trim() === 'PCD') {
				pcdColIndex = i;
				break;
			}
		}
	}

	if (pcdColIndex >= 0) {
		$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
			if (!$('#togglePcd').is(':checked')) return true;
			const valor = (data[pcdColIndex] || '').trim();
			return valor === 'SIM';
		});
	}

	document.getElementById('togglePcd').addEventListener('change', function () {
		const mostrarApenasPcd = this.checked;
		$('#tabela-paginada').DataTable().draw();

		// Alterna visibilidade dos botões
		document.getElementById('btnExportarPlanilha').style.display = mostrarApenasPcd ? 'none' : '';
		document.getElementById('btnExportarPlanilhaPcd').style.display = mostrarApenasPcd ? '' : 'none';
	});
});
</script>
