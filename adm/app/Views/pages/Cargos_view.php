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
						<li class="breadcrumb-item active">Serviços</li>
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
<div class="d-flex">
	<a href="<?php echo base_url('Dashboard') ?>" class="btn btn-warning btn-sm mr-2">
		<i class="fas fa-arrow-left"></i> Voltar
	</a>
	<a href="<?php echo base_url('/Cargos/formularioCadastro') ?>" class="btn btn-primary btn-sm">
		<i class="fas fa-plus"></i> Adicionar Cargo
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
										<th class="text-center" style="width: 70px;">Alterar</th>
										<th class="text-center" style="width: 70px;">Excluir</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($cargos as $valueCargos): ?>
										<tr>
											<td><?php echo esc($valueCargos->ds_nome_cargo) ?></td>
											<td class="text-center"><?php echo esc($valueCargos->ds_carga_horaria) ?></td>
											<td class="text-center">
												<a class="btn btn-outline-primary btn-sm" href="<?php echo base_url('/CargosExperiencias/formularioCargosExperiencia/' . $valueCargos->pk_id_cargo) ?>">
													<i class="fas fa-briefcase"></i> Experiências
												</a>
											</td>
											<td class="text-center">
												<a class="btn btn-outline-primary btn-sm" href="<?php echo base_url('/CargosEscolaridades/formularioCargosEscolaridade/' . $valueCargos->pk_id_cargo) ?>">
													<i class="fas fa-graduation-cap"></i> Escolaridade
												</a>
											</td>
											<td class="text-center">
												<a class="btn btn-outline-primary btn-sm" href="<?php echo base_url('/CargosCursos/formularioCargosCurso/' . $valueCargos->pk_id_cargo) ?>">
													<i class="fas fa-book"></i> Cursos
												</a>
											</td>
											<td class="text-center">
												<a class="btn btn-outline-primary btn-sm" href="<?php echo base_url('/CargosCriterios/formularioCargosCriterio/' . $valueCargos->pk_id_cargo) ?>">
													<i class="fas fa-clipboard-check"></i> Critérios
												</a>
											</td>
											<td class="text-center">
												<a class="btn btn-info btn-sm" href="<?php echo base_url('/Cargos/formularioAlteracao/' . $valueCargos->pk_id_cargo) ?>">
													<i class="fas fa-edit"></i>
												</a>
											</td>
											<td class="text-center">
												<button type="button" class="btn btn-danger btn-sm deleteItem" data-id="<?php echo $valueCargos->pk_id_cargo . '|' . $valueCargos->ds_nome_cargo ?>">
													<i class="fas fa-trash"></i>
												</button>
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

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalDeleteItens" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		<div class="modal-content">
			<form method="POST" action="<?php echo base_url('Cargos/deletar') ?>">
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
