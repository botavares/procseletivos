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

                            <form id="formCursosCargo"
                                  method="POST"
                                  action="<?= site_url('CargosCursos/registrarAssociacaoCargoCursos') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="fk_id_cargo" value="<?= esc($cargo->pk_id_cargo) ?>">
                                <input type="hidden" name="pk_id_cargo_aperfeicoamento" id="pk_id_cargo_aperfeicoamento" value="">
                                <input type="hidden" name="acao" id="acao" value="<?= esc($acao) ?>">

                                <!-- Cursos de aperfeicoamento -->
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Cursos de aperfeicoamento *</label>
                                        <select class="form-control" name="fk_id_curso" id="fk_id_curso" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($cursos as $exp): ?>
                                                <option value="<?= esc($exp->pk_id_curso) ?>">
                                                    <?= esc($exp->ds_nome_curso) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- PONTUACAO MINIMA / MAXIMA -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Pontuacao por cada cursos de aperfeicoamento </label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_minima"
                                               id="ds_pontuacao_minima"
                                               value=""
                                               min="0">
                                               <small class="form-text text-muted">
                                                   Insira aqui quantos ponto cada cursos de aperfeicoamento tera. 
                                               </small>
                                    </div>
                                   

                                    <div class="form-group col-md-6">
                                        <label>Pontuacao maxima do cursos de aperfeicoamento</label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_maxima"
                                               id="ds_pontuacao_maxima"
                                               value=""
                                               min="0">
                                               <small class="form-text text-muted">
                                                   Insira qual e a pontuacao maxima que o candidato pode obter com esse cursos de aperfeicoamento.
                                               </small>
                                    </div>
                                </div>
                                <!-- TIPO DE CAMPO -->
                                <div class="form-group">
                                    <label>Como o candidato ira preencher os dados desse curso de aperfeicoamento?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_digitar" value="INPUT">
                                        <label class="form-check-label" for="tipo_campo_digitar">
                                            O candidato vai digitar o total de cursos de aperfeicoamento que ele possui. (Recomendado)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_escolher" value="SELECT">
                                        <label class="form-check-label" for="tipo_campo_escolher">
                                            O candidato vai escolher o valor oferecido a ele.
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

                            <!-- DATATABLE: Cursos de aperfeicoamentoS PARA ESSE CARGO -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-list mr-1"></i>
                                Cursos de aperfeicoamentos para esse cargo:
                            </h5>

                            <table id="tabela-cursos-cargo" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descricao do Curso de Aperfeicoamento</th>
                                        <th>Pontuacao minima</th>
                                        <th>Pontuacao maxima</th>
                                        <th>Campo</th>
                                        <th class="text-center">Alterar</th>
                                        <th class="text-center">Retirar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($cursosDoCargo)): ?>
                                        <?php foreach ($cursosDoCargo as $item): ?>
                                            <tr>
                                                <td><?= esc($item->ds_nome_curso) ?></td>
                                                <td><?= esc($item->ds_pontuacao_minima) ?></td>
                                                <td><?= esc($item->ds_pontuacao_maxima) ?></td>
                                                <td><?= esc($item->ds_tipo_campo) ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info btn-sm btn-alterar-curso"
                                                            data-id="<?= esc($item->pk_id_cargo_aperfeicoamento) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-retirar-curso"
                                                            data-id="<?= esc($item->pk_id_cargo_aperfeicoamento) ?>"
                                                            data-nome="<?= esc($item->ds_nome_curso) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhuma Cursos de aperfeicoamento associada a este cargo.</td>
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

<!-- Modal de Confirmacao: Retirar Cursos de aperfeicoamento do Cargo -->
<div class="modal fade" id="modalRetirarCurso" tabindex="-1" role="dialog" aria-labelledby="modalRetirarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('CargosCursos/deletarAssociacaoCargoCursos') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="pk_id_cargo_aperfeicoamento" id="retirar_pk_id" />

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
                    <p>Deseja realmente retirar a Cursos de aperfeicoamento <strong id="retirar_nome_curso"></strong> deste cargo?</p>
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
        var table = $('#tabela-cursos-cargo').DataTable({
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
        $(document).on('click', '.btn-retirar-curso', function() {
            var id   = $(this).data('id');
            var nome = $(this).data('nome');

            $('#retirar_pk_id').val(id);
            $('#retirar_nome_curso').text(nome);
            $('#modalRetirarCurso').modal('show');
        });

        // Carregar dados no formulario ao clicar em alterar
        $(document).on('click', '.btn-alterar-curso', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '<?= site_url('CargosCursos/buscarAssociacao') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && !data.erro) {
                        $('#pk_id_cargo_aperfeicoamento').val(data.pk_id_cargo_aperfeicoamento);
                        $('#acao').val('update');
                        $('#fk_id_curso').val(data.fk_id_curso).trigger('change');
                        $('#ds_pontuacao_minima').val(data.ds_pontuacao_minima);
                        $('#ds_pontuacao_maxima').val(data.ds_pontuacao_maxima);

                        if (data.ds_tipo_campo === 'INPUT') {
                            $('#tipo_campo_digitar').prop('checked', true);
                        } else if (data.ds_tipo_campo === 'SELECT') {
                            $('#tipo_campo_escolher').prop('checked', true);
                        } else {
                            $('input[name="ds_tipo_campo"]').prop('checked', false);
                        }

                        // Rolar ate o formulario
                        $('html, body').animate({
                            scrollTop: $('#formCursosCargo').offset().top - 100
                        }, 500);
                    } else {
                        alert('Erro ao buscar dados do curso.');
                    }
                },
                error: function() {
                    alert('Erro ao buscar dados do curso.');
                }
            });
        });

        // Botao Limpar: reseta o formulario para nova associacao
        $('#btnLimpar').on('click', function() {
            $('#formCursosCargo')[0].reset();
            $('#pk_id_cargo_aperfeicoamento').val('');
            $('#acao').val('create');
        });
    });
</script>
