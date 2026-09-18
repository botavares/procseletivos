<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container dash-container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Gerenciamento de Processos Seletivos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('Dashboard') ?>">Início</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid dash-container">

            <!-- Alertas -->
            <div class="dash-alert-wrapper">
                <?php if(session()->has('mensagemError')): ?>
                    <div class="alert dash-alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo esc(session()->getFlashdata('mensagemError')) ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session()->has('mensagemSuccess')): ?>
                    <div class="alert dash-alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo esc(session()->getFlashdata('mensagemSuccess')) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Acesso Rápido -->
            <?php if($administrador == 1): ?>
                <div class="dash-section-title mb-2">
                    <i class="fas fa-th-large"></i> Área de Acesso Rápido
                </div>

                <div class="row mb-3">
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-2">
                        <a href="<?php echo base_url('Cargos') ?>" class="dash-quick-card">
                            <div class="dash-quick-icon dash-quick-icon--cadastros1"><i class="fas fa-user"></i></div>
                            <h6>Cargos</h6>
                            <small>Gerenciar cargos</small>
                        </a>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-2">
                        <a href="<?php echo base_url('Editais') ?>" class="dash-quick-card">
                            <div class="dash-quick-icon dash-quick-icon--cadastros2"><i class="fas fa-bullhorn"></i></div>
                            <h6>Editais</h6>
                            <small>Gerenciar editais</small>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-2">
                        <a href="#" data-toggle="modal" data-target="#escolhaEditais" class="dash-quick-card">
                            <div class="dash-quick-icon dash-quick-icon--cadastros3"><i class="fas fa-users"></i></div>
                            <h6>Candidatos</h6>
                            <small>Gerenciar candidatos</small>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-2">
                        <a href="#" data-toggle="modal" data-target="#escolhaClassificacoes" class="dash-quick-card">
                            <div class="dash-quick-icon dash-quick-icon--consultas3"><i class="fas fa-trophy"></i></div>
                            <h6>Classificação</h6>
                            <small>Consultar classificações</small>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-2">
                        <a href="#" data-toggle="modal" data-target="#escolhaRecursos" class="dash-quick-card">
                            <div class="dash-quick-icon dash-quick-icon--consultas2"><i class="fas fa-gavel"></i></div>
                            <h6>Recursos</h6>
                            <small>Aplicar recursos</small>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-2">
                        <a href="#" data-toggle="modal" data-target="#relatorios" class="dash-quick-card">
                            <div class="dash-quick-icon dash-quick-icon--relatorios1"><i class="fas fa-file-alt"></i></div>
                            <h6>Relatórios</h6>
                            <small>Emissão de relatórios</small>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Cards de Estatísticas -->
            <div class="dash-section-title mb-2">
                <i class="fas fa-chart-pie"></i> Indicadores e Estatísticas
            </div>
            <div class="row mb-3">
                <!-- Card 1: Total de Editais Ativos -->
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 mb-2">
                    <div class="card dash-stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="dash-stat-icon dash-stat-icon--primary mr-3">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div>
                                    <div id="dash-stat-totalEditais" class="dash-stat-value"><?php echo number_format(count($editais ?? []), 0, ',', '.') ?></div>
                                    <div class="dash-stat-label">Editais Ativos</div>
                                </div>
                            </div>
                            <div class="dash-stat-trend text-success mt-1">
                                <i class="fas fa-check-circle"></i> Em andamento
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Cargos com total de candidatos -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-2">
                    <div class="card dash-stat-card h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="dash-stat-icon dash-stat-icon--info mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <div class="dash-stat-value">Inscrições por Cargo</div>
                                    <div class="dash-stat-label">Editais Ativos</div>
                                </div>
                            </div>
                            <div class="dash-stat-trend text-info mt-1" style="max-height: 140px; overflow-y: auto;">
                                <?php if (!empty($cargosContagem)): ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php foreach ($cargosContagem as $item): ?>
                                            <li class="py-1 border-bottom">
                                                <strong>Edital <?php echo formatarNumeroEdital($item->ds_numero_edital) ?></strong> - <?php echo esc($item->ds_nome_cargo) ?>: 
                                                <span class="badge badge-info"><?php echo number_format($item->total_candidatos, 0, ',', '.') ?> candidatos</span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-muted small">Nenhum edital ativo com inscrições.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Invisível -->
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 mb-2" style="display: none;">
                    <div class="card dash-stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="dash-stat-icon dash-stat-icon--success mr-3">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <div id="dash-stat-totalCandidatos" class="dash-stat-value">-</div>
                                    <div class="dash-stat-label">Candidatos</div>
                                </div>
                            </div>
                            <div class="dash-stat-trend text-success mt-1">
                                <i class="fas fa-chart-line"></i> Inscritos
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Invisível -->
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 mb-2" style="display: none;">
                    <div class="card dash-stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="dash-stat-icon dash-stat-icon--warning mr-3">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div id="dash-stat-totalSecretarias" class="dash-stat-value">-</div>
                                    <div class="dash-stat-label">Secretarias</div>
                                </div>
                            </div>
                            <div class="dash-stat-trend text-warning mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Vinculadas
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- ================================================================
     MODAL: Escolha Editais (Candidatos)
     ================================================================ -->
<div class="modal fade dashboard-modal" id="escolhaEditais" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-users"></i> Escolha o Edital e o Cargo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="<?= base_url('Candidatos/salvarEscolha') ?>">
            <input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
            <div class="form-group">
                <label for="edital">Edital</label>
                <select class="form-control select-edital" data-target="#select-cargo" name="edital" required>
                    <option value="">Selecione um Edital</option>
                    <?php foreach ($editais as $edital): ?>
                        <option value="<?= $edital->pk_id_edital; ?>">
                            <?= substr_replace($edital->ds_numero_edital, '/', -4, 0) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="cargo">Selecione o Cargo</label>
                <select class="form-control select-cargo" id="select-cargo" name="cargo" required>
                    <option value="">Selecione um cargo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Sair</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     MODAL: Escolha Classificações
     ================================================================ -->
<div class="modal fade dashboard-modal" id="escolhaClassificacoes" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-trophy"></i> Escolha o Edital e o Cargo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="<?= base_url('Classificacoes/salvarEscolha') ?>">
            <input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
            <div class="form-group">
                <label for="edital">Edital</label>
                <select class="form-control select-edital" data-target="#select-cargo-classificacoes" name="edital" required>
                    <option value="">Selecione um Edital</option>
                    <?php foreach ($editais as $edital): ?>
                        <option value="<?= $edital->pk_id_edital; ?>">
                            <?= substr_replace($edital->ds_numero_edital, '/', -4, 0) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="cargo">Selecione o Cargo</label>
                <select class="form-control select-cargo" id="select-cargo-classificacoes" name="cargo" required>
                    <option value="">Selecione um cargo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Sair</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     MODAL: Escolha Recursos
     ================================================================ -->
<div class="modal fade dashboard-modal" id="escolhaRecursos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-gavel"></i> Escolha o Edital e o Cargo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="<?= base_url('Recursos/salvarEscolha') ?>">
            <input type="hidden" id="csrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
            <div class="form-group">
                <label for="edital">Edital</label>
                <select class="form-control select-edital" data-target="#select-cargo-recursos" name="edital" required>
                    <option value="">Selecione um Edital</option>
                    <?php foreach ($editais as $edital): ?>
                        <option value="<?= $edital->pk_id_edital; ?>">
                            <?= substr_replace($edital->ds_numero_edital, '/', -4, 0) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="cargo">Selecione o Cargo</label>
                <select class="form-control select-cargo" id="select-cargo-recursos" name="cargo" required>
                    <option value="">Selecione um cargo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Sair</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     MODAL: Relatórios
     ================================================================ -->
<div class="modal fade dashboard-modal" id="relatorios" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-alt"></i> Central de Relatórios</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3 text-center">Selecione o relatório que deseja emitir</p>
        <div class="list-group">
            <a href="#" data-toggle="modal" data-target="#escolhaRelatorioClassificacao" data-dismiss="modal" class="list-group-item list-group-item-action d-flex align-items-center">
                <i class="fas fa-trophy fa-lg text-warning mr-3"></i>
                <div>
                    <strong>Classificação por Cargo</strong><br>
                    <small class="text-muted">Gerar classificação em PDF ou CSV</small>
                </div>
            </a>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     MODAL: Classificação por Cargo (Relatório)
     ================================================================ -->
<div class="modal fade dashboard-modal" id="escolhaRelatorioClassificacao" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title"><i class="fas fa-trophy"></i> Classificação por Cargo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formRelatorioClassificacao">
            <input type="hidden" id="csrf-classificacao" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
            <div class="form-group">
                <label for="edital-classificacao">Edital</label>
                <select class="form-control" id="edital-classificacao" name="edital" required>
                    <option value="">Selecione um Edital</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cargo-class">Cargo</label>
                <select class="form-control" id="cargo-class" name="cargo" required disabled>
                    <option value="">Selecione um cargo</option>
                </select>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Retornar</button>
        <a href="#" id="btn-gerar-pdf" class="btn btn-danger disabled" target="_blank" onclick="return validarFormClass();">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <a href="#" id="btn-gerar-csv" class="btn btn-success disabled" target="_blank" onclick="return validarFormClass();">
            <i class="fas fa-file-excel"></i> CSV
        </a>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     MODAL: Erros Genérico
     ================================================================ -->
<div class="modal fade dashboard-modal" id="modalErros" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="nome-erro" class="modal-title text-danger"></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <p id="descricao-erro"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><strong>Retornar</strong></button>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     Footer
     ================================================================ -->
<footer class="main-footer">
    <div class="float-right d-none d-sm-block"></div>
    <strong>Copyright &copy; <?php echo date('Y')?> <a href="https://www.divinópolis.mg.gov.br">Prefeitura Municipal de Divinópolis</a></strong>.
</footer>

<aside class="control-sidebar control-sidebar-dark"></aside>
</div>
