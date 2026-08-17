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
                                // Normaliza $cargoExperiencia para objeto, caso venha como array
                                if ($cargoExperiencia !== null && is_array($cargoExperiencia)) {
                                    $cargoExperiencia = (object) $cargoExperiencia;
                                }

                                // Helper para recuperar valor preenchido (banco > old > default)
                                $val = function($campo, $default = '') use ($cargoExperiencia) {
                                    if ($cargoExperiencia && property_exists($cargoExperiencia, $campo) && $cargoExperiencia->$campo !== null) {
                                        return $cargoExperiencia->$campo;
                                    }
                                    return old($campo) ?? $default;
                                };
                            ?>

                            <form id="formExperienciasCargo"
                                  method="POST"
                                  action="<?= site_url('Cargos/registrarExperiencia') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="fk_id_cargo" value="<?= esc($cargo->pk_id_cargo) ?>">
                                <input type="hidden" name="acao" value="<?= esc($acao) ?>">

                                <!-- EXPERIÊNCIA -->
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Experiência *</label>
                                        <select class="form-control" name="fk_id_experiencia" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($experiencias as $exp): ?>
                                                <?php
                                                    $selected = '';
                                                    if ($cargoExperiencia && $cargoExperiencia->fk_id_experiencia == $exp->pk_id_experiencia) {
                                                        $selected = 'selected';
                                                    } elseif (old('fk_id_experiencia') == $exp->pk_id_experiencia) {
                                                        $selected = 'selected';
                                                    }
                                                ?>
                                                <option value="<?= esc($exp->pk_id_experiencia) ?>" <?= $selected ?>>
                                                    <?= esc($exp->ds_nome_experiencia) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- QUANTIDADE MÍNIMA / MÁXIMA -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Quantidade mínima de experiência</label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_quantidade_minima"
                                               value="<?= esc($val('ds_quantidade_minima')) ?>"
                                               min="0">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Quantidade máxima de experiência</label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_quantidade_maxima"
                                               value="<?= esc($val('ds_quantidade_maxima')) ?>"
                                               min="0">
                                    </div>
                                </div>

                                <!-- MULTIPLICADOR -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Multiplicador</label>
                                        <input type="number"
                                               step="any"
                                               class="form-control"
                                               name="ds_multiplicador"
                                               value="<?= esc($val('ds_multiplicador')) ?>">
                                        <small class="form-text text-muted">
                                            O multiplicador será usado para indicar a pontuação de acordo com a quantidade de experiência do candidato.
                                        </small>
                                    </div>
                                </div>

                                <!-- TIPO DE CAMPO -->
                                <div class="form-group">
                                    <label>Como será informada a experiência?</label>
                                    <?php
                                        $tipoCampo = '';
                                        if ($cargoExperiencia && property_exists($cargoExperiencia, 'ds_tipo_campo') && $cargoExperiencia->ds_tipo_campo !== null) {
                                            $tipoCampo = $cargoExperiencia->ds_tipo_campo;
                                        } elseif (old('ds_tipo_campo') !== null) {
                                            $tipoCampo = old('ds_tipo_campo');
                                        }
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_digitar" value="INPUT" <?= $tipoCampo === 'INPUT' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tipo_campo_digitar">
                                            O candidato vai digitar o total de experiência que ele possui
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_escolher" value="SELECT" <?= $tipoCampo === 'SELECT' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tipo_campo_escolher">
                                            O candidato vai escolher o valor oferecido a ele (recomendado)
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

                            <!-- DATATABLE: EXPERIÊNCIAS PARA ESSE CARGO -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-list mr-1"></i>
                                Experiências para esse cargo:
                            </h5>

                            <table id="tabela-experiencias-cargo" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descrição da experiência</th>
                                        <th>Quantidade mínima</th>
                                        <th>Quantidade máxima</th>
                                        <th>Campo</th>
                                        <th class="text-center">Retirar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($experienciasDoCargo)): ?>
                                        <?php foreach ($experienciasDoCargo as $item): ?>
                                            <tr>
                                                <td><?= esc($item->ds_nome_experiencia) ?></td>
                                                <td><?= esc($item->ds_quantidade_minima) ?></td>
                                                <td><?= esc($item->ds_quantidade_maxima) ?></td>
                                                <td><?= esc($item->ds_tipo_campo) ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-retirar-experiencia"
                                                            data-id="<?= esc($item->pk_id_cargos_experiencias) ?>"
                                                            data-nome="<?= esc($item->ds_nome_experiencia) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Nenhuma experiência associada a este cargo.</td>
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

<!-- Modal de Confirmação: Retirar Experiência do Cargo -->
<div class="modal fade" id="modalRetirarExperiencia" tabindex="-1" role="dialog" aria-labelledby="modalRetirarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('Cargos/deletarExperiencia') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="pk_id_cargos_experiencias" id="retirar_pk_id" />

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
                    <p>Deseja realmente retirar a experiência <strong id="retirar_nome_experiencia"></strong> deste cargo?</p>
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
        var table = $('#tabela-experiencias-cargo').DataTable({
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
        $(document).on('click', '.btn-retirar-experiencia', function() {
            var id   = $(this).data('id');
            var nome = $(this).data('nome');

            $('#retirar_pk_id').val(id);
            $('#retirar_nome_experiencia').text(nome);
            $('#modalRetirarExperiencia').modal('show');
        });
    });
</script>
