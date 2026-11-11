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
							<?php if (session()->has('errors')): ?>
								<ul class="mb-0 pl-3 mt-2">
									<?php foreach (session('errors') as $erro): ?>
										<li><?= esc($erro) ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
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
								<input type="hidden" name="fk_id_secretaria" id="fk_id_secretaria" value="<?= esc(session('fk_id_secretaria') ?? '') ?>" />
								<input type="hidden" name="ds_arquivo_atual" value="<?= esc($dados->ds_arquivo ?? '') ?>" />
								<input type="hidden" name="return_to" value="<?= esc($returnUrl ?? '') ?>" />
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
										</select>
										<small id="avisoSemEdital" class="form-text text-danger" style="display: none;">
											Não existe Edital de Abertura para este processo.
										</small>
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

							<!-- GRID DE DOCUMENTOS DO PROCESSO -->
							<div class="card card-outline card-success mt-4">
								<div class="card-header py-2 d-flex justify-content-between align-items-center">
									<h3 class="card-title m-0">
										<i class="fas fa-file-alt mr-2"></i>
										Documentos do Processo
										<?php if (!empty($processoSelecionado)): ?>
											<strong>
												<?= esc(formatarNumeroEdital(($processoSelecionado->nm_numero_processo ?? '') . ($processoSelecionado->nm_ano_processo ?? ''))) ?>
											</strong>
										<?php endif; ?>
									</h3>
								</div>
								<div class="card-body p-2">
									<?php if (empty($documentosProcesso)): ?>
										<div class="alert alert-info mb-0">
											<i class="fas fa-info-circle mr-1"></i>
											Nenhum documento registrado para este processo.
										</div>
									<?php else: ?>
										<div class="table-responsive">
											<table class="table table-sm table-bordered table-striped table-hover mb-0">
												<thead class="thead-light">
													<tr>
														<th class="text-left">Tipo</th>
														<th class="text-center">Nº/Ano</th>
														<th class="text-left">Código Oficial</th>
														<th class="text-center">Vigente</th>
														<th class="text-center" style="width: 120px;">Visualizar</th>
														<th class="text-center" style="width: 120px;">Alterar</th>
														<th class="text-center" style="width: 120px;">Excluir</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($documentosProcesso as $doc): ?>
														<?php
															$pasta = pasta_tipo_documento((int) ($doc->nm_tipo ?? 0));
															$caminhoArquivo = WRITEPATH . 'uploads/editais/' . $pasta . '/' . ($doc->ds_arquivo ?? '');
															$existeArquivo = !empty($doc->ds_arquivo) && file_exists($caminhoArquivo) && is_file($caminhoArquivo);
														?>
														<tr>
															<td class="align-middle"><?= esc($doc->tipo_label ?? 'Documento') ?></td>
															<td class="text-center align-middle"><?= esc(($doc->nm_numero_documento ?? '-') . '/' . ($doc->nm_ano_documento ?? '-')) ?></td>
															<td class="align-middle"><?= esc($doc->ds_codigo_oficial ?? '-') ?></td>
															<td class="text-center align-middle">
																<?php if ((int) ($doc->nm_versao_vigente ?? 0) === 1): ?>
																	<span class="badge badge-success">Sim</span>
																<?php else: ?>
																	<span class="badge badge-secondary">Não</span>
																<?php endif; ?>
															</td>
															<td class="text-center align-middle">
																<?php if ($existeArquivo): ?>
																	<a href="<?= base_url('ProcessosSeletivos/obterEdital/' . $pasta . '/' . rawurlencode($doc->ds_arquivo)) ?>"
																	   target="_blank"
																	   class="btn btn-outline-primary btn-sm"
																	   title="Visualizar arquivo">
																		<i class="fas fa-eye"></i>
																	</a>
																<?php else: ?>
																	<a href="<?= url_diario_oficial($doc->ds_codigo_oficial ?? '') ?>"
																	   target="_blank"
																	   class="btn btn-outline-secondary btn-sm"
																	   title="Abrir no Diário Oficial">
																		<i class="fas fa-external-link-alt"></i>
																	</a>
																<?php endif; ?>
															</td>
															<td class="text-center align-middle">
																<a href="<?= base_url_adm('ProcessosDocumentos/formularioAlteracao/' . $doc->pk_id_documento) ?>"
																   class="btn btn-info btn-sm"
																   title="Alterar documento">
																	<i class="fas fa-edit"></i>
																</a>
															</td>
															<td class="text-center align-middle">
																<button type="button"
																		class="btn btn-danger btn-sm deleteDocumento"
																		data-id="<?= $doc->pk_id_documento ?>"
																		title="Excluir documento">
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
							<!-- /GRID DE DOCUMENTOS -->

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
									const grupoArquivo = arquivoInput.closest('.form-group');

									// Dados dos processos seletivos para autopreenchimento
									const processos = <?php echo json_encode(array_map(fn($p) => [
										'id' => (int) $p->pk_id_processo,
										'numero' => (int) $p->nm_numero_processo,
										'ano' => (int) $p->nm_ano_processo,
									], $processos ?? [])) ?>;

									// Documentos do processo selecionado para autopreenchimento da retificacao
									const documentosProcesso = <?php echo json_encode(array_map(fn($d) => [
										'id' => (int) $d->pk_id_documento,
										'tipo' => (int) $d->nm_tipo,
										'numero' => (int) $d->nm_numero_documento,
										'ano' => (int) $d->nm_ano_documento,
										'label' => ($d->tipo_label ?? 'Documento'),
									], $documentosProcesso ?? [])) ?>;

									const retificarInicial = <?= json_encode((int) old('nm_documento_retificado', $dados->nm_documento_retificado ?? 0)) ?>;
									const avisoSemEdital = document.getElementById('avisoSemEdital');

									// Tipos que exigem arquivo obrigatorio: 1 (Edital Abertura) e 2 (Retificacao)
									const tiposComArquivoObrigatorio = ['1', '2'];
									// Versao vigente so para edital de abertura
									const tiposComVersaoVigente = ['1'];

									function buscarProcesso(id) {
										return processos.find(p => p.id === id) || null;
									}

									function buscarEditaisDoProcesso() {
										return documentosProcesso.filter(d => d.tipo === 1);
									}

									function atualizarSelectRetificar() {
										retificarSelect.innerHTML = '<option value="0">Selecione...</option>';
										const editais = buscarEditaisDoProcesso();

										if (editais.length === 0) {
											avisoSemEdital.style.display = '';
											retificarSelect.value = '0';
											return;
										}

										avisoSemEdital.style.display = 'none';
										editais.forEach(edital => {
											const option = document.createElement('option');
											option.value = edital.id;
											option.textContent = edital.label + ' ' + edital.numero + '/' + edital.ano;
											retificarSelect.appendChild(option);
										});

										// Prefere valor ja preenchido (old/dados); senao seleciona o ultimo edital
										const idsEditais = editais.map(e => e.id);
										if (retificarInicial > 0 && idsEditais.includes(retificarInicial)) {
											retificarSelect.value = String(retificarInicial);
										} else {
											retificarSelect.value = String(editais[editais.length - 1].id);
										}
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
											grupoArquivo.style.display = '';
											arquivoInput.setAttribute('required', 'required');
											grupoArquivo.querySelector('label').innerHTML = 'Arquivo (PDF) <span class="text-danger">*</span>';
										} else {
											grupoArquivo.style.display = 'none';
											arquivoInput.removeAttribute('required');
											arquivoInput.value = '';
											grupoArquivo.querySelector('label').innerHTML = 'Arquivo (PDF)';
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
											atualizarSelectRetificar();
										} else {
											grupoRetificar.style.display = 'none';
											retificarSelect.removeAttribute('required');
											retificarSelect.value = '0';
											avisoSemEdital.style.display = 'none';
										}

										// Autopreencher e bloquear Nº Documento / Ano Documento para Edital de Abertura (1) e Retificacao (2)
										if (tipo === '1' || tipo === '2') {
											preencherCamposDoProcesso();
											definirReadOnlyCamposDoc(true);
										} else {
											definirReadOnlyCamposDoc(false);
										}
									}

									processoSelect.addEventListener('change', function () {
										if (tipoSelect.value === '1' || tipoSelect.value === '2') {
											preencherCamposDoProcesso();
										}
										if (tipoSelect.value === '2') {
											atualizarSelectRetificar();
										}
									});

									tipoSelect.addEventListener('change', ajustarFormulario);
									ajustarFormulario();
								})();

								// Modal de exclusão de documentos
								$(function () {
									$('.deleteDocumento').on('click', function () {
										var id = $(this).data('id');
										$('#chavePrimariaDocumento').val(id);
										$('#modalDeleteDocumento').modal('show');
									});
								});
							</script>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<!-- Modal de Confirmação de Exclusão de Documento -->
<div class="modal fade" id="modalDeleteDocumento" tabindex="-1" role="dialog" aria-labelledby="modalLabelDocumento" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		<div class="modal-content">
			<form method="POST" action="<?= base_url_adm('ProcessosDocumentos/deletar') ?>">
				<div class="modal-header py-2 bg-danger">
					<h5 class="modal-title text-white" id="modalLabelDocumento">
						<i class="fas fa-exclamation-triangle mr-2"></i>Excluir
					</h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body py-3">
					<input id="chavePrimariaDocumento" type="hidden" name="chavePrimaria" />
					<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
					<p class="mb-0">Deseja realmente excluir este documento?</p>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger btn-sm botao-refresh">Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>