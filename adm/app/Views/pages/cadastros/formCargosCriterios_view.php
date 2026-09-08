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

                            <form id="formCriteriosCargo"
                                  method="POST"
                                  action="<?= site_url('CargosCriterios/registrarAssociacaoCargoCriterio') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="fk_id_cargo" value="<?= esc($cargo->pk_id_cargo) ?>">
                                <input type="hidden" name="pk_id_cargo_criterio" id="pk_id_cargo_criterio" value="">
                                <input type="hidden" name="acao" id="acao" value="<?= esc($acao) ?>">

                                <!-- Criterio -->
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Nome do critério</label>
                                        <select class="form-control" name="fk_id_criterio" id="fk_id_criterio" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($Criterios as $esc): ?>
                                                <option value="<?= esc($esc->pk_id_criterio) ?>">
                                                    <?= esc($esc->ds_nome_criterio) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- QUANTIDADE MÍNIMA / MÁXIMA -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Pontuação por cada critério</label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_minima"
                                               id="ds_pontuacao_minima"
                                               value=""
                                               min="0">
                                        <small class="form-text text-muted">
                                            Insira qual é a pontuação mínima que o candidato pode obter com esse critério.
                                        </small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Pontuação máxima </label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_maxima"
                                               id="ds_pontuacao_maxima"
                                               value=""
                                               min="0">
                                        <small class="form-text text-muted">
                                            Insira qual é a pontuação máxima que o candidato pode obter com esse critério.
                                        </small>
                                    </div>
                                </div>
                                <!-- TIPO DE CAMPO -->
                                <div class="form-group">
                                    <label>Como o candidato irá preencher os dados desse critério?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_digitar" value="INPUT">
                                        <label class="form-check-label" for="tipo_campo_digitar">
                                            O candidato vai digitar o total de itens, 
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_escolher" value="CHECK">
                                        <label class="form-check-label" for="tipo_campo_escolher">
                                            O candidato vai apontar se possui o determinado curso (ideal quando a pontuação por Criterio e pontuação máxima for igual). 
                                        </label>
                                    </div>
                                </div>

                                <hr>

                                <!-- FOOTER -->
                                <div class="text-right mt-4">
                                    <button type="button" id="btnLimpar" class="btn btn-secondary mr-2">
                                        <i class="fas fa-eraser"></i> Limpar
                                    </button>

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Salvar
                                    </button>
                                </div>

                            </form>

                            <hr class="mt-5 mb-4">

                            <!-- DATATABLE: CriterioS PARA ESSE CARGO -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-list mr-1"></i>
                                Criterios Adicionais para esse cargo:
                            </h5>

                            <table id="tabela-Criterios-cargo" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descrição da Criterio</th>
                                        <th>Pontuação mínima</th>
                                        <th>Pontuação máxima</th>
                                        <th>Campo</th>
                                        <th class="text-center">Alterar</th>
                                        <th class="text-center">Retirar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($CriteriosDoCargo)): ?>
                                        <?php foreach ($CriteriosDoCargo as $item): ?>
                                            <tr>
                                                <td><?= esc($item->ds_nome_criterio) ?></td>
                                                <td><?= esc($item->ds_pontuacao_minima) ?></td>
                                                <td><?= esc($item->ds_pontuacao_maxima) ?></td>
                                                <td><?= esc($item->ds_tipo_campo) ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info btn-sm btn-alterar-Criterio"
                                                            data-id="<?= esc($item->pk_id_cargo_criterio) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-retirar-Criterio"
                                                            data-id="<?= esc($item->pk_id_cargo_criterio) ?>"
                                                            data-nome="<?= esc($item->ds_nome_criterio) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhuma Criterio associada a este cargo.</td>
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

<!-- Modal de Confirmação: Retirar Criterio do Cargo -->
<div class="modal fade" id="modalRetirarCriterio" tabindex="-1" role="dialog" aria-labelledby="modalRetirarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('CargosCriterios/deletarAssociacaoCargoCriterio') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="pk_id_cargo_criterio" id="retirar_pk_id" />

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
                    <p>Deseja realmente retirar a Criterio <strong id="retirar_nome_criterio"></strong> deste cargo?</p>
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
        var table = $('#tabela-Criterios-cargo').DataTable({
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
        $(document).on('click', '.btn-retirar-Criterio', function() {
            var id   = $(this).data('id');
            var nome = $(this).data('nome');

            $('#retirar_pk_id').val(id);
            $('#retirar_nome_criterio').text(nome);
            $('#modalRetirarCriterio').modal('show');
        });

        // Carregar dados no formulário ao clicar em alterar
        $(document).on('click', '.btn-alterar-Criterio', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '<?= site_url('CargosCriterios/buscarAssociacao') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && !data.erro) {
                        $('#pk_id_cargo_criterio').val(data.pk_id_cargo_criterio);
                        $('#acao').val('update');
                        $('#fk_id_criterio').val(data.fk_id_criterio).trigger('change');
                        $('#ds_pontuacao_minima').val(data.ds_pontuacao_minima);
                        $('#ds_pontuacao_maxima').val(data.ds_pontuacao_maxima);

                        if (data.ds_tipo_campo === 'INPUT') {
                            $('#tipo_campo_digitar').prop('checked', true);
                        } else if (data.ds_tipo_campo === 'CHECK') {
                            $('#tipo_campo_escolher').prop('checked', true);
                        } else {
                            $('input[name="ds_tipo_campo"]').prop('checked', false);
                        }

                        // Rolar até o formulário
                        $('html, body').animate({
                            scrollTop: $('#formCriteriosCargo').offset().top - 100
                        }, 500);
                    } else {
                        alert('Erro ao buscar dados da Criterio.');
                    }
                },
                error: function() {
                    alert('Erro ao buscar dados da Criterio.');
                }
            });
        });

        // Botão Limpar: reseta o formulário para nova associação
        $('#btnLimpar').on('click', function() {
            $('#formCriteriosCargo')[0].reset();
            $('#pk_id_cargo_criterio').val('');
            $('#acao').val('create');
        });
    });
</script>
