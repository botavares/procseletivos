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
						<li class="breadcrumb-item active">Recursos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-primary">
						<div class="card-header d-flex justify-content-start align-items-center py-2">
							<div class="d-flex">
								<a href="<?php echo base_url('Dashboard') ?>" class="btn btn-warning btn-sm mr-2">
									<i class="fas fa-arrow-left"></i> Voltar
								</a>
							</div>
						</div>

						<div class="card-body p-2">
							<table id="tabela-paginada" class="table table-sm table-bordered table-striped table-hover minhaDataTable mb-0">
								<thead class="thead-light">
									<tr>
										<?php foreach ($titulosTabela as $tituloColuna): ?>
											<th class="text-center"><?php echo esc($tituloColuna) ?></th>
										<?php endforeach; ?>
										<th class="text-center" style="width: 120px;">Aplicar Recurso</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($candidatos as $valueCandidato): ?>
										<tr>
											<td align="center"><?php echo formatarNumeroEdital($valueCandidato['ds_numero_edital']) ?></td>
											<td align="center"><?php echo date('d/m/Y', strtotime($valueCandidato['ds_data_cadastro'])) ?></td>
											<td align="left" style="min-width: 280px; white-space: nowrap;"><?php echo esc($valueCandidato['ds_nome']) ?></td>
											<td align="center"><?php echo date('d/m/Y', strtotime($valueCandidato['ds_nascimento'])) ?></td>
											<td align="left"><?php echo mask($valueCandidato['ds_celular'], '(##) #####-####') ?></td>
											<td align="left"><?php echo esc($valueCandidato['ds_email']) ?></td>
											<td align="center"><?php echo esc($valueCandidato['ds_protocolo']) ?></td>
											<td class="text-center">
												<a class="btn btn-success btn-sm" 
												   href="<?php echo base_url('Recursos/' . $valueCandidato['fk_id_edital'] . '/' . $valueCandidato['fk_id_cargo'] . '/' . $valueCandidato['pk_id_cadastrado']) ?>"
												   title="Aplicar Recurso">
													<i class="fas fa-gavel"></i>
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
