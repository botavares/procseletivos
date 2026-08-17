<?php
use App\Enums\Escolaridades\EnumEscolaridades;
?>
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">

                    <div class="card card-outline card-primary shadow-sm">

                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-briefcase mr-2"></i>
                                <?= esc($titulo) ?>
                            </h3>
                        </div>

                        <!-- TOOLBOX -->
                        <div class="card-toolbox mt-2 mb-2 d-flex flex-row">
                            <div class="mb-2 ml-1 mr-2">
                                <a href="<?= site_url('Cargos') ?>"
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

                            <form id="formCargos"
                                  method="POST"
                                  action="<?= site_url('Cargos/registrar') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="pk_id_cargo" value="<?= set_value('pk_id_cargo', $dados->pk_id_cargo ?? '') ?>">

                                <!-- DADOS DO CARGO -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Dados do Cargo
                                </h5>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Nome do Cargo *</label>
                                        <input type="text"
                                               class="form-control"
                                               name="ds_nome_cargo"
                                               value="<?= set_value('ds_nome_cargo', $dados->ds_nome_cargo ?? '') ?>"
                                               required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Carga Horária Semanal *</label>
                                        <input type="text"
                                               class="form-control"
                                               name="ds_carga_horaria"
                                               value="<?= set_value('ds_carga_horaria', $dados->ds_carga_horaria ?? '') ?>"
                                               required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Nível de Escolaridade *</label>
                                        <select class="form-control" name="ds_nivel" required>
                                            <option value="">Selecione</option>
                                            <option value="<?= EnumEscolaridades::FUNDAMENTAL->value ?>"
                                                <?= (set_value('ds_nivel', $dados->ds_nivel ?? '') == EnumEscolaridades::FUNDAMENTAL->value) ? 'selected' : '' ?>>
                                                Ensino Fundamental
                                            </option>
                                            <option value="<?= EnumEscolaridades::ENSINOMEDIOTECNICO->value ?>"
                                                <?= (set_value('ds_nivel', $dados->ds_nivel ?? '') == EnumEscolaridades::ENSINOMEDIOTECNICO->value) ? 'selected' : '' ?>>
                                                Ensino Médio ou Técnico
                                            </option>
                                            <option value="<?= EnumEscolaridades::GRADUACAO->value ?>"
                                                <?= (set_value('ds_nivel', $dados->ds_nivel ?? '') == EnumEscolaridades::GRADUACAO->value) ? 'selected' : '' ?>>
                                                Ensino Superior
                                            </option>
                                            <option value="<?= EnumEscolaridades::LATUSENSO->value ?>"
                                                <?= (set_value('ds_nivel', $dados->ds_nivel ?? '') == EnumEscolaridades::LATUSENSO->value) ? 'selected' : '' ?>>
                                                Pós-graduação
                                            </option>
                                            <option value="<?= EnumEscolaridades::MESTRADO->value ?>"
                                                <?= (set_value('ds_nivel', $dados->ds_nivel ?? '') == EnumEscolaridades::MESTRADO->value) ? 'selected' : '' ?>>
                                                Mestrado
                                            </option>
                                            <option value="<?= EnumEscolaridades::DOUTORADO->value ?>"
                                                <?= (set_value('ds_nivel', $dados->ds_nivel ?? '') == EnumEscolaridades::DOUTORADO->value) ? 'selected' : '' ?>>
                                                Doutorado
                                            </option>
                                        </select>
                                    </div>
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
