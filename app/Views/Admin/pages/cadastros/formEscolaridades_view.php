<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                Processos Seletivos - Cadastro de Escolaridades
                            </h3>
                        </div>
                        <div class="card-toolbox mt-2 mb-2 d-flex flex-row">
                            <div class="mb-2 ml-1 mr-2">
                                <a href="<?= base_url_adm('Escolaridades') ?>"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                                </a>
                            </div>
                        </div>
                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    <?php foreach (session('errors') as $erro): ?>
                                        <li><?= esc($erro) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <form id="formEscolaridades"
                                  method="POST"
                                  action="<?= base_url_adm('Escolaridades/registrar') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="<?= $acao ?>">
                                <?php if ($acao === 'update' && $dados): ?>
                                    <input type="hidden" name="pk_id_escolaridade" value="<?= $dados->pk_id_escolaridade ?>">
                                <?php endif; ?>
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Dados da Escolaridade
                                </h5>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Nome da Escolaridade *</label>
                                        <input type="text"
                                               class="form-control"
                                               name="ds_nome_escolaridade"
                                               value="<?= old('ds_nome_escolaridade', $dados->ds_nome_escolaridade ?? '') ?>"
                                               required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Nível *</label>
                                        <select class="form-control" name="fk_id_nivel" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($niveis as $nivel): ?>
                                                <option value="<?= $nivel->pk_id_niveis_escolaridade ?>"
                                                    <?= old('fk_id_nivel', $dados->fk_id_nivel ?? '') == $nivel->pk_id_niveis_escolaridade ? 'selected' : '' ?>>
                                                    <?= esc($nivel->ds_nome) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Instrução</label>
                                        <textarea class="form-control" name="ds_instrucao" rows="3"><?= old('ds_instrucao', $dados->ds_instrucao ?? '') ?></textarea>
                                    </div>
                                </div>
                                <hr>
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
