<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?php echo esc($titulo) ?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url('Dashboard') ?>">Home</a></li>
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
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<?= esc(session('mensagemError')) ?>
						</div>
					<?php endif; ?>
					<?php if (session()->has('mensagemSuccess')): ?>
						<div class="alert alert-success alert-dismissible fade show mb-3 py-2">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<?= esc(session('mensagemSuccess')) ?>
						</div>
					<?php endif; ?>

					<div class="card card-outline card-primary">
						<div class="card-header d-flex justify-content-between align-items-center py-2">
							<div>
								<?php if (isset($idEdital)): ?>
									<a href="<?php echo base_url('Candidatos/' . $idEdital . '/' . $idCurso) ?>" class="btn btn-warning btn-sm">
										<i class="fas fa-arrow-left"></i> Voltar
									</a>
								<?php else: ?>
									<a href="<?php echo base_url('Dashboard') ?>" class="btn btn-warning btn-sm">
										<i class="fas fa-arrow-left"></i> Voltar
									</a>
								<?php endif; ?>
							</div>
						</div>

						<div class="card-body p-2">
							<table id="tabela-paginada" class="table table-sm table-bordered table-striped table-hover minhaDataTable mb-0">
								<thead class="thead-light">
									<tr>
										<?php foreach ($titulosTabela as $tituloColuna): ?>
											<th class="text-center"><?php echo esc($tituloColuna) ?></th>
										<?php endforeach; ?>
										<th class="text-center" style="width: 110px;">Comparecimento</th>
										<th class="text-center" style="width: 140px;">Contratar/Encaminhar</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($convocados as $valueConvocados): ?
										<?php
											$dataNascimento = date('d/m/Y', strtotime($valueConvocados->ds_nascimento));
											$dataConvocacao = date('d/m/Y', strtotime($valueConvocados->ds_data));
											$statusInteresse = ($valueConvocados->ds_interesse == '1')
												? "<i class='fas fa-thumbs-up text-success' title='Candidato demonstrou interesse pelo email'></i>"
												: "";
										?>
										<tr>
											<td class="text-center"><?php echo $valueConvocados->ds_periodo ?></td>
											<td><?php echo $valueConvocados->ds_nome . ' ' . $statusInteresse ?></td>
											<td class="text-center"><?php echo $dataNascimento ?></td>
											<td><?php echo mask($valueConvocados->ds_celular, '(##) #####-####') ?></td>
											<td><?php echo $valueConvocados->ds_email ?></td>
											<td><?php echo $valueConvocados->ds_nome_curso ?></td>
											<td class="text-center"><?php echo $dataConvocacao ?></td>
											<td class="text-center">
												<div class="custom-control custom-checkbox">
													<input type="checkbox" class="custom-control-input comparecimento" id="comparecimento-<?php echo $valueConvocados->pk_id_candidato ?>" data-id="<?php echo $valueConvocados->pk_id_candidato ?>" <?php if($valueConvocados->ds_comparecimento == '1'){echo 'checked';} ?> />
													<label class="custom-control-label" for="comparecimento-<?php echo $valueConvocados->pk_id_candidato ?>"></label>
												</div>
												<input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
											</td>
											<td class="text-center">
												<a class="btn btn-info btn-sm" href="<?php echo base_url('/Contratos/formContratar/' . $valueConvocados->pk_id_candidato) ?>">
													<i class="fas fa-edit"></i>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							<p class="text-danger mb-0 mt-2 small">
								<i class="fas fa-info-circle mr-1"></i>
								Após 48 horas da data e hora da convocação, o sistema realizará a "desconvocação" automática dos candidatos que não se manifestaram.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalDeleteItens" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		<div class="modal-content">
			<form method="POST" action="<?php echo base_url('Convocados/deletar') ?>">
				<div class="modal-header py-2">
					<h5 class="modal-title" id="modalLabel"><i class="fas fa-exclamation-triangle text-danger mr-2"></i>Excluir</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body py-3">
					<input id="chavePrimaria" type="hidden" name="chavePrimaria" />
					<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
					<p class="mb-0">Deseja realmente excluir <strong id="nomeItem"></strong>?</p>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger btn-sm botao-refresh">Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>
