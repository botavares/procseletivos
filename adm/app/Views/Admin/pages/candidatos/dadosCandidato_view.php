<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">
                                <i class="fas fa-user mr-2"></i>
                                <?php echo esc($titulo); ?>
                            </h3>
                        </div>

                        <!-- TOOLBOX -->
                        <div class="card-toolbox mt-2 mb-2 d-flex flex-row">
                            <div class="mb-2 ml-1 mr-2">
                                <a href="<?php echo base_url('Candidatos/' . $dadosCandidato['idEdital'] . '/' . $dadosCandidato['idCargo']); ?>"
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

                        <!-- BODY -->
                        <div class="card-body">

                            <!-- DADOS PESSOAIS -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-id-card mr-1"></i>
                                Dados Pessoais
                            </h5>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nome Completo</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo strtoupper(esc($dadosCandidato['candidato']->ds_nome)); ?>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Data de Nascimento</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo date('d/m/Y', strtotime($dadosCandidato['candidato']->ds_nascimento)); ?>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Telefone</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo mask($dadosCandidato['candidato']->ds_celular, '(##) # ####-####'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Endereço</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php
                                            echo strtoupper(
                                                esc($dadosCandidato['candidato']->ds_rua . ', ' .
                                                $dadosCandidato['candidato']->ds_numero . ' - ' .
                                                $dadosCandidato['candidato']->ds_nome_bairro . ' - ' .
                                                $dadosCandidato['candidato']->ds_cidade . ' - ' .
                                                $dadosCandidato['candidato']->ds_uf)
                                            );
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>E-mail</label>
                                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light font-weight-bold">
                                        <?php echo esc($dadosCandidato['candidato']->ds_email); ?>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- PONTUAÇÕES CLASSIFICATÓRIAS -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-star mr-1"></i>
                                Pontuações Classificatórias
                            </h5>

                            <div class="form-row">
                                <!-- Experiências -->
                                <div class="form-group col-md-6">
                                    <label>Experiências</label>
                                    <?php if (!empty($dadosCandidato['experiencias'])): ?>
                                        <?php foreach($dadosCandidato['experiencias'] as $experiencia): ?>
                                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-light">
                                                <span class="small"><?php echo esc($experiencia->ds_nome_experiencia); ?></span>
                                                <span class="badge badge-primary"><?php echo (int)$experiencia->ds_quantidade; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small px-3 py-2">Nenhuma experiência cadastrada.</div>
                                    <?php endif; ?>
                                </div>

                                <!-- Escolaridades -->
                                <div class="form-group col-md-6">
                                    <label>Escolaridade</label>
                                    <?php if (!empty($dadosCandidato['escolaridades'])): ?>
                                        <?php foreach($dadosCandidato['escolaridades'] as $escolaridade): ?>
                                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-light">
                                                <span class="small"><?php echo esc($escolaridade->ds_nome_escolaridade); ?></span>
                                                <span class="badge badge-info"><?php echo (int)$escolaridade->ds_quantidade; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small px-3 py-2">Nenhuma escolaridade cadastrada.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Aperfeiçoamentos -->
                                <div class="form-group col-md-6">
                                    <label>Aperfeiçoamentos</label>
                                    <?php if (!empty($dadosCandidato['aperfeicoamentos'])): ?>
                                        <?php foreach($dadosCandidato['aperfeicoamentos'] as $aperfeicoamento): ?>
                                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-light">
                                                <span class="small"><?php echo esc($aperfeicoamento->ds_nome_curso); ?></span>
                                                <span class="badge badge-success"><?php echo (int)$aperfeicoamento->ds_quantidade; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small px-3 py-2">Nenhum aperfeiçoamento cadastrado.</div>
                                    <?php endif; ?>
                                </div>

                                <!-- Critérios Adicionais (NOVO) -->
                                <div class="form-group col-md-6">
                                    <label>Critérios Adicionais</label>
                                    <?php if (!empty($dadosCandidato['criterios'])): ?>
                                        <?php foreach($dadosCandidato['criterios'] as $criterio): ?>
                                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-light">
                                                <span class="small"><?php echo esc($criterio->ds_nome_criterio); ?></span>
                                                <span class="badge badge-warning"><?php echo (int)$criterio->ds_quantidade; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small px-3 py-2">Nenhum critério adicional cadastrado.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr>

                            <!-- BOTÕES -->
                            <div class="text-right mt-4">
                                <a href="<?php echo base_url('Recursos/' . $dadosCandidato['idEdital'] . '/' . $dadosCandidato['idCargo'] . '/' . $dadosCandidato['idCandidato']); ?>"
                                   class="btn btn-primary">
                                    <i class="fas fa-edit mr-1"></i> Aplicar Recurso
                                </a>
                            </div>

                        </div>
                        <!-- /card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
