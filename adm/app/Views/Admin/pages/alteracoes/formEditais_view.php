<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">

                    <div class="card card-outline card-primary shadow-sm">

                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-bullhorn mr-2"></i>
                                <?= esc($titulo) ?>
                            </h3>

                        </div>
						<!-- TOOLBOX -->
						<div class="card-toolbox mt-2 mb-2 d-flex flex-row ">
							<div class="mb-2 ml-1 mr-2">
								<a href="<?= site_url('Editais') ?>"
								class="btn btn-warning btn-sm">
									<i class="fas fa-arrow-left mr-1"></i> Voltar
								</a>
							</div>
							<!-- COLOCAR AQUI OUTROS BOTÕES SE PRECISAR UTILIZANDO A FORMATAÇÃO DO BOTOÂ DE CIMA-->
						</div>

                        <!-- ERROS -->
                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    <?php foreach (session('errors') as $erro): ?>
                                        <li><?= esc($erro) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- BODY -->
                        <div class="card-body">

                            <form id="formEditais"
                                  method="POST"
                                  action="<?= site_url('Editais/registrar') ?>"
                                  enctype="multipart/form-data">

                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="pk_id_edital" value="<?= set_value('pk_id_edital', $dados->pk_id_edital)  ?>">
                                <!-- DADOS DO EDITAL -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Dados do Edital
                                </h5>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Número do Edital *</label>
                                        <input type="text"
                                               class="form-control edital"
                                               name="ds_numero_edital"
                                               value="<?= set_value('ds_numero_edital', $dados->ds_numero_edital) ?>"
                                               readonly>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Data de Início *</label>
                                        <input type="text"
                                               class="form-control input-data"
                                               name="ds_data_inicial"
                                               value="<?= set_value('ds_data_inicial', date('d/m/Y', strtotime($dados->ds_data_inicial))) ?>"
                                               required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Data de Término *</label>
                                        <input type="text"
                                               class="form-control input-data"
                                               name="ds_data_termino"
                                               value="<?=set_value('ds_data_termino', date('d/m/Y', strtotime($dados->ds_data_termino)))?>"
                                               required>
                                    </div>
                                </div>

                                <hr>

                                <!-- cargos -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-book mr-1"></i>
                                    cargos Vinculados
                                </h5>
								<?php
									$totalcargos   = count($cargos);
									$colunas       = 4;
									$porColuna     = (int) ceil($totalcargos / $colunas);

									$colunascargos = array_chunk($cargos, $porColuna);
									$oldcargos = old('ds_cargos')
                                        ?? ($cargosEditais ?? []);
								?>

                                <div class="row">
    								<?php foreach ($colunascargos as $coluna): ?>
										<div class="col-lg-3 col-md-6 col-sm-12">
											<?php foreach ($coluna as $curso): ?>
												<div class="custom-control custom-checkbox mb-2">
													<input type="checkbox"
														class="custom-control-input"
														id="curso<?= $curso->pk_id_cargo ?>"
														name="ds_cargos[]"
														value="<?= $curso->pk_id_cargo ?>"
														<?= in_array($curso->pk_id_cargo, $oldcargos) ? 'checked' : '' ?>>

													<label class="custom-control-label"
														for="curso<?= $curso->pk_id_cargo ?>">
														<?= esc($curso->ds_nome_cargo) ?>
													</label>
												</div>
											<?php endforeach; ?>
										</div>
    								<?php endforeach; ?>
								</div>


                                <hr>

                                <!-- ARQUIVO -->
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-file-pdf text-danger mr-1"></i>
                                        Arquivo do Edital (PDF)
                                    </label>
									<?php if ($dados->ds_arquivo_edital): ?>
                                        <p class="mb-2">
                                            Arquivo atual:
                                            <a class="blue" href="<?= base_url('Editais/obterEdital/' . $dados->ds_arquivo_edital) ?>"
                                               target="_blank">
                                                <?= esc($dados->ds_arquivo_edital) ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file"
                                           class="form-control-file"
                                           name="ds_arquivos"
                                           accept="application/pdf">
                                </div>

                                <hr>

                                <!-- STATUS -->
                                <div class="form-group col-md-4 p-0">
                                    <label>Status *</label>
                                    <select class="form-control" name="ds_status" required>
                                        <option value="1" <?= $dados->ds_status == 1 ? 'selected' : '' ?>>ATIVO</option>
                                        <option value="0" <?= $dados->ds_status == 0 ? 'selected' : '' ?>>INATIVO</option>
                                    </select>
                                </div>

                                <!-- FOOTER -->
                                <div class="text-right mt-4">
                                    <button type="reset" class="btn btn-secondary mr-2">
                                        <i class="fas fa-eraser"></i> Limpar
                                    </button>

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Salvar
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>
<div class="modal fade" id="modalErros" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span id="nome-erro"></span>
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p id="descricao-erro" class="mb-0"></p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">
                    Fechar
                </button>
            </div>

        </div>
    </div>
</div>

