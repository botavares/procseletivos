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

			<?php if (session()->has('mensagemSuccess')): ?>
				<div class="alert alert-success alert-dismissible fade show mb-3 py-2">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?= esc(session('mensagemSuccess')) ?>
				</div>
			<?php endif; ?>

			<div class="row">
				<!-- COLUNA ESQUERDA: CARDS INFORMATIVOS FIXOS -->
				<div class="col-lg-4 col-md-12">
					<div class="sticky-top" style="top: 10px; z-index: 1020;">

						<!-- CARD 1: DADOS DO PROCESSO -->
						<div class="card card-outline card-primary mb-3">
							<div class="card-header py-2">
								<h6 class="mb-0 font-weight-bold">
									<i class="fas fa-info-circle mr-1"></i> Dados do Processo
								</h6>
							</div>
							<div class="card-body p-2">
								<table class="table table-sm table-borderless mb-0">
									<tbody>
										<tr>
											<td class="py-1" style="width: 45%;"><strong>Número:</strong></td>
											<td class="py-1" id="preview-numero-processo">
												<?php if (!empty($dados->nm_numero_processo) && !empty($dados->nm_ano_processo)): ?>
													<?= esc(formatarNumeroEdital(($dados->nm_numero_processo ?? '') . ($dados->nm_ano_processo ?? ''))) ?>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="py-1"><strong>Início Inscrições:</strong></td>
											<td class="py-1" id="preview-inicio-inscricao">
												<?php if (!empty($dados->dt_inicio_inscricao)): ?>
													<?= esc(date('d/m/Y', strtotime($dados->dt_inicio_inscricao))) ?>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="py-1"><strong>Fim Inscrições:</strong></td>
											<td class="py-1" id="preview-fim-inscricao">
												<?php if (!empty($dados->dt_fim_inscricao)): ?>
													<?= esc(date('d/m/Y', strtotime($dados->dt_fim_inscricao))) ?>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="py-1"><strong>Situação:</strong></td>
											<td class="py-1" id="preview-situacao">
												<?php if (!empty($situacaoLabel)): ?>
													<span class="badge badge-info"><?= esc($situacaoLabel) ?></span>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<!-- CARD 2: CARGOS -->
						<div class="card card-outline card-secondary mb-3">
							<div class="card-header py-2">
								<h6 class="mb-0 font-weight-bold">
									<i class="fas fa-briefcase mr-1"></i> Cargos
								</h6>
							</div>
							<div class="card-body p-2">
								<?php if (empty($cargos)): ?>
									<p class="text-muted mb-0 small">Nenhum cargo vinculado.</p>
								<?php else: ?>
									<ul class="list-group list-group-flush">
										<?php foreach ($cargos as $cargo): ?>
											<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-start">
												<span class="small text-truncate mr-2" style="max-width: 75%;" title="<?= esc($cargo->ds_nome_cargo ?? '') ?>">
													<i class="fas fa-briefcase text-secondary mr-1"></i>
													<?= esc($cargo->ds_nome_cargo ?? 'Cargo não informado') ?>
												</span>
												<span class="badge badge-light border small">
													<?= (int) ($cargo->nm_vagas ?? 0) ?> vaga(s)
												</span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</div>

						<!-- CARD 3: DOCUMENTOS -->
						<div class="card card-outline card-info mb-3">
							<div class="card-header py-2">
								<h6 class="mb-0 font-weight-bold">
									<i class="fas fa-file-alt mr-1"></i> Documentos
								</h6>
							</div>
							<div class="card-body p-2">
								<?php if (empty($documentos)): ?>
									<p class="text-muted mb-0 small">Nenhum documento registrado.</p>
								<?php else: ?>
									<ul class="list-group list-group-flush">
										<?php foreach ($documentos as $doc): ?>
											<?php
											$tipoLabel = $doc->tipo_label ?? 'Documento';
											$numeroDoc = esc(($doc->nm_numero_documento ?? '') . '/' . ($doc->nm_ano_documento ?? ''));
											$pasta = pasta_tipo_documento((int) ($doc->nm_tipo ?? 0));
											$caminhoArquivo = WRITEPATH . 'uploads/editais/' . $pasta . '/' . ($doc->ds_arquivo ?? '');
											$existeArquivo = !empty($doc->ds_arquivo) && file_exists($caminhoArquivo) && is_file($caminhoArquivo);
											?>
											<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-start">
												<div class="small text-truncate mr-2" style="max-width: 75%;" title="<?= esc($tipoLabel) ?> <?= $numeroDoc ?>">
													<i class="fas fa-file-alt text-primary mr-1"></i>
													<?= esc($tipoLabel) ?> <small class="text-muted"><?= $numeroDoc ?></small>
												</div>
												<?php if ($existeArquivo): ?>
													<a href="<?= base_url('ProcessosSeletivos/obterEdital/' . $pasta . '/' . rawurlencode($doc->ds_arquivo)) ?>"
													   target="_blank"
													   class="btn btn-xs btn-outline-primary"
													   title="Visualizar arquivo">
														<i class="fas fa-eye"></i>
													</a>
												<?php else: ?>
													<a href="<?= url_diario_oficial($doc->ds_codigo_oficial ?? '') ?>"
													   target="_blank"
													   class="btn btn-xs btn-outline-secondary"
													   title="Abrir no Diário Oficial">
														<i class="fas fa-external-link-alt"></i>
													</a>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</div>

						<!-- CARD 4: AÇÕES (aparece após salvar/submit) -->
						<div class="card card-outline card-primary mb-3 <?= empty($dados->pk_id_processo) ? 'd-none' : '' ?>" id="card-acoes-processo">
							<div class="card-header py-2">
								<h6 class="mb-0 font-weight-bold">
									<i class="fas fa-cogs mr-1"></i> Ações
								</h6>
							</div>
							<div class="card-body p-2">
								<div class="d-flex flex-column" style="gap: 6px;">
									<?php if (!empty($dados->pk_id_processo)): ?>
										<a href="<?= base_url_adm('ProcessosDocumentos/formularioCadastro/' . $dados->pk_id_processo . '/from/alteracao') ?>"
										   class="btn btn-outline-primary btn-sm">
											<i class="fas fa-file-alt mr-1"></i> Documentos
										</a>
										<a href="<?= base_url_adm('ProcessosCargos/vincular/' . $dados->pk_id_processo . '/from/alteracao') ?>"
										   class="btn btn-outline-success btn-sm">
											<i class="fas fa-briefcase mr-1"></i> Cargos do Processo
										</a>
									<?php endif; ?>
									<a href="<?php echo base_url_adm('ProcessosSeletivos') ?>"
									   class="btn btn-warning btn-sm">
										<i class="fas fa-arrow-left mr-1"></i> Voltar
									</a>
								</div>
							</div>
						</div>

					</div>
				</div>

				<!-- COLUNA DIREITA: FORMULÁRIO -->
				<div class="col-lg-8 col-md-12">
					<div class="card card-outline card-primary">
						<div class="card-header">
							<a href="<?php echo base_url_adm('ProcessosSeletivos') ?>" class="btn btn-warning btn-sm">
								<i class="fas fa-arrow-left"></i> Voltar
							</a>
						</div>

						<div class="card-body">
							<form id="formProcessoSeletivo" method="POST" action="<?php echo base_url_adm('ProcessosSeletivos/registrar') ?>">
								<input type="hidden" name="acao" id="acao" value="<?= esc($acao) ?>" />
								<input type="hidden" name="pk_id_processo" value="<?= esc($dados->pk_id_processo ?? '') ?>" />
								<input type="hidden" name="fk_id_secretaria" value="<?= esc(session('fk_id_secretaria')) ?>" />
								<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

								<div class="row">
									<div class="form-group col-md-2">
										<label for="nm_numero_processo">Número do Processo <span class="text-danger">*</span></label>
										<input type="text" class="form-control preview-trigger" id="nm_numero_processo" name="nm_numero_processo"
											   value="<?= old('nm_numero_processo', $dados->nm_numero_processo ?? '') ?>"
											   placeholder="Somente números" required>
									</div>
									<div class="form-group col-md-2">
										<label for="nm_ano_processo">Ano <span class="text-danger">*</span></label>
										<input type="text" class="form-control preview-trigger" id="nm_ano_processo" name="nm_ano_processo"
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
										<input type="text" class="form-control input-data preview-trigger" id="dt_inicio_inscricao" name="dt_inicio_inscricao"
											   value="<?= old('dt_inicio_inscricao', isset($dados->dt_inicio_inscricao) ? date('d/m/Y', strtotime($dados->dt_inicio_inscricao)) : '') ?>"
											   placeholder="dd/mm/AAAA" required>
									</div>
									<div class="form-group col-md-3">
										<label for="dt_fim_inscricao">Fim das Inscrições <span class="text-danger">*</span></label>
										<input type="text" class="form-control input-data preview-trigger" id="dt_fim_inscricao" name="dt_fim_inscricao"
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
										<select class="form-control preview-trigger" id="nm_situacao" name="nm_situacao" required>
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

							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<script>
(function () {
	const form = document.getElementById('formProcessoSeletivo');
	const numeroInput = document.getElementById('nm_numero_processo');
	const anoInput = document.getElementById('nm_ano_processo');
	const inicioInput = document.getElementById('dt_inicio_inscricao');
	const fimInput = document.getElementById('dt_fim_inscricao');
	const situacaoSelect = document.getElementById('nm_situacao');

	const previewNumero = document.getElementById('preview-numero-processo');
	const previewInicio = document.getElementById('preview-inicio-inscricao');
	const previewFim = document.getElementById('preview-fim-inscricao');
	const previewSituacao = document.getElementById('preview-situacao');

	const situacaoLabels = {};
	<?php foreach (\App\Enums\Admin\SituacaoProcessosEnum::cases() as $enum): ?>
		situacaoLabels['<?= $enum->value ?>'] = '<?= esc($enum->label()) ?>';
	<?php endforeach; ?>

	function formatarNumeroEdital(numero, ano) {
		if (!numero || !ano) return '<span class="text-muted">-</span>';
		numero = String(numero).padStart(3, '0');
		ano = String(ano);
		if (ano.length < 4) return '<span class="text-muted">-</span>';
		return escHtml(numero + '/' + ano);
	}

	function escHtml(texto) {
		const div = document.createElement('div');
		div.textContent = texto;
		return div.innerHTML;
	}

	function atualizarPreview() {
		// Número do processo
		previewNumero.innerHTML = formatarNumeroEdital(numeroInput.value, anoInput.value);

		// Datas
		previewInicio.textContent = inicioInput.value || '-';
		previewFim.textContent = fimInput.value || '-';

		// Situação
		const situacaoValor = situacaoSelect.value;
		if (situacaoValor && situacaoLabels[situacaoValor]) {
			previewSituacao.innerHTML = '<span class="badge badge-info">' + escHtml(situacaoLabels[situacaoValor]) + '</span>';
		} else {
			previewSituacao.innerHTML = '<span class="text-muted">-</span>';
		}
	}

	form.querySelectorAll('.preview-trigger').forEach(function (el) {
		el.addEventListener('input', atualizarPreview);
		el.addEventListener('change', atualizarPreview);
	});

	atualizarPreview();
})();
</script>
