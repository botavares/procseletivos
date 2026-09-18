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
										<th class="text-center" style="width: 100px;">Exibir Dados</th>
										<th class="text-center" style="width: 100px;">Situação</th>
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
										<td class="text-center">
											<a class="btn btn-secondary btn-sm" 
											   href="<?php echo base_url('Recursos/' . $valueCandidato['fk_id_edital'] . '/' . $valueCandidato['fk_id_cargo'] . '/' . $valueCandidato['pk_id_cadastrado']) ?>"
											   title="Recursos">
												<i class="fas fa-gavel"></i>
											</a>
										</td>
										<td class="text-center">
											<a class="btn btn-info btn-sm" 
											   href="<?php echo base_url('Candidatos/exibirDados/' . $valueCandidato['fk_id_edital'] . '/' . $valueCandidato['fk_id_cargo'] . '/' . $valueCandidato['pk_id_cadastrado']) ?>"
											   title="Exibir Dados">
												<i class="fas fa-user"></i>
											</a>
										</td>
										<td class="text-center">
											<a class="btn btn-primary btn-sm" 
											   href="<?php echo base_url('Candidatos/formSituacaoCandidato/' . $valueCandidato['fk_id_edital'] . '/' . $valueCandidato['fk_id_cargo'] . '/' . $valueCandidato['pk_id_cadastrado']) ?>"
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
