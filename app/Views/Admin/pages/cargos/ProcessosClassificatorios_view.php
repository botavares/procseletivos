<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?= esc($titulo ?? 'Dados Classificatórios do Cargo') ?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?= base_url_adm('Dashboard') ?>">Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url_adm($returnUrl ?? 'ProcessosSeletivos') ?>">Processo Seletivo</a></li>
						<li class="breadcrumb-item active">Classificatórios</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<?php if (session()->has('mensagemError')): ?>
				<div class="alert alert-danger alert-dismissible fade show mb-3 py-2">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?= esc(session('mensagemError')) ?>
				</div>
			<?php endif; ?>

			<?php if (session()->has('mensagemSuccess')): ?>
				<div class="alert alert-success alert-dismissible fade show mb-3 py-2">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?= esc(session('mensagemSuccess')) ?>
				</div>
			<?php endif; ?>

			<!-- CABEÇALHO INFORMATIVO -->
			<div class="card card-outline card-primary mb-4">
				<div class="card-header d-flex justify-content-between align-items-center py-2">
					<div>
						<h3 class="card-title m-0">
							<i class="fas fa-clipboard-list mr-2"></i>
							Gerenciar Dados Classificatórios
						</h3>
					</div>
					<div class="d-flex">
						<a href="<?= base_url_adm($returnUrl ?? 'ProcessosSeletivos') ?>" class="btn btn-warning btn-sm">
							<i class="fas fa-arrow-left mr-1"></i> Voltar
						</a>
					</div>
				</div>
				<div class="card-body p-3">
					<div class="row">
						<div class="col-md-6">
							<div class="d-flex align-items-center mb-2">
								<i class="fas fa-briefcase text-primary fa-lg mr-2"></i>
								<div>
									<small class="text-muted d-block">Cargo</small>
									<strong class="h5 m-0"><?= esc($cargo->ds_nome_cargo ?? 'Cargo não informado') ?></strong>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="d-flex align-items-center mb-2">
								<i class="fas fa-file-alt text-success fa-lg mr-2"></i>
								<div>
									<small class="text-muted d-block">Processo Seletivo</small>
									<strong class="h5 m-0">
										<?= esc(formatarNumeroEdital(($processo->nm_numero_processo ?? '') . ($processo->nm_ano_processo ?? ''))) ?>
										<small class="text-muted ml-1"><?= esc($processo->ds_objeto ?? '') ?></small>
									</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- CARDS DE AÇÕES -->
			<div class="row">
				<div class="col-12">
					<h5 class="mb-3 text-primary">
						<i class="fas fa-cogs mr-1"></i>
						Selecione o tipo de dado classificatório que deseja configurar para este cargo:
					</h5>
				</div>

				<!-- Experiências -->
				<div class="col-lg-4 col-md-6 mb-3">
					<a href="<?= base_url_adm('CargosExperiencias/formularioCargosExperiencia/' . ($processo->pk_id_processo ?? 0) . '/' . ($cargo->pk_id_cargo ?? 0)) ?>"
					   class="btn btn-outline-info btn-block text-left p-3 h-100 d-flex align-items-center card-hover"
					   title="Associar experiências ao cargo">
						<i class="fas fa-briefcase fa-2x mr-3"></i>
						<div class="text-left">
							<div class="font-weight-bold">Experiências</div>
							<small class="text-muted">Adicionar experiências exigidas para o cargo</small>
						</div>
					</a>
				</div>

				<!-- Escolaridades -->
				<div class="col-lg-4 col-md-6 mb-3">
					<a href="<?= base_url_adm('CargosEscolaridades/formularioCargosEscolaridade/' . ($processo->pk_id_processo ?? 0) . '/' . ($cargo->pk_id_cargo ?? 0)) ?>"
					   class="btn btn-outline-primary btn-block text-left p-3 h-100 d-flex align-items-center card-hover"
					   title="Associar escolaridades ao cargo">
						<i class="fas fa-graduation-cap fa-2x mr-3"></i>
						<div class="text-left">
							<div class="font-weight-bold">Escolaridades</div>
							<small class="text-muted">Adicionar níveis de escolaridade exigidos</small>
						</div>
					</a>
				</div>

				<!-- Cursos -->
				<div class="col-lg-4 col-md-6 mb-3">
					<a href="<?= base_url_adm('CargosCursos/formularioCargosCurso/' . ($processo->pk_id_processo ?? 0) . '/' . ($cargo->pk_id_cargo ?? 0)) ?>"
					   class="btn btn-outline-success btn-block text-left p-3 h-100 d-flex align-items-center card-hover"
					   title="Associar cursos/aperfeiçoamentos ao cargo">
						<i class="fas fa-book fa-2x mr-3"></i>
						<div class="text-left">
							<div class="font-weight-bold">Cursos</div>
							<small class="text-muted">Adicionar cursos e aperfeiçoamentos exigidos</small>
						</div>
					</a>
				</div>

				<!-- Critérios -->
				<div class="col-lg-4 col-md-6 mb-3">
					<a href="<?= base_url_adm('CargosCriterios/formularioCargosCriterio/' . ($processo->pk_id_processo ?? 0) . '/' . ($cargo->pk_id_cargo ?? 0)) ?>"
					   class="btn btn-outline-warning btn-block text-left p-3 h-100 d-flex align-items-center card-hover"
					   title="Associar critérios adicionais ao cargo">
						<i class="fas fa-star fa-2x mr-3"></i>
						<div class="text-left">
							<div class="font-weight-bold">Critérios</div>
							<small class="text-muted">Adicionar critérios adicionais de pontuação</small>
						</div>
					</a>
				</div>

				<!-- Desempate -->
				<div class="col-lg-4 col-md-6 mb-3">
					<a href="<?= base_url_adm('CargosDesempateConfig/' . ($processo->pk_id_processo ?? 0) . '/' . ($cargo->pk_id_cargo ?? 0)) ?>"
					   class="btn btn-outline-danger btn-block text-left p-3 h-100 d-flex align-items-center card-hover"
					   title="Configurar critérios de desempate do cargo">
						<i class="fas fa-balance-scale fa-2x mr-3"></i>
						<div class="text-left">
							<div class="font-weight-bold">Desempate</div>
							<small class="text-muted">Configurar regras de desempate na classificação</small>
						</div>
					</a>
				</div>
			</div>
		</div>
	</section>
</div>

<style>
	.card-hover {
		transition: transform .15s ease-in-out, box-shadow .15s ease-in-out;
		border-width: 2px;
	}
	.card-hover:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0,0,0,0.12);
	}
	.card-hover i {
		width: 40px;
		text-align: center;
	}
</style>
