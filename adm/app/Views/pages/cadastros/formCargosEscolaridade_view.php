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

                            <form id="formEscolaridadesCargo"
                                  method="POST"
                                  action="<?= site_url('CargosEscolaridades/registrarAssociacaoCargoEscolaridade') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="fk_id_cargo" value="<?= esc($cargo->pk_id_cargo) ?>">
                                <input type="hidden" name="pk_id_cargos_escolaridade" id="pk_id_cargos_escolaridade" value="">
                                <input type="hidden" name="acao" id="acao" value="<?= esc($acao) ?>">

                                <!-- ESCOLARIDADE -->
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Nome da escolaridade</label>
                                        <select class="form-control" name="fk_id_escolaridade" id="fk_id_escolaridade" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($escolaridades as $esc): ?>
                                                <option value="<?= esc($esc->pk_id_escolaridade) ?>">
                                                    <?= esc($esc->ds_nome_escolaridade) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- QUANTIDADE MINIMA / MAXIMA -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Pontuacao por cada escolaridade</label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_minima"
                                               id="ds_pontuacao_minima"
                                               value=""
                                               min="0">
                                        <small class="form-text text-muted">
                                            Insira qual e a pontuacao minima que o candidato pode obter com essa escolaridade.
                                        </small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Pontuacao maxima </label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_maxima"
                                               id="ds_pontuacao_maxima"
                                               value=""
                                               min="0">
                                        <small class="form-text text-muted">
                                            Insira qual e a pontuacao maxima que o candidato pode obter com essa escolaridade.
                                        </small>
                                    </div>
                                </div>
                                <!-- TIPO DE CAMPO -->
                                <div class="form-group">
                                    <label>Como o candidato ira preencher os dados dessa escolaridade?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_digitar" value="INPUT">
                                        <label class="form-check-label" for="tipo_campo_digitar">
                                            O candidato vai digitar o total de cursos concluidos (ideal para mais de uma pos-graduacao, mestrado ou doutorado).
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_escolher" value="CHECK">
                                        <label class="form-check-label" for="tipo_campo_escolher">
                                            O candidato vai apontar se possui o determinado curso (ideal quando a pontuacao por escolaridade e pontuacao maxima for igual). 
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

                            <!-- DATATABLE: ESCOLARIDADES PARA ESSE CARGO -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-list mr-1"></i>
                                Escolaridades para esse cargo:
                            </h5>

                            <table id="tabela-escolaridades-cargo" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descricao da escolaridade</th>
                                        <th>Pontuacao minima</th>
                                        <th>Pontuacao maxima</th>
                                        <th>Campo</th>
                                        <th class="text-center">Alterar</th>
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
                                                    <button type="button" class="btn btn-info btn-sm btn-alterar-escolaridade"
                                                            data-id="<?= esc($item->pk_id_cargos_escolaridade) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
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
                                            <td colspan="6" class="text-center">Nenhuma escolaridade associada a este cargo.</td>
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

<!-- Modal de Confirmacao: Retirar Escolaridade do Cargo -->
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
                        <i class="fas fa-times mr-1"></i> Nao, cancelar
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

        // Carregar dados no formulario ao clicar em alterar
        $(document).on('click', '.btn-alterar-escolaridade', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '<?= site_url('CargosEscolaridades/buscarAssociacao') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && !data.erro) {
                        $('#pk_id_cargos_escolaridade').val(data.pk_id_cargos_escolaridade);
                        $('#acao').val('update');
                        $('#fk_id_escolaridade').val(data.fk_id_escolaridade).trigger('change');
                        $('#ds_pontuacao_minima').val(data.ds_pontuacao_minima);
                        $('#ds_pontuacao_maxima').val(data.ds_pontuacao_maxima);

                        if (data.ds_tipo_campo === 'INPUT') {
                            $('#tipo_campo_digitar').prop('checked', true);
                        } else if (data.ds_tipo_campo === 'CHECK') {
                            $('#tipo_campo_escolher').prop('checked', true);
                        } else {
                            $('input[name="ds_tipo_campo"]').prop('checked', false);
                        }

                        // Rolar ate o formulario
                        $('html, body').animate({
                            scrollTop: $('#formEscolaridadesCargo').offset().top - 100
                        }, 500);
                    } else {
                        alert('Erro ao buscar dados da escolaridade.');
                    }
                },
                error: function() {
                    alert('Erro ao buscar dados da escolaridade.');
                }
            });
        });

        // Botao Limpar: reseta o formulario para nova associacao
        $('#btnLimpar').on('click', function() {
            $('#formEscolaridadesCargo')[0].reset();
            $('#pk_id_cargos_escolaridade').val('');
            $('#acao').val('create');
        });
    });
</script>
