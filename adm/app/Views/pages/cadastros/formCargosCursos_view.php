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
                                // Normaliza $cargoCurso para objeto, caso venha como array
                                if ($cargoCurso !== null && is_array($cargoCurso)) {
                                    $cargoCurso = (object) $cargoCurso;
                                }

                                // Helper para recuperar valor preenchido (banco > old > default)
                                $val = function($campo, $default = '') use ($cargoCurso) {
                                    if ($cargoCurso && property_exists($cargoCurso, $campo) && $cargoCurso->$campo !== null) {
                                        return $cargoCurso->$campo;
                                    }
                                    return old($campo) ?? $default;
                                };
                            ?>

                            <form id="formCursosCargo"
                                  method="POST"
                                  action="<?= site_url('CargosCursos/registrarAssociacaoCargoCursos') ?>">

                                <?= csrf_field() ?>
                                <input type="hidden" name="fk_id_cargo" value="<?= esc($cargo->pk_id_cargo) ?>">
                                <input type="hidden" name="acao" value="<?= esc($acao) ?>">

                                <!-- Cursos de aperfeiçoamento -->
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Cursos de aperfeiçoamento *</label>
                                        <select class="form-control" name="fk_id_curso" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($cursos as $exp): ?>
                                                <?php
                                                    $selected = '';
                                                    if ($cargoCurso && $cargoCurso->fk_id_curso == $exp->pk_id_curso) {
                                                        $selected = 'selected';
                                                    } elseif (old('fk_id_curso') == $exp->pk_id_curso) {
                                                        $selected = 'selected';
                                                    }
                                                ?>
                                                <option value="<?= esc($exp->pk_id_curso) ?>" <?= $selected ?>>
                                                    <?= esc($exp->ds_nome_curso) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- PONTUAÇÃO MÍNIMA / MÁXIMA -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Pontuação por cada cursos de aperfeiçoamento </label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_minima"
                                               value="<?= esc($val('ds_pontuacao_minima')) ?>"
                                               min="0">
                                               <small class="form-text text-muted">
                                                   Insira aqui quantos ponto cada cursos de aperfeiçoamento terá. 
                                               </small>
                                    </div>
                                   

                                    <div class="form-group col-md-6">
                                        <label>Pontuação máxima do cursos de aperfeiçoamento</label>
                                        <input type="number"
                                               class="form-control"
                                               name="ds_pontuacao_maxima"
                                               value="<?= esc($val('ds_pontuacao_maxima')) ?>"
                                               min="0">
                                               <small class="form-text text-muted">
                                                   Insira qual é a pontuação máxima que o candidato pode obter com esse cursos de aperfeiçoamento.
                                               </small>
                                    </div>
                                </div>
                                <!-- TIPO DE CAMPO -->
                                <div class="form-group">
                                    <label>Como o candidato irá preencher os dados desse curso de aperfeiçoamento?</label>
                                    <?php
                                        $tipoCampo = '';
                                        if ($cargoCurso && property_exists($cargoCurso, 'ds_tipo_campo') && $cargoCurso->ds_tipo_campo !== null) {
                                            $tipoCampo = $cargoCurso->ds_tipo_campo;
                                        } elseif (old('ds_tipo_campo') !== null) {
                                            $tipoCampo = old('ds_tipo_campo');
                                        }
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_digitar" value="INPUT" <?= $tipoCampo === 'INPUT' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tipo_campo_digitar">
                                            O candidato vai digitar o total de cursos de aperfeiçoamento que ele possui. (Recomendado)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ds_tipo_campo" id="tipo_campo_escolher" value="SELECT" <?= $tipoCampo === 'SELECT' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tipo_campo_escolher">
                                            O candidato vai escolher o valor oferecido a ele.
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

                            <!-- DATATABLE: Cursos de aperfeiçoamentoS PARA ESSE CARGO -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-list mr-1"></i>
                                Cursos de aperfeiçoamentos para esse cargo:
                            </h5>

                            <table id="tabela-cursos-cargo" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descrição do Curso de Aperfeiçoamento</th>
                                        <th>Pontuação mínima</th>
                                        <th>Pontuação máxima</th>
                                        <th>Campo</th>
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
                                            <td colspan="5" class="text-center">Nenhuma Cursos de aperfeiçoamento associada a este cargo.</td>
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

<!-- Modal de Confirmação: Retirar Cursos de aperfeiçoamento do Cargo -->
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
                    <p>Deseja realmente retirar a Cursos de aperfeiçoamento <strong id="retirar_nome_curso"></strong> deste cargo?</p>
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
    });
</script>
