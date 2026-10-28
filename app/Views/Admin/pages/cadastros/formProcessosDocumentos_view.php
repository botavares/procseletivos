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
						<li class="breadcrumb-item active">Documentos</li>
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
							<button type="button" class="close" data-dismiss="alert"></button>
							<?= esc(session('mensagemError')) ?>
						</div>
					<?php endif; ?>

					<div class="card card-outline card-primary">
						<div class="card-header py-2">
							<a href="<?php echo base_url_adm($returnUrl ?? 'ProcessosDocumentos') ?>" class="btn btn-warning btn-sm mr-2">
								<i class="fas fa-arrow-left"></i> Voltar
							</a>
						</div>
						<div class="card-body">
							<form id="formDocumento" method="POST"
								  action="<?php echo base_url_adm('ProcessosDocumentos/registrar') ?>"
								  enctype="multipart/form-data">
								<input type="hidden" name="acao" id="acao" value="<?= esc($acao) ?>" />
								<input type="hidden" name="pk_id_documento" value="<?= esc($dados->pk_id_documento ?? '') ?>" />
								<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

								<div class="row">
									<div class="form-group col-md-4">
										<label for="fk_processo_referencia">Processo Seletivo <span class="text-danger">*</span></label>
										<select class="form-control" id="fk_processo_referencia" name="fk_processo_referencia" required>
											<option value="">Selecione...</option>
											<?php foreach ($processos as $proc): ?>
												<?php
													$selected = '';
													if (old('fk_processo_referencia')) {
														$selected = old('fk_processo_referencia') == $proc->pk_id_processo ? 'selected' : '';
													} elseif (!empty($dados->fk_processo_referencia)) {
														$selected = $dados->fk_processo_referencia == $proc->pk_id_processo ? 'selected' : '';
													} elseif (!empty($idProcesso)) {
														$selected = $idProcesso == $proc->pk_id_processo ? 'selected' : '';
													}
												?>
												<option value="<?= $proc->pk_id_processo ?>" <?= $selected ?>>
													<?= esc(formatarNumeroEdital(($proc->nm_numero_processo ?? '') . ($proc->nm_ano_processo ?? '')) . ' - ' . $proc->ds_objeto) ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<div class="form-group col-md-4">
										<label for="nm_tipo">Tipo <span class="text-danger">*</span></label>
										<select class="form-control" id="nm_tipo" name="nm_tipo" required>
											<option value="">Selecione...</option>
											<?php foreach (\App\Enums\Admin\TiposDocumentosEnum::cases() as $enum): ?>
												<option value="<?= $enum->value ?>"
													<?= old('nm_tipo', $dados->nm_tipo ?? '') == $enum->value ? 'selected' : '' ?>>
													<?= esc($enum->label()) ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<div class="form-group col-md-2">
										<label for="nm_numero_documento">Nº Documento <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="nm_numero_documento" name="nm_numero_documento"
											   value="<?= old('nm_numero_documento', $dados->nm_numero_documento ?? '') ?>" required />
									</div>

									<div class="form-group col-md-2">
										<label for="nm_ano_documento">Ano Documento <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="nm_ano_documento" name="nm_ano_documento"
											   value="<?= old('nm_ano_documento', $dados->nm_ano_documento ?? '') ?>" required />
									</div>
								</div>

								<div class="row">
									<div class="form-group col-md-6">
										<label for="ds_codigo_oficial">Código Oficial <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="ds_codigo_oficial" name="ds_codigo_oficial"
											   value="<?= old('ds_codigo_oficial', $dados->ds_codigo_oficial ?? '') ?>" required />
									</div>

									<div class="form-group col-md-3" id="grupoVersaoVigente">
										<label for="nm_versao_vigente">Versão Vigente <span class="text-danger">*</span></label>
										<select class="form-control" id="nm_versao_vigente" name="nm_versao_vigente" required>
											<option value="1" <?= old('nm_versao_vigente', $dados->nm_versao_vigente ?? 1) == 1 ? 'selected' : '' ?>>SIM</option>
											<option value="0" <?= old('nm_versao_vigente', $dados->nm_versao_vigente ?? 1) == 0 ? 'selected' : '' ?>>NÃO</option>
										</select>
									</div>

									<div class="form-group col-md-3" id="grupoDocumentoRetificar">
										<label for="nm_documento_retificado">Documento a Retificar</label>
										<select class="form-control" id="nm_documento_retificado" name="nm_documento_retificado">
											<option value="0">Selecione...</option>
											<?php foreach ($processos as $proc): ?>
												<?php
													$selected = '';
													if (old('nm_documento_retificado')) {
														$selected = old('nm_documento_retificado') == $proc->pk_id_processo ? 'selected' : '';
													} elseif (!empty($dados->nm_documento_retificado)) {
														$selected = $dados->nm_documento_retificado == $proc->pk_id_processo ? 'selected' : '';
													}
												?>
												<option value="<?= $proc->pk_id_processo ?>" <?= $selected ?>>
													<?= esc(formatarNumeroEdital(($proc->nm_numero_processo ?? '') . ($proc->nm_ano_processo ?? ''))) ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>

								<div class="row">
									<div class="form-group col-md-6">
										<label for="ds_arquivo">Arquivo (PDF)</label>
										<input type="file" class="form-control-file" id="ds_arquivo" name="ds_arquivo" accept=".pdf" />
										<?php if (!empty($dados->ds_arquivo ?? '')): ?>
											<small class="form-text text-muted">Arquivo atual: <?= esc($dados->ds_arquivo) ?></small>
										<?php endif; ?>
									</div>
								</div>

								<div class="row mt-2">
									<div class="col-md-12">
										<button type="submit" class="btn btn-primary">Salvar</button>
									</div>
								</div>
							</form>

							<script>
								(function () {
									const tipoSelect = document.getElementById('nm_tipo');
									const arquivoInput = document.getElementById('ds_arquivo');
									const grupoVersaoVigente = document.getElementById('grupoVersaoVigente');
									const versaoVigenteSelect = document.getElementById('nm_versao_vigente');
									const grupoRetificar = document.getElementById('grupoDocumentoRetificar');
									const retificarSelect = document.getElementById('nm_documento_retificado');
									const processoSelect = document.getElementById('fk_processo_referencia');
									const numeroDocInput = document.getElementById('nm_numero_documento');
									const anoDocInput = document.getElementById('nm_ano_documento');

									// Dados dos processos seletivos para autopreenchimento
									const processos = <?php echo json_encode(array_map(fn($p) => [
										'id' => (int) $p->pk_id_processo,
										'numero' => (int) $p->nm_numero_processo,
										'ano' => (int) $p->nm_ano_processo,
									], $processos ?? [])) ?>;

									// Tipos que exigem arquivo obrigatorio: 1 (Edital Abertura), 2 (Retificacao), 7 (Republicacao Consolidada)
									const tiposComArquivoObrigatorio = ['1', '2', '7'];
									// Versao vigente so para editais e republicacao consolidada: 1 e 7
									const tiposComVersaoVigente = ['1', '7'];

									function buscarProcesso(id) {
										return processos.find(p => p.id === id) || null;
									}

									function definirReadOnlyCamposDoc(readOnly) {
										numeroDocInput.readOnly = readOnly;
										anoDocInput.readOnly = readOnly;
										if (readOnly) {
											numeroDocInput.classList.add('bg-light');
											anoDocInput.classList.add('bg-light');
										} else {
											numeroDocInput.classList.remove('bg-light');
											anoDocInput.classList.remove('bg-light');
										}
									}

									function preencherCamposDoProcesso() {
										const proc = buscarProcesso(parseInt(processoSelect.value, 10));
										if (proc) {
											numeroDocInput.value = proc.numero;
											anoDocInput.value = proc.ano;
										}
									}

									function ajustarFormulario() {
										const tipo = tipoSelect.value;

										// Arquivo obrigatorio?
										if (tiposComArquivoObrigatorio.includes(tipo)) {
											arquivoInput.setAttribute('required', 'required');
											arquivoInput.closest('.form-group').querySelector('label').innerHTML = 'Arquivo (PDF) <span class="text-danger">*</span>';
										} else {
											arquivoInput.removeAttribute('required');
											arquivoInput.closest('.form-group').querySelector('label').innerHTML = 'Arquivo (PDF)';
										}

										// Versao vigente visivel/aplicavel?
										if (tiposComVersaoVigente.includes(tipo)) {
											grupoVersaoVigente.style.display = '';
											versaoVigenteSelect.setAttribute('required', 'required');
										} else {
											grupoVersaoVigente.style.display = 'none';
											versaoVigenteSelect.removeAttribute('required');
											versaoVigenteSelect.value = '0';
										}

										// Mostrar Documento a Retificar apenas para Retificacao (2)
										if (tipo === '2') {
											grupoRetificar.style.display = '';
											retificarSelect.setAttribute('required', 'required');
										} else {
											grupoRetificar.style.display = 'none';
											retificarSelect.removeAttribute('required');
											retificarSelect.value = '0';
										}

										// Autopreencher e bloquear Nº Documento / Ano Documento para Edital de Abertura (1)
										if (tipo === '1') {
											preencherCamposDoProcesso();
											definirReadOnlyCamposDoc(true);
										} else {
											definirReadOnlyCamposDoc(false);
										}
									}

									processoSelect.addEventListener('change', function () {
										if (tipoSelect.value === '1') {
											preencherCamposDoProcesso();
										}
									});

									tipoSelect.addEventListener('change', ajustarFormulario);
									ajustarFormulario();
								})();
							</script>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>