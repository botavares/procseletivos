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
                                Processos Seletivos - Cadastro de Experiências
                            </h3>
                        </div>

                        <!-- TOOLBOX -->
                        <div class="card-toolbox mt-2 mb-2 d-flex flex-row">
                            <div class="mb-2 ml-1 mr-2">
                                <a href="<?= base_url_adm('Experiencias') ?>"
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

                            <form id="formExperiencias"
                                  method="POST"
                                  action="<?= base_url_adm('Experiencias/registrar') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="<?= $acao ?>">
                                <?php if ($acao === 'update' && $dados): ?>
                                    <input type="hidden" name="pk_id_experiencia" value="<?= $dados->pk_id_experiencia ?>">
                                <?php endif; ?>

                                <!-- DADOS DA EXPERIÊNCIA -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Dados da Experiência
                                </h5>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Nome da Experiência *</label>
                                        <input type="text"
                                               class="form-control"
                                               name="ds_nome_experiencia"
                                               value="<?= old('ds_nome_experiencia', $dados->ds_nome_experiencia ?? '') ?>"
                                               required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tipo de Experiência *</label>
                                        <input type="text"
                                               class="form-control"
                                               name="ds_tipo_experiencia"
                                               value="<?= old('ds_tipo_experiencia', $dados->ds_tipo_experiencia ?? '') ?>"
                                               required>
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
