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
													<?= esc($proc->nm_numero_processo . '/' . $proc->nm_ano_processo . ' - ' . $proc->ds_objeto) ?>
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

									<div class="form-group col-md-6">
										<label for="ds_link">Link</label>
										<input type="url" class="form-control" id="ds_link" name="ds_link"
											   value="<?= old('ds_link', $dados->ds_link ?? '') ?>" />
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

									<div class="form-group col-md-2">
										<label for="nm_versao_vigente">Versão Vigente</label>
										<input type="number" class="form-control" id="nm_versao_vigente" name="nm_versao_vigente" min="0"
											   value="<?= old('nm_versao_vigente', $dados->nm_versao_vigente ?? 0) ?>" />
									</div>

									<div class="form-group col-md-4">
										<label for="nm_documento_retificado">Documento Retificado (ID)</label>
										<input type="number" class="form-control" id="nm_documento_retificado" name="nm_documento_retificado" min="0"
											   value="<?= old('nm_documento_retificado', $dados->nm_documento_retificado ?? 0) ?>" />
									</div>
								</div>

								<div class="row mt-2">
									<div class="col-md-12">
										<button type="submit" class="btn btn-primary">Salvar</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
