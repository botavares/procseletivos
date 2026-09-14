<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="card card-outline card-info shadow-sm">
                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                <?php echo esc($titulo); ?>
                            </h3>
                            <a href="<?php echo base_url('Recursos/historico'); ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Voltar
                            </a>
                        </div>

                        <div class="card-body">

                            <!-- DADOS PRINCIPAIS DO RECURSO -->
                            <h5 class="mb-3 text-info">
                                <i class="fas fa-file-alt mr-1"></i>
                                Informações do Recurso
                            </h5>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Protocolo</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo esc($recurso->ds_numero_protocolo); ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Data/Hora da Alteração</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo date('d/m/Y', strtotime($recurso->ds_data_alteracao)) . ' ' . $recurso->ds_hora_alteracao; ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Responsável</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo esc($recurso->ds_usuario_responsavel); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Candidato</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo esc($recurso->ds_nome_candidato ?? 'N/A'); ?>
                                        <?php if (!empty($recurso->ds_cpf)): ?>
                                            <span class="text-muted small">(CPF: <?= mask($recurso->ds_cpf, '###.###.###-##') ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Edital</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo esc($recurso->ds_numero_edital ?? 'N/A'); ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Cargo</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo esc($recurso->ds_nome_cargo ?? 'N/A'); ?>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- DETALHES DA ALTERAÇÃO -->
                            <h5 class="mb-3 text-info">
                                <i class="fas fa-exchange-alt mr-1"></i>
                                Detalhes da Alteração
                            </h5>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Campo Alterado</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo esc(ucfirst($recurso->ds_campo_alterado)); ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>ID do Campo</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo $recurso->fk_id_campo_alterado; ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Tipo</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php
                                            $tipoClass = [
                                                'alterado' => 'text-warning',
                                                'inserido' => 'text-success',
                                                'removido' => 'text-danger'
                                            ][$recurso->ds_tipo] ?? 'text-secondary';

                                            $tipoLabel = [
                                                'alterado' => 'Alterado',
                                                'inserido' => 'Inserido',
                                                'removido' => 'Removido'
                                            ][$recurso->ds_tipo] ?? $recurso->ds_tipo;
                                        ?>
                                        <span class="<?= $tipoClass ?> font-weight-bold"><?= $tipoLabel ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Valor Antigo</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold text-center">
                                        <?php echo $recurso->ds_valor_antigo ?? '-'; ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Valor Novo</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold text-center">
                                        <?php echo $recurso->ds_valor_novo ?? '-'; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($registrosProtocolo) && count($registrosProtocolo) > 1): ?>
                                <hr>

                                <h5 class="mb-3 text-info">
                                    <i class="fas fa-list mr-1"></i>
                                    Outras Alterações no Mesmo Protocolo (<?= count($registrosProtocolo) - 1; ?>)
                                </h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Campo Alterado</th>
                                                <th>Tipo</th>
                                                <th>Valor Antigo</th>
                                                <th>Valor Novo</th>
                                                <th>Data/Hora</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($registrosProtocolo as $item): ?>
                                                <?php if ($item->pk_id_historico == $recurso->pk_id_historico) continue; ?>
                                                <?php
                                                    $tipoClassItem = [
                                                        'alterado' => 'badge-warning',
                                                        'inserido' => 'badge-success',
                                                        'removido' => 'badge-danger'
                                                    ][$item->ds_tipo] ?? 'badge-secondary';

                                                    $tipoLabelItem = [
                                                        'alterado' => 'Alterado',
                                                        'inserido' => 'Inserido',
                                                        'removido' => 'Removido'
                                                    ][$item->ds_tipo] ?? $item->ds_tipo;
                                                ?>
                                                <tr>
                                                    <td><?= esc(ucfirst($item->ds_campo_alterado)) ?> (ID: <?= $item->fk_id_campo_alterado ?>)</td>
                                                    <td><span class="badge <?= $tipoClassItem ?>"><?= $tipoLabelItem ?></span></td>
                                                    <td class="text-center"><?= $item->ds_valor_antigo ?? '-' ?></td>
                                                    <td class="text-center"><?= $item->ds_valor_novo ?? '-' ?></td>
                                                    <td><?= date('d/m/Y', strtotime($item->ds_data_alteracao)) ?> <?= $item->ds_hora_alteracao ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <div class="text-right mt-4">
                                <a href="<?php echo base_url('Recursos/historico'); ?>" class="btn btn-warning">
                                    <i class="fas fa-arrow-left mr-1"></i> Voltar ao Histórico
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
