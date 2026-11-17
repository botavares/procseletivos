<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?php echo esc($titulo) ?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url_adm('Dashboard') ?>">Home</a></li>
						<li class="breadcrumb-item active">Convocados</li>
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
						<div class="card-header d-flex justify-content-between align-items-center py-2">
							<div>
								<a href="<?php echo base_url_adm('Dashboard') ?>" class="btn btn-warning btn-sm">
									<i class="fas fa-arrow-left"></i> Voltar
								</a>
							</div>
							<div class="text-right small">
								<strong>Cargo:</strong> <?= esc($nomeCargo) ?>
							</div>
						</div>

						<form method="post" action="<?php echo base_url_adm('Convocados/convocarCandidato') ?>">
							<input type="hidden" name="idProcesso" value="<?php echo (int) $idProcesso ?>" />
							<input type="hidden" name="idCargo" value="<?php echo (int) $idCargo ?>" />
							<input type="hidden" name="idConvocacao" value="<?php echo (int) $idConvocacao ?>" />
							<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

							<div class="card-body p-2">
								<div class="table-responsive">
									<table id="tabela-paginada" class="table table-sm table-bordered table-striped table-hover minhaDataTable mb-2">
										<thead class="thead-light">
											<tr>
												<?php foreach ($titulosTabela as $tituloColuna): ?>
													<th class="text-center"><?php echo esc($tituloColuna) ?></th>
												<?php endforeach; ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($candidatos as $candidato): ?>
								<?php
									$idCandidato = (int) $candidato['fk_id_candidato'];
									$ehTitular   = isset($titulares[$idCandidato]);
									$ehSuplente  = isset($suplentes[$idCandidato]);
								?>
												<tr>
													<td class="text-center"><?= esc($candidato['ds_posicao'] ?? '-') ?></td>
													<td><?= esc($candidato['ds_nome_candidato'] ?? '-') ?></td>
													<td class="text-center">
														<div class="custom-control custom-checkbox">
															<input type="checkbox" class="custom-control-input check-titular" id="titular-<?= $idCandidato ?>" name="titular[]" value="<?= $idCandidato ?>" <?php if ($ehTitular) { echo 'checked'; } ?> />
															<label class="custom-control-label" for="titular-<?= $idCandidato ?>"></label>
														</div>
													</td>
													<td class="text-center">
														<div class="custom-control custom-checkbox">
															<input type="checkbox" class="custom-control-input check-suplente" id="suplente-<?= $idCandidato ?>" name="suplente[]" value="<?= $idCandidato ?>" <?php if ($ehSuplente) { echo 'checked'; } ?> />
															<label class="custom-control-label" for="suplente-<?= $idCandidato ?>"></label>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>

								<div class="d-flex justify-content-between align-items-center">
									<p class="text-muted mb-0 small">
										<i class="fas fa-info-circle mr-1"></i>
										Marque <strong>Titular</strong> ou <strong>Suplente</strong> para cada candidato. O mesmo candidato não pode ser ambos.
									</p>
									<button type="submit" class="btn btn-primary btn-sm">
										<i class="fas fa-bullhorn mr-1"></i> Convocar
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<script>
$(document).ready(function () {
    $('.check-titular').on('change', function () {
        if ($(this).is(':checked')) {
            var id = $(this).val();
            $('#suplente-' + id).prop('checked', false);
        }
    });

    $('.check-suplente').on('change', function () {
        if ($(this).is(':checked')) {
            var id = $(this).val();
            $('#titular-' + id).prop('checked', false);
        }
    });
});
</script>
