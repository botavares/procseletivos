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

                            <?php
                                // Normaliza $cargoEscolaridade para objeto, caso venha como array
                                if ($cargoEscolaridade !== null && is_array($cargoEscolaridade)) {
                                    $cargoEscolaridade = (object) $cargoEscolaridade;
                                }

                                // Helper para recuperar valor preenchido (banco > old > default)
                                $val = function($campo, $default = '') use ($cargoEscolaridade) {
                                    if ($cargoEscolaridade && property_exists($cargoEscolaridade, $campo) && $cargoEscolaridade->$campo !== null) {
                                        return $cargoEscolaridade->$campo;
                                    }
                                    return old($campo) ?? $default;
                                };
                            ?>

                            <form id="formEscolaridadesCargo"
                                  method="POST"
                                  action="<?= site_url('CargosEscolaridades/registrarAssociacaoCargoEscolaridade') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="fk_id_cargo" value="<?= esc($cargo->pk_id_cargo) ?>">
                                <input type="hidden" name="acao" value="<?= esc($acao) ?>">



                               

                                <!-- ESCOLARIDADE -->
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Nome da escolaridade</label>
                                        <select class="form-control" name="fk_id_escolaridade" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($escolaridades as $esc): ?>
                                                <?php
                                                    $selected = '';
                                                    if ($cargoEscolaridade && $cargoEscolaridade->fk_id_escolaridade == $esc->pk_id_escolaridade) {
                                                        $selected = 'selected';
                                                    } elseif (old('fk_id_escolaridade') == $esc->pk_id_escolaridade) {
                                                        $selected = 'selected';
                                                    }
                                                ?>
                                                <option value="<?= esc($esc->pk_id_escolaridade) ?>" <?= $selected ?>>
                                                    <?= esc($esc->ds_nome_escolaridade) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                 


                                <!-- QUANTIDADE MÍNIMA / MÁXIMA -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Pontuação por cada escolaridade</label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_minima"
                                               id="pontuacao_minima"
                                               value="<?= esc($val('ds_pontuacao_minima')) ?>"
                                               min="0">
                                        <small class="form-text text-muted">
                                            Insira qual é a pontuação mínima que o candidato pode obter com essa escolaridade.
                                        </small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Pontuação máxima </label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_maxima"
                                               id="pontuacao_maxima"
                                               value="<?= esc($val('ds_pontuacao_maxima')) ?>"
                                               min="0">
                                        <small class="form-text text-muted">
                                            Insira qual é a pontuação máxima que o candidato pode obter com essa escolaridade.
                                        </small>
                                    </div>
                                </div>
                                <!-- TIPO DE CAMPO -->
                                <div class="form-group">
                                    <label>Como o candidato irá preencher os dados dessa escolaridade?</label>
                                    <?php
                                        $tipoCampo = '';
                                        if ($cargoEscolaridade && property_exists($cargoEscolaridade, 'ds_tipo_campo') && $cargoEscolaridade->ds_tipo_campo !== null) {
                                            $tipoCampo = $cargoEscolaridade->ds_tipo_campo;
                                        } elseif (old('ds_tipo_campo') !== null) {
                                            $tipoCampo = old('ds_tipo_campo');
                                        }
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_digitar" value="INPUT" <?= $tipoCampo === 'INPUT' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tipo_campo_digitar">
                                            O candidato vai digitar o total de cursos concluidos (ideal para mais de uma pós-graduação, mestrado ou doutorado).
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_escolher" value="CHECK" <?= $tipoCampo === 'CHECK' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tipo_campo_escolher">
                                            O candidato vai apontar se possui o determinado curso (ideal quando a pontuação por escolaridade e pontuação máxima for igual). 
                                        </label>
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

                            <hr class="mt-5 mb-4">

                            <!-- DATATABLE: ESCOLARIDADES PARA ESSE CARGO -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-list mr-1"></i>
                                Escolaridades para esse cargo:
                            </h5>

                            <table id="tabela-escolaridades-cargo" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descrição da escolaridade</th>
                                        <th>Pontuação mínima</th>
                                        <th>Pontuação máxima</th>
                                        <th>Campo</th>
                                        <th class="text-center">Retirar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($escolaridadesDoCargo)): ?>
                                        <?php foreach ($escolaridadesDoCargo as $item): ?>
                                            <tr>
                                                <td><?= esc($item->ds_nome_escolaridade) ?></td>
                                                <td><?= esc($item->ds_pontuacao_minima) ?></td>
                                                <td><?= esc($item->ds_pontuacao_maxima) ?></td>
                                                <td><?= esc($item->ds_tipo_campo) ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-retirar-escolaridade"
                                                            data-id="<?= esc($item->pk_id_cargos_escolaridade) ?>"
                                                            data-nome="<?= esc($item->ds_nome_escolaridade) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Nenhuma escolaridade associada a este cargo.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal de Confirmação: Retirar Escolaridade do Cargo -->
<div class="modal fade" id="modalRetirarEscolaridade" tabindex="-1" role="dialog" aria-labelledby="modalRetirarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('CargosEscolaridades/deletarAssociacaoCargoEscolaridade') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="pk_id_cargos_escolaridade" id="retirar_pk_id" />

                <div class="modal-header">
                    <h5 class="modal-title" id="modalRetirarLabel">
                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                        Confirmar retirada
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Deseja realmente retirar a escolaridade <strong id="retirar_nome_escolaridade"></strong> deste cargo?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Não, cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i> Sim, retirar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // DataTable
        var table = $('#tabela-escolaridades-cargo').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            },
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true
        });

        // Abrir modal ao clicar em retirar
        $(document).on('click', '.btn-retirar-escolaridade', function() {
            var id   = $(this).data('id');
            var nome = $(this).data('nome');

            $('#retirar_pk_id').val(id);
            $('#retirar_nome_escolaridade').text(nome);
            $('#modalRetirarEscolaridade').modal('show');
        });
    });
</script>
