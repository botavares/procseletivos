<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?php echo esc($titulo ?? 'Processo Seletivo') ?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url_adm('Dashboard') ?>">Home</a></li>
						<li class="breadcrumb-item active">Processos Seletivos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php if (session()->has('mensagemError')): ?>
						<div class="alert alert-danger alert-dismissible fade show mb-3 py-2">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<?php echo esc(session('mensagemError')) ?>
						</div>
					<?php endif; ?>

					<?php if (session()->has('errors')): ?>
						<div class="alert alert-danger alert-dismissible fade show mb-3 py-2">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<ul class="mb-0">
								<?php foreach (session('errors') as $erro): ?>
									<li><?= esc($erro) ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<div class="card card-outline card-primary">
						<div class="card-header">
							<a href="<?php echo base_url_adm('ProcessosSeletivos') ?>" class="btn btn-warning btn-sm">
								<i class="fas fa-arrow-left"></i> Voltar
							</a>
						</div>

						<?php if (session()->has('mensagemSuccess')): ?>
							<div class="alert alert-success alert-dismissible fade show m-3 mb-0 py-2">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<?php echo esc(session('mensagemSuccess')) ?>
							</div>
						<?php endif; ?>

						<div class="card-body">
							<form method="POST" action="<?php echo base_url_adm('ProcessosSeletivos/registrar') ?>">
								<input type="hidden" name="acao" id="acao" value="<?= esc($acao) ?>" />
								<input type="hidden" name="pk_id_processo" value="<?= esc($dados->pk_id_processo ?? '') ?>" />
								<input type="hidden" name="fk_id_secretaria" value="<?= esc(session('fk_id_secretaria')) ?>" />
								<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

								<div class="row">
									<div class="form-group col-md-2">
										<label for="nm_numero_processo">Número do Processo <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="nm_numero_processo" name="nm_numero_processo"
											   value="<?= old('nm_numero_processo', $dados->nm_numero_processo ?? '') ?>"
											   placeholder="Somente números" required>
									</div>
									<div class="form-group col-md-2">
										<label for="nm_ano_processo">Ano <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="nm_ano_processo" name="nm_ano_processo"
											   value="<?= old('nm_ano_processo', $dados->nm_ano_processo ?? '') ?>"
											   placeholder="AAAA" maxlength="4" required>
									</div>
									<div class="form-group col-md-2">
										<label for="nm_meses_validade">Meses de Validade <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="nm_meses_validade" name="nm_meses_validade"
											   value="<?= old('nm_meses_validade', $dados->nm_meses_validade ?? '12') ?>"
											   placeholder="Somente números" required>
									</div>
								</div>

								<div class="row">
									<div class="form-group col-md-12">
										<label for="ds_objeto">Objeto <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="ds_objeto" name="ds_objeto"
											   value="<?= old('ds_objeto', $dados->ds_objeto ?? '') ?>"
											   placeholder="Descrição do objeto do processo seletivo" maxlength="255" required>
									</div>
								</div>

								<div class="row">
									<div class="form-group col-md-3">
										<label for="dt_inicio_inscricao">Início das Inscrições <span class="text-danger">*</span></label>
										<input type="text" class="form-control input-data" id="dt_inicio_inscricao" name="dt_inicio_inscricao"
											   value="<?= old('dt_inicio_inscricao', isset($dados->dt_inicio_inscricao) ? date('d/m/Y', strtotime($dados->dt_inicio_inscricao)) : '') ?>"
											   placeholder="dd/mm/AAAA" required>
									</div>
									<div class="form-group col-md-3">
										<label for="dt_fim_inscricao">Fim das Inscrições <span class="text-danger">*</span></label>
										<input type="text" class="form-control input-data" id="dt_fim_inscricao" name="dt_fim_inscricao"
											   value="<?= old('dt_fim_inscricao', isset($dados->dt_fim_inscricao) ? date('d/m/Y', strtotime($dados->dt_fim_inscricao)) : '') ?>"
											   placeholder="dd/mm/AAAA" required>
									</div>
									<div class="form-group col-md-3">
										<label for="dt_divulga_resultado">Divulgação do Resultado Final</label>
										<input type="text" class="form-control input-data" id="dt_divulga_resultado" name="dt_divulga_resultado"
											   value="<?= old('dt_divulga_resultado', isset($dados->dt_divulga_resultado) ? date('d/m/Y', strtotime($dados->dt_divulga_resultado)) : '') ?>"
											   placeholder="dd/mm/AAAA">
									</div>
								</div>

								<div class="row">
									<div class="form-group col-md-3">
										<label for="nm_situacao">Situação <span class="text-danger">*</span></label>
										<select class="form-control" id="nm_situacao" name="nm_situacao" required>
											<?php foreach (\App\Enums\Admin\SituacaoProcessosEnum::cases() as $enum): ?>
												<option value="<?= $enum->value ?>"
													<?= old('nm_situacao', $dados->nm_situacao ?? '') == $enum->value ? 'selected' : '' ?>>
													<?= esc($enum->label()) ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label for="nm_prorrogado">Prorrogado <span class="text-danger">*</span></label>
										<select class="form-control" id="nm_prorrogado" name="nm_prorrogado" required>
											<?php foreach (\App\Enums\Admin\ProrrogacaoProcessoEnum::cases() as $enum): ?>
												<option value="<?= $enum->value ?>"
													<?= old('nm_prorrogado', $dados->nm_prorrogado ?? '') == $enum->value ? 'selected' : '' ?>>
													<?= esc($enum->label()) ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>

								<div class="card-footer">
									<button type="submit" class="btn btn-primary">Salvar</button>
								</div>

								<?php if (!empty($dados->pk_id_processo)): ?>
									<div class="card-footer bg-light border-top py-2">
										<div class="btn-group" role="group">
											<a href="<?= base_url_adm('ProcessosDocumentos/formularioCadastro/' . $dados->pk_id_processo . '/returnTo/ProcessosSeletivos~formularioAlteracao~' . $dados->pk_id_processo) ?>" class="btn btn-outline-primary btn-sm mr-2">
												<i class="fas fa-file-alt"></i> Documentos
											</a>
											<a href="<?= base_url_adm('Cargos') ?>" class="btn btn-outline-primary btn-sm">
												<i class="fas fa-briefcase"></i> Cargos do Edital
											</a>
										</div>
									</div>
								<?php endif; ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
