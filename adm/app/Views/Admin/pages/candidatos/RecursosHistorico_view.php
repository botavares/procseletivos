<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12 col-md-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-history mr-2"></i>
                                <?php echo esc($titulo); ?>
                            </h3>
                        </div>

                        <!-- FILTROS -->
                        <div class="card-body">
                            <form method="POST" action="<?php echo base_url('Recursos/historico'); ?>">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Protocolo</label>
                                            <input type="text" name="protocolo" class="form-control" value="<?= $filtros['protocolo'] ?? '' ?>" placeholder="Nº do Protocolo">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Candidato (Nome ou CPF)</label>
                                            <input type="text" name="candidato" class="form-control" value="<?= $filtros['candidato'] ?? '' ?>" placeholder="Nome ou CPF">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Data Início</label>
                                            <input type="date" name="data_inicio" class="form-control" value="<?= $filtros['data_inicio'] ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Data Fim</label>
                                            <input type="date" name="data_fim" class="form-control" value="<?= $filtros['data_fim'] ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search mr-1"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Edital</label>
                                            <select name="edital" class="form-control">
                                                <option value="">Todos</option>
                                                <?php foreach($editais as $edital): ?>
                                                <option value="<?= $edital->pk_id_edital ?>" <?php echo (isset($filtros['edital']) && $filtros['edital'] == $edital->pk_id_edital) ? 'selected' : ''; ?>>
                                                    <?= esc(formatarNumeroEdital($edital->ds_numero_edital)) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cargo</label>
                                            <select name="cargo" class="form-control">
                                                <option value="">Todos</option>
                                                <?php foreach($cargos as $cargo): ?>
                                                    <option value="<?= $cargo->pk_id_cargo ?>" <?php echo (isset($filtros['cargo']) && $filtros['cargo'] == $cargo->pk_id_cargo) ? 'selected' : ''; ?>>
                                                        <?= esc($cargo->ds_nome_cargo) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <a href="<?php echo base_url('Recursos/historico'); ?>" class="btn btn-secondary w-100">
                                                <i class="fas fa-eraser mr-1"></i> Limpar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <!-- RESULTADOS -->
                            <?php if (!empty($historico)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover table-sm">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Protocolo</th>
                                                <th class="text-nowrap">Candidato</th>
                                                <th>Edital</th>
                                                <th>Cargo</th>
                                                <th>Campo Alterado</th>
                                                <th>Tipo</th>
                                                <th>Valor Antigo</th>
                                                <th>Valor Novo</th>
                                                <th>Observação</th>
                                                <th>Responsável</th>
                                                <th class="text-center">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($historico as $item): ?>
                                                <?php
                                                    $tipoClass = [
                                                        'alterado'   => 'badge-warning',
                                                        'inserido'   => 'badge-success',
                                                        'removido'   => 'badge-danger',
                                                        'indeferido' => 'badge-dark'
                                                    ][$item->ds_tipo] ?? 'badge-secondary';

                                                    $tipoLabel = [
                                                        'alterado'   => 'Alterado',
                                                        'inserido'   => 'Inserido',
                                                        'removido'   => 'Removido',
                                                        'indeferido' => 'Indeferido'
                                                    ][$item->ds_tipo] ?? $item->ds_tipo;
                                                ?>
                                                <tr>
                                                    <td><?= esc($item->ds_numero_protocolo) ?></td>
                                                    <td class="text-nowrap"><?= esc($item->ds_nome_candidato ?? 'N/A') ?></td>
                                                    <td><?= esc(formatarNumeroEdital($item->ds_numero_edital ?? '')) ?></td>
                                                    <td><?= esc($item->ds_nome_cargo ?? 'N/A') ?></td>
                                                    <td><?= esc($item->ds_nome_campo ?? 'Campo não encontrado') ?></td>
                                                    <td><span class="badge <?= $tipoClass ?>"><?= $tipoLabel ?></span></td>
                                                    <td class="text-center"><?= $item->ds_valor_antigo ?? '-' ?></td>
                                                    <td class="text-center"><?= $item->ds_valor_novo ?? '-' ?></td>
                                                    <td>
                                                        <?php if ($item->ds_tipo === 'indeferido' && !empty($item->ds_observacao)): ?>
                                                            <span class="text-danger small"><i class="fas fa-ban mr-1"></i><?= esc(substr($item->ds_observacao, 0, 100) . (strlen($item->ds_observacao) > 100 ? '...' : '')) ?></span>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($item->ds_usuario_responsavel) ?></td>
                                                    <td class="text-center">
                                                        <a href="<?php echo base_url('Recursos/detalhes/' . $item->pk_id_historico); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Nenhum registro encontrado com os filtros aplicados.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
