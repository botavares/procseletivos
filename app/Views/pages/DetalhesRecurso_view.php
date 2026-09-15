<main class="d-flex flex-fill mb-5" id="main">
	<div class="container-lg d-flex">
		<div class="col-md-12 row">
			<div class="col-md-12 mb-5">
				<div class="main-content pl-sm-3 mt-0" id="main-content">
					<nav class="br-breadcrumb" aria-label="Breadcrumbs">
						<ol class="crumb-list" role="list">
							<li class="crumb home">
								<a class="br-button circle" href="<?php echo base_url("Home")?>">
									<span class="sr-only">Página inicial</span>
									<i class="fas fa-home"></i>
								</a>
							</li>
							<li class="crumb">
								<i class="icon fas fa-chevron-right"></i>
								<a href="<?php echo base_url("Cadastros")?>">Candidato</a>
							</li>
							<li class="crumb" data-active="active">
								<i class="icon fas fa-chevron-right"></i>
								<span tabindex="0" aria-current="page">Detalhes do Recurso</span>
							</li>
						</ol>
					</nav>

					<div class="mrg-top-10 mrg-bottom-10">
						<a class="br-button primary voltar" href="<?php echo base_url("Cadastros")?>">
							<i class="fas fa-arrow-left"></i> Voltar
						</a>
					</div>

					<h2 class="text-gray-80 mrg-top-20 mrg-bottom-20">Detalhes do Recurso</h2>

					<div class="br-card p-4 mb-4">
						<div class="row">
							<div class="col-12 col-md-6 mb-3">
								<div class="br-input">
									<label for="protocolo" class="text-nowrap font-weight-bold">Protocolo</label>
									<input id="protocolo" type="text" value="<?= esc($detalhes['protocolo']) ?>" disabled />
								</div>
							</div>
							<div class="col-12 col-md-6 mb-3">
								<div class="br-input">
									<label for="edital" class="text-nowrap font-weight-bold">Edital</label>
									<input id="edital" type="text" value="<?= esc($detalhes['numero_edital']) ?>" disabled />
								</div>
							</div>
							<div class="col-12 mb-3">
								<div class="br-input">
									<label for="cargo" class="text-nowrap font-weight-bold">Cargo</label>
									<input id="cargo" type="text" value="<?= esc($detalhes['nome_cargo']) ?>" disabled />
								</div>
							</div>
						</div>
					</div>

					<h3 class="text-gray-80 mrg-top-20 mrg-bottom-20">Campos Recursados</h3>

					<?php if (!empty($detalhes['campos'])): ?>
						<div class="row">
							<?php foreach ($detalhes['campos'] as $campo): ?>
								<div class="col-12 mb-3">
									<div class="br-card p-3">
										<div class="d-flex justify-content-between align-items-start flex-wrap">
											<div class="mb-2">
												<h5 class="text-weight-semi-bold text-gray-80 mb-1">
													<?= esc(ucfirst($campo['categoria'])) ?>: <?= esc($campo['nome_campo']) ?>
												</h5>
												<p class="mb-0 text-gray-60">
													<strong>Status:</strong>
													<?php if ($campo['status'] == 0): ?>
														<span class="badge bg-danger text-white" style="color: #fff !important;"><?= esc($campo['status_label']) ?></span>
													<?php elseif ($campo['status'] == 1): ?>
														<span class="badge bg-success text-white" style="color: #fff !important;"><?= esc($campo['status_label']) ?></span>
													<?php else: ?>
														<span class="badge bg-warning text-white" style="color: #fff !important;"><?= esc($campo['status_label']) ?></span>
													<?php endif; ?>
												</p>
											</div>
										</div>

										<?php if ($campo['status'] == 1): ?>
											<div class="row mt-2">
												<div class="col-12 col-md-6 mb-2">
													<div class="p-2 bg-gray-10 rounded">
														<span class="text-gray-60"><strong>Valor anterior:</strong></span>
														<br><span class="text-gray-80"><?= esc($campo['valor_antigo']) ?></span>
													</div>
												</div>
												<div class="col-12 col-md-6 mb-2">
													<div class="p-2 bg-gray-10 rounded">
														<span class="text-gray-60"><strong>Valor alterado:</strong></span>
														<br><span class="text-gray-80"><?= esc($campo['valor_novo']) ?></span>
													</div>
												</div>
											</div>
										<?php endif; ?>

										<?php if (!empty($campo['observacao'])): ?>
											<div class="mt-2 p-2 br-message info">
												<div class="icon"><i class="fas fa-info-circle"></i></div>
												<div class="content">
													<span class="message-title">Observação</span>
													<div class="message-body"><?= nl2br(esc($campo['observacao'])) ?></div>
												</div>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else: ?>
						<div class="br-message info mrg-top-10 pdd-5">
							<div class="icon"><i class="fas fa-info-circle fa-lg"></i></div>
							<div class="content" role="alert">
								<span class="message-title">Informação</span>
								<div class="message-body">Nenhum campo recursado encontrado para este protocolo.</div>
							</div>
						</div>
					<?php endif; ?>

					<div class="mrg-top-30 mrg-bottom-30 text-center">
						<a class="br-button secondary" href="<?php echo base_url("Cadastros")?>">
							<i class="fas fa-arrow-left"></i> Retornar para Meus Cadastros
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
