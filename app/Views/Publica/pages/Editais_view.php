       
        <div class="preloader">
            <div class="loader">
                <div class="ytp-spinner">
                    <div class="ytp-spinner-container">
                        <div class="ytp-spinner-rotator">
                            <div class="ytp-spinner-left">
                                <div class="ytp-spinner-circle"></div>
                            </div>
                            <div class="ytp-spinner-right">
                                <div class="ytp-spinner-circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM PRELOADER -->
        <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger text-center" role="alert">
            <ul class="list-unstyled mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php if (session()->has('mensagemSuccess')): ?>
            <div class="alert bg-green-vivid-50 text-center area-alertas font22 branco chanfrado" role="alert">
                <?= esc(session('mensagemSuccess')) ?>
            </div>
        <?php endif; ?>
        <?php if (session()->has('mensagemError')): ?>
            <div class="alert bg-red-vivid-50 text-center area-alertas font22 branco chanfrado" role="alert">
                <?= esc(session('mensagemError')) ?>
            </div>
        <?php endif; ?>
        <nav class="br-breadcrumb" aria-label="Breadcrumbs">
			<ol class="crumb-list" role="list">
			    <li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
				<li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Editais (Página Atual)</span></li>
			</ol>
		</nav>

        <div class="br-message info mrg-top-10 pdd-5">
            <div class="icon"><i class="fas fa-info-circle fa-lg"></i></div>
            <div class="content" role="alert">
                <span class="message-title">Abaixo estão listados os editais disponíveis.</span>
                <div class="message-body">
                    Caso seu curso NÃO esteja contemplado nos editais, você poderá cadastrar normalmente. MANTENHA SEUS DADOS ATUALIZADOS, principalmente o período em que está cursando
                e quando surgir um edital direcionado ao seu curso, ele será automaticamente vinculado ao seu cadastro.
                </div>
            </div>
        </div>

        <div>
            <a  class="br-button primary voltar mrg-top-10" href="<?php echo base_url("Home")?>"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>


        <div class="col-md-12 offset-md-3 pdd0">
            <div class="border-bottom mrg-bottom-10">
                <span class="bold font-24">Editais disponíveis</span>
            </div>
            <div class="row mt-4">
                <?php foreach ($editais as $edital): ?>
                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="br-card hover h-100">
                                    <div class="card-header">
                                        <?php 
                                        // Formata o número do edital com a máscara 012026 para 01/2026
                                            $ano = substr($edital->ds_numero_edital, -4);            // últimos 4 dígitos → ano
                                            $numero = substr($edital->ds_numero_edital, 0, -4);      // o que sobra → número do edital
                                            // remove zeros à esquerda
                                            $numero = ltrim($numero, "0");
                                            $editalFormatado = "Edital " .$numero . '/' . $ano;
                                        ?>
                                        <h5 class="card-title bold font-em-1"><?= $editalFormatado; ?></h5>
                                    </div>
                                    <div class="card-content text-center bold font-24 font-blue">
                                        <p class="card-text font-em-1"><?= "início: " . esc(date("d/m/Y",strtotime($edital->ds_data_inicial))) ?></p>
                                        <p class="card-text font-em-1"><?= "término: " . esc(date("d/m/Y",strtotime($edital->ds_data_termino)))?></p>

                                        <?php $listaCargos = $cargosPorEdital[$edital->pk_id_edital] ?? []; ?>
                                        <div class="mb-3" style="font-size: 0.85em; line-height: 1.2;">
                                            <p class="mb-0 text-secondary small"><strong>Cargo(s) do edital:</strong></p>
                                            <p class="mb-0 text-secondary small" style="word-break: break-word;">
                                                <?php if (!empty($listaCargos)): ?>
                                                    <?php $nomesCargos = array_map(fn($c) => esc($c->ds_nome_cargo), $listaCargos); ?>
                                                    <?= implode(', ', $nomesCargos) ?>
                                                <?php else: ?>
                                                    Nenhum cargo vinculado
                                                <?php endif; ?>
                                            </p>
                                        </div>

                                        <a href="<?= site_url('Editais/obterEdital/' . esc($edital->ds_arquivo_edital)) ?>" class="br-button primary" target="_self">Ver Edital</a>
                                    </div>                          
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($editaisEncerrados)): ?>
        <div class="col-md-12 offset-md-3 pdd0 mt-5">
            <div class="border-bottom mrg-bottom-10">
                <span class="bold font-20 text-secondary">Editais encerrados</span>
            </div>
            <div class="row mt-4">
                <?php foreach ($editaisEncerrados as $edital): ?>
                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                        <div class="card" style="opacity: 0.9;">
                            <div class="card-body">
                                <div class="br-card hover h-100">
                                    <div class="card-header">
                                        <?php 
                                            $ano = substr($edital->ds_numero_edital, -4);
                                            $numero = substr($edital->ds_numero_edital, 0, -4);
                                            $numero = ltrim($numero, "0");
                                            $editalFormatado = "Edital " .$numero . '/' . $ano;
                                        ?>
                                        <h5 class="card-title bold font-em-1 text-secondary"><?= $editalFormatado; ?></h5>
                                    </div>
                                    <div class="card-content text-center bold font-24 text-secondary" style="font-size: 0.95em;">
                                        <p class="card-text font-em-1"><?= "início: " . esc(date("d/m/Y",strtotime($edital->ds_data_inicial))) ?></p>
                                        <p class="card-text font-em-1 text-danger"><?= "término: " . esc(date("d/m/Y",strtotime($edital->ds_data_termino)))?></p>

                                        <?php $listaCargosEncerrados = $cargosPorEditalEncerrados[$edital->pk_id_edital] ?? []; ?>
                                        <div class="mb-3" style="font-size: 0.85em; line-height: 1.2;">
                                            <p class="mb-0 text-secondary small"><strong>Cargo(s) do edital:</strong></p>
                                            <p class="mb-0 text-secondary small" style="word-break: break-word;">
                                                <?php if (!empty($listaCargosEncerrados)): ?>
                                                    <?php $nomesCargosEncerrados = array_map(fn($c) => esc($c->ds_nome_cargo), $listaCargosEncerrados); ?>
                                                    <?= implode(', ', $nomesCargosEncerrados) ?>
                                                <?php else: ?>
                                                    Nenhum cargo vinculado
                                                <?php endif; ?>
                                            </p>
                                        </div>

                                        <a href="<?= site_url('Editais/obterEdital/' . esc($edital->ds_arquivo_edital)) ?>" class="br-button secondary" target="_self">Ver Edital</a>
                                    </div>                          
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

</main>
                    
       
       
       
       
       
     