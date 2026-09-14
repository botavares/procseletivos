<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-gavel mr-2"></i>
                                <?php echo esc($titulo); ?>
                            </h3>
                        </div>

                        <!-- TOOLBOX -->
                        <div class="card-toolbox mt-2 mb-2 d-flex flex-row">
                            <div class="mb-2 ml-1 mr-2">
                                <a href="<?php echo base_url('Candidatos/exibirDados/' . $dadosRecursos['idEdital'] . '/' . $dadosRecursos['idCargo'] . '/' . $dadosRecursos['idCandidato']); ?>"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                                </a>
                            </div>
                        </div>

                        <!-- MENSAGENS -->
                        <?php if(session()->has('error')): ?>
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    <?php foreach(session()->getFlashdata('error') as $valueError): ?>
                                        <li><?php echo esc($valueError); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo base_url('Recursos/registrar') ?>" enctype="multipart/form-data">
                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                            <input type="hidden" name="recurso-aberto" value="true">
                            <input type="hidden" name="action" value="<?= $acao ?>">
                            <input type="hidden" name="pk_id_edital" value="<?= $edital ?>">
                            <input type="hidden" name="pk_id_cargo" value="<?= $cargo ?>">
                            <input type="hidden" name="pk_id_candidato" value="<?= $dadosRecursos['candidato']->pk_id_cadastrado ?>">
                            <input type="hidden" name="ds_nome" value="<?= esc($dadosRecursos['candidato']->ds_nome) ?>">

                            <div class="card-body">

                                <!-- DADOS DO RECURSO -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-file-alt mr-1"></i>
                                    Dados do Recurso
                                </h5>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Nome Completo</label>
                                        <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                            <?php echo strtoupper(esc($dadosRecursos['candidato']->ds_nome)); ?>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Protocolo do Recurso</label>
                                        <input type="text" id="input-protocolo" class="form-control" name="ds_protocolo" value="<?= old('ds_protocolo'); ?>" required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Data de Nascimento</label>
                                        <input type="text" id="input-data-nascimento" class="form-control input-data" name="ds_data_nascimento" value="<?= date('d/m/Y', strtotime($dadosRecursos['candidato']->ds_nascimento)); ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>E-mail</label>
                                        <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                            <?php echo esc($dadosRecursos['candidato']->ds_email); ?>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- PONTUAÇÕES CLASSIFICATÓRIAS -->
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-star mr-1"></i>
                                    Pontuações Classificatórias (Alterar via Recurso)
                                </h5>

                                <?php
                                    // Helper: indexar dados salvos por ID do campo
                                    $indexar = function($lista, $campoId) {
                                        $map = [];
                                        foreach ($lista as $item) {
                                            $map[(int)$item->{$campoId}] = (int)$item->ds_quantidade;
                                        }
                                        return $map;
                                    };

                                    $experienciasSalvas = $indexar($dadosRecursos['experiencias'] ?? [], 'fk_id_experiencia');
                                    $escolaridadesSalvas = $indexar($dadosRecursos['escolaridades'] ?? [], 'fk_id_escolaridade');
                                    $aperfeicoamentosSalvos = $indexar($dadosRecursos['aperfeicoamentos'] ?? [], 'fk_id_curso');
                                    $criteriosSalvos = $indexar($dadosRecursos['criterios'] ?? [], 'fk_id_criterio');

                                    // Macro para renderizar um campo com checkbox de indeferimento
                                    $renderCampo = function($campo, $valorAtual, $nomeCategoria, $id, $nomeId) use ($dadosRecursos, $indeferimentosMap) {
                                        $prefix = $nomeCategoria;
                                        $checkId = "check-indef-{$prefix}-{$id}";
                                        $obsId = "obs-{$prefix}-{$id}";

                                        $jaIndeferido = isset($indeferimentosMap[$nomeCategoria][$id]);
                                        $obsTexto = $jaIndeferido ? ($indeferimentosMap[$nomeCategoria][$id]['observacao'] ?? '') : '';
                                ?>
                                        <div class="border rounded px-3 py-2 mb-2 bg-light">
                                            <div class="small font-weight-bold mb-1"><?php echo esc($campo->{"ds_nome_{$nomeId}"}); ?></div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <!-- SELECT -->
                                                    <?php if ($campo->ds_tipo_campo === 'SELECT'): ?>
                                                        <?php
                                                            $totalDeAnos = ($campo->ds_pontuacao_maxima / $campo->ds_pontuacao_minima);
                                                            $tipoExp = $campo->ds_tipo_experiencia ?? '';
                                                        ?>
                                                        <select class="form-control form-control-sm" name="ds_<?= $prefix ?>[<?= $id ?>]">
                                                            <option value="0" <?php echo ($valorAtual == 0) ? 'selected' : ''; ?>>Não possui<?php echo $tipoExp ? ' ' . esc($tipoExp) : ''; ?></option>
                                                            <?php for($i = 1; $i <= $totalDeAnos; $i++): ?>
                                                                <?php
                                                                    $textoOption = ($i == $totalDeAnos) ? $i . " ou mais" . ($tipoExp ? ' ' . $tipoExp : '') : $i . ($tipoExp ? ' ' . $tipoExp : '');
                                                                ?>
                                                                <option value="<?= $i ?>" <?php echo ($valorAtual == $i) ? 'selected' : ''; ?>><?php echo esc($textoOption); ?></option>
                                                            <?php endfor; ?>
                                                        </select>
                                                    <?php endif; ?>

                                                    <!-- CHECK -->
                                                    <?php if ($campo->ds_tipo_campo === 'CHECK'): ?>
                                                        <input type="hidden" name="ds_<?= $prefix ?>[<?= $id ?>]" value="0">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="check-<?= $prefix ?>-<?= $id ?>" name="ds_<?= $prefix ?>[<?= $id ?>]" value="1" <?php echo ($valorAtual > 0) ? 'checked' : ''; ?>>
                                                            <label class="custom-control-label" for="check-<?= $prefix ?>-<?= $id ?>">Possui</label>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- INPUT -->
                                                    <?php if ($campo->ds_tipo_campo === 'INPUT'): ?>
                                                        <input type="number" min="0" class="form-control form-control-sm" name="ds_<?= $prefix ?>[<?= $id ?>]" value="<?= $valorAtual ?>" placeholder="Quantidade">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4 d-flex align-items-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input toggle-observacao" id="<?= $checkId ?>" name="indef_<?= $prefix ?>[<?= $id ?>]" value="1" data-target="<?= $obsId ?>" <?= $jaIndeferido ? 'checked' : '' ?>>
                                                        <label class="custom-control-label text-danger small" for="<?= $checkId ?>">
                                                            <i class="fas fa-ban mr-1"></i>Indeferir
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- OBSERVAÇÃO (hidden por padrão, visível se já indeferido) -->
                                            <div id="<?= $obsId ?>" class="mt-2 <?= $jaIndeferido ? '' : 'd-none' ?>">
                                                <label class="small text-danger font-weight-bold mb-1"><i class="fas fa-ban mr-1"></i>Motivo do Indeferimento:</label>
                                                <textarea class="form-control form-control-sm border-danger" name="obs_<?= $prefix ?>[<?= $id ?>]" rows="2" placeholder="Descreva o motivo do indeferimento..."><?= esc($obsTexto) ?></textarea>
                                            </div>
                                        </div>
                                <?php
                                    };
                                ?>

                                <div class="form-row">
                                    <!-- EXPERIÊNCIAS -->
                                    <div class="form-group col-md-6">
                                        <label class="text-primary font-weight-bold">Experiências</label>
                                        <?php if (!empty($camposFormularios['experiencias'])): ?>
                                            <?php foreach($camposFormularios['experiencias'] as $campo): ?>
                                                <?php
                                                    $id = (int)$campo->fk_id_experiencia;
                                                    $valorAtual = $experienciasSalvas[$id] ?? 0;
                                                    $renderCampo($campo, $valorAtual, 'experiencias', $id, 'experiencia');
                                                ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-muted small px-3 py-2">Nenhuma experiência configurada para este cargo.</div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- ESCOLARIDADES -->
                                    <div class="form-group col-md-6">
                                        <label class="text-primary font-weight-bold">Escolaridade</label>
                                        <?php if (!empty($camposFormularios['escolaridades'])): ?>
                                            <?php foreach($camposFormularios['escolaridades'] as $campo): ?>
                                                <?php
                                                    $id = (int)$campo->fk_id_escolaridade;
                                                    $valorAtual = $escolaridadesSalvas[$id] ?? 0;
                                                    $renderCampo($campo, $valorAtual, 'escolaridades', $id, 'escolaridade');
                                                ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-muted small px-3 py-2">Nenhuma escolaridade configurada para este cargo.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <!-- APERFEIÇOAMENTOS -->
                                    <div class="form-group col-md-6">
                                        <label class="text-primary font-weight-bold">Cursos de Aperfeiçoamento</label>
                                        <?php if (!empty($camposFormularios['aperfeicoamentos'])): ?>
                                            <?php foreach($camposFormularios['aperfeicoamentos'] as $campo): ?>
                                                <?php
                                                    $id = (int)$campo->fk_id_curso;
                                                    $valorAtual = $aperfeicoamentosSalvos[$id] ?? 0;
                                                    $renderCampo($campo, $valorAtual, 'aperfeicoamentos', $id, 'curso');
                                                ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-muted small px-3 py-2">Nenhum aperfeiçoamento configurado para este cargo.</div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- CRITÉRIOS ADICIONAIS -->
                                    <div class="form-group col-md-6">
                                        <label class="text-primary font-weight-bold">Critérios Adicionais</label>
                                        <?php if (!empty($camposFormularios['criterios'])): ?>
                                            <?php foreach($camposFormularios['criterios'] as $campo): ?>
                                                <?php
                                                    $id = (int)$campo->fk_id_criterio;
                                                    $valorAtual = $criteriosSalvos[$id] ?? 0;
                                                    $renderCampo($campo, $valorAtual, 'criterios', $id, 'criterio');
                                                ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-muted small px-3 py-2">Nenhum critério adicional configurado para este cargo.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                            <!-- /card-body -->

                            <div class="card-footer text-right">
                                <a href="<?php echo base_url('Candidatos/exibirDados/' . $dadosRecursos['idEdital'] . '/' . $dadosRecursos['idCargo'] . '/' . $dadosRecursos['idCandidato']); ?>" class="btn btn-warning">
                                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-success ml-2">
                                    <i class="fas fa-save mr-1"></i> Salvar Recurso
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('.toggle-observacao').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var targetId = this.getAttribute('data-target');
            var targetDiv = document.getElementById(targetId);
            if (this.checked) {
                targetDiv.classList.remove('d-none');
            } else {
                targetDiv.classList.add('d-none');
            }
        });
    });
</script>
