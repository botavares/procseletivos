<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">

                    <div class="card card-outline card-primary shadow-sm">

                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-link mr-2"></i>
                                <?= esc($titulo) ?>
                            </h3>
                        </div>

                        <!-- TOOLBOX -->
                        <div class="card-toolbox mt-2 mb-2 d-flex flex-row">
                            <div class="mb-2 ml-1 mr-2">
                                <a href="<?= base_url_adm($returnUrl ?? 'ProcessosSeletivos') ?>"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                                </a>
                            </div>
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

                            <form id="formProcessosCargos"
                                  method="POST"
                                  action="<?= base_url_adm('ProcessosCargos/registrar') ?>">

                                <?= csrf_field() ?>

                                <input type="hidden" name="action" value="<?= esc($acao) ?>">
                                <input type="hidden" name="pk_id_processo_cargo" value="<?= set_value('pk_id_processo_cargo', $dados->pk_id_processo_cargo ?? '') ?>">
                                <input type="hidden" name="fk_id_processo" value="<?= set_value('fk_id_processo', $fk_id_processo) ?>">
                                <input type="hidden" name="fk_id_cargo" value="<?= set_value('fk_id_cargo', $fk_id_cargo) ?>">
                                <input type="hidden" name="return_to" value="<?= esc($returnUrl ?? '') ?>">

                                <!-- IDENTIFICAÇÃO -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Identificação
                                </h5>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Processo Seletivo</label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?= esc(formatarNumeroEdital(($processo->nm_numero_processo ?? '') . ($processo->nm_ano_processo ?? ''))) ?>"
                                               readonly>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Cargo Escolhido</label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?= esc($cargo->ds_nome_cargo ?? '') ?>"
                                               readonly>
                                    </div>
                                </div>

                                <hr>

                                <!-- DADOS DO VÍNCULO -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    Dados do Vínculo
                                </h5>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="nm_vagas">Número de Vagas *</label>
                                        <input type="number"
                                               id="nm_vagas"
                                               class="form-control"
                                               name="nm_vagas"
                                               min="0"
                                               step="1"
                                               value="<?= set_value('nm_vagas', $dados->nm_vagas ?? '') ?>"
                                               required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nm_vagas_pcd">Vagas PCD</label>
                                        <input type="number"
                                               id="nm_vagas_pcd"
                                               class="form-control"
                                               name="nm_vagas_pcd"
                                               min="0"
                                               step="1"
                                               value="<?= set_value('nm_vagas_pcd', $dados->nm_vagas_pcd ?? '') ?>">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nm_cadastro_reserva">Cadastro Reserva *</label>
                                        <select id="nm_cadastro_reserva"
                                                class="form-control"
                                                name="nm_cadastro_reserva"
                                                required>
                                            <option value="">Selecione</option>
                                            <option value="1"
                                                <?= set_value('nm_cadastro_reserva', $dados->nm_cadastro_reserva ?? '') == '1' ? 'selected' : '' ?>>
                                                Sim
                                            </option>
                                            <option value="0"
                                                <?= set_value('nm_cadastro_reserva', $dados->nm_cadastro_reserva ?? '') == '0' ? 'selected' : '' ?>>
                                                Não
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="nm_carga_horaria">Carga Horária *</label>
                                        <input type="number"
                                               id="nm_carga_horaria"
                                               class="form-control"
                                               name="nm_carga_horaria"
                                               min="1"
                                               step="1"
                                               value="<?= set_value('nm_carga_horaria', $dados->nm_carga_horaria ?? '') ?>"
                                               required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nm_tipo_carga_horaria">Tipo de Carga Horária *</label>
                                        <select id="nm_tipo_carga_horaria"
                                                class="form-control"
                                                name="nm_tipo_carga_horaria"
                                                required>
                                            <option value="">Selecione</option>
                                            <option value="1"
                                                <?= set_value('nm_tipo_carga_horaria', $dados->nm_tipo_carga_horaria ?? '') == '1' ? 'selected' : '' ?>>
                                                SEMANAIS
                                            </option>
                                            <option value="2"
                                                <?= set_value('nm_tipo_carga_horaria', $dados->nm_tipo_carga_horaria ?? '') == '2' ? 'selected' : '' ?>>
                                                MENSAIS
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nm_remuneracao">Remuneração (R$) *</label>
                                        <input type="text"
                                               id="nm_remuneracao"
                                               class="form-control moeda"
                                               name="nm_remuneracao"
                                               placeholder="R$ 0,00"
                                               value="<?= set_value('nm_remuneracao', isset($dados->nm_remuneracao) ? number_format((float) $dados->nm_remuneracao, 2, ',', '.') : '') ?>"
                                               required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="ds_requisito_provimento">Requisito de Provimento *</label>
                                    <textarea id="ds_requisito_provimento"
                                              class="form-control"
                                              name="ds_requisito_provimento"
                                              rows="3"
                                              maxlength="255"
                                              required><?= set_value('ds_requisito_provimento', $dados->ds_requisito_provimento ?? '') ?></textarea>
                                </div>

                                <hr>

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

<script>
    $(function () {
        $('.moeda').inputmask({
            alias: 'numeric',
            groupSeparator: '.',
            radixPoint: ',',
            digits: 2,
            digitsOptional: false,
            prefix: 'R$ ',
            placeholder: '0',
            autoUnmask: false,
            removeMaskOnSubmit: false
        });
    });
</script>
