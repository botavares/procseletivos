<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?= esc($titulo) ?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?= base_url_adm('Dashboard') ?>">Home</a></li>
						<li class="breadcrumb-item active">Processos × Cargos</li>
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

					<!-- METADE SUPERIOR: CARGOS DISPONÍVEIS -->
					<div class="card card-outline card-primary">
						<div class="card-header d-flex justify-content-between align-items-center py-2">
							<div>
								<h3 class="card-title m-0">
									<i class="fas fa-briefcase mr-2"></i>
									<?php if (!empty($processo)): ?>
										Vincular cargos ao Processo
										<strong><?= esc(formatarNumeroEdital(($processo->nm_numero_processo ?? '') . ($processo->nm_ano_processo ?? ''))) ?></strong>
									<?php else: ?>
										Vincular cargos ao Processo Seletivo
									<?php endif; ?>
								</h3>
							</div>
							<div class="d-flex">
								<a href="<?= base_url_adm($returnUrl ?? 'ProcessosSeletivos') ?>" class="btn btn-warning btn-sm mr-2">
									<i class="fas fa-arrow-left"></i> Voltar
								</a>
							</div>
						</div>

						<div class="card-body p-2">
							<?php if (empty($cargos)): ?>
								<div class="alert alert-info mb-0">
									<i class="fas fa-info-circle mr-1"></i>
									Nenhum cargo disponível para vincular nesta secretaria.
								</div>
							<?php else: ?>
								<table id="tabela-paginada" class="table table-sm table-bordered table-striped table-hover minhaDataTable mb-0">
									<thead class="thead-light">
										<tr>
											<th class="text-left">Nome do Cargo</th>
											<th class="text-center" style="width: 120px;">Escolher</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($cargos as $cargo): ?>
											<tr>
												<td class="align-middle"><?= esc($cargo->ds_nome_cargo) ?></td>
												<td class="text-center align-middle">
													<a href="<?= base_url_adm('ProcessosCargos/formularioCadastro/' . ($processo->pk_id_processo ?? 0) . '/' . $cargo->pk_id_cargo . '/returnTo/ProcessosCargos~vincular~' . ($processo->pk_id_processo ?? 0) . '~from~' . ($from ?? 'lista')) ?>"
													   class="btn btn-success btn-sm"
													   title="Vincular cargo">
														<i class="fas fa-plus"></i> Escolher
													</a>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php endif; ?>
						</div>
					</div>

					<!-- METADE INFERIOR: CARGOS VINCULADOS -->
					<div class="card card-outline card-success mt-4">
						<div class="card-header py-2">
							<h3 class="card-title m-0">
								<i class="fas fa-check-circle mr-2"></i>
								Cargos Vinculados
							</h3>
						</div>

						<div class="card-body p-2">
							<?php if (empty($vinculados)): ?>
								<div class="alert alert-info mb-0">
									<i class="fas fa-info-circle mr-1"></i>
									Nenhum cargo vinculado a este processo.
								</div>
							<?php else: ?>
								<div class="table-responsive">
									<table class="table table-sm table-bordered table-striped table-hover mb-0">
										<thead class="thead-light">
											<tr>
												<th class="text-left">Cargo</th>
												<th class="text-center">Vagas</th>
												<th class="text-center">Vagas PCD</th>
												<th class="text-center">C. Horária</th>
												<th class="text-right">Remuneração</th>
												<th class="text-center" style="width: 120px;">Alterar</th>
												<th class="text-center" style="width: 170px;">Adicionar Classificatórios</th>
												<th class="text-center" style="width: 120px;">Excluir</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($vinculados as $v): ?>
												<tr>
													<td class="align-middle"><?= esc($v->ds_nome_cargo) ?></td>
													<td class="text-center align-middle"><?= esc($v->nm_vagas) ?></td>
													<td class="text-center align-middle"><?= esc($v->nm_vagas_pcd ?? '-') ?></td>
													<td class="text-center align-middle">
														<?= esc($v->nm_carga_horaria) ?>
														<?= ((int) ($v->nm_tipo_carga_horaria ?? 0) === 1) ? ' semanais' : ' mensais' ?>
													</td>
													<td class="text-right align-middle"><?= esc(number_format((float) ($v->nm_remuneracao ?? 0), 2, ',', '.')) ?></td>
													<td class="text-center align-middle">
														<a href="<?= base_url_adm('ProcessosCargos/formularioAlteracao/' . $v->pk_id_processo_cargo . '/returnTo/ProcessosCargos~vincular~' . ($processo->pk_id_processo ?? 0) . '~from~' . ($from ?? 'lista')) ?>"
														   class="btn btn-info btn-sm"
														   title="Alterar vínculo">
															<i class="fas fa-edit"></i>
														</a>
													</td>
													<td class="text-center align-middle">
														<a href="<?= base_url_adm('ProcessosCargos/classificatorios/' . ($processo->pk_id_processo ?? 0) . '/' . $v->fk_id_cargo) ?>"
														   class="btn btn-success btn-sm"
														   title="Gerenciar dados classificatórios do cargo">
															<i class="fas fa-clipboard-list mr-1"></i> Classificatórios
														</a>
													</td>
													<td class="text-center align-middle">
														<button type="button"
																class="btn btn-danger btn-sm deleteItem"
																data-id="<?= $v->pk_id_processo_cargo ?>"
																title="Excluir vínculo">
																	<i class="fas fa-trash"></i>
																</button>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php endif; ?>
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
			<form method="POST" action="<?= base_url_adm('ProcessosCargos/deletar') ?>">
				<div class="modal-header py-2 bg-danger">
					<h5 class="modal-title text-white" id="modalLabel">
						<i class="fas fa-exclamation-triangle mr-2"></i>Excluir
					</h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body py-3">
					<input id="chavePrimaria" type="hidden" name="chavePrimaria" />
					<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
					<p class="mb-0">Deseja realmente excluir o vínculo deste cargo?</p>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger btn-sm botao-refresh">Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(function () {
		$('.deleteItem').on('click', function () {
			var id = $(this).data('id');
			$('#chavePrimaria').val(id);
			$('#modalDeleteItens').modal('show');
		});
	});
</script>
