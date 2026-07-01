<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <?php $origem = current_url(); ?>

    <!-- LOGO -->
    <a href="<?= site_url('Dashboard') ?>" class="brand-link">
        <img src="<?= base_url('external/img/logo/brasao.png') ?>"
             alt="Logo do sistema"
             class="brand-image img-circle elevation-3"
             style="opacity: .9">
        <span class="brand-text font-weight-light small">
            <?= esc($titulo) ?>
        </span>
    </a>

    <div class="sidebar">

        <!-- USUÁRIO -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img src="<?= base_url('external/img/programmer.png') ?>"
                     class="img-circle elevation-2"
                     alt="Usuário">
            </div>
            <div class="info">
                <span class="d-block text-white small">
                    Olá, <?= esc(session('nome')) ?>
                </span>
            </div>
        </div>

        <!-- BUSCA -->
        <div class="form-inline mb-3">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar"
                       type="search"
                       placeholder="Pesquisar"
                       aria-label="Pesquisar">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- MENU -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                <?php if (session('administrador') == '1'): ?>

                    <li class="nav-item">
                        <a href="<?= site_url('Dashboard') ?>" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Início</p>
                        </a>
                    </li>

                    <li class="nav-header text-uppercase">Cadastros</li>

                    <li class="nav-item">
                        <a href="<?= site_url('Cargos') ?>" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Cargos</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('Editais') ?>" class="nav-link">
                            <i class="nav-icon fas fa-bullhorn"></i>
                            <p>Editais</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link"
                           data-toggle="modal"
                           data-target="#escolhaEditais">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Candidatos</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= site_url('Secretarias') ?>" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Secretarias</p>
                        </a>
                    </li>
<!--
                    <li class="nav-item">
                        <a href="<?= site_url('Setores') ?>" class="nav-link">
                            <i class="nav-icon fas fa-sitemap"></i>
                            <p>Setores</p>
                        </a>
                    </li>
-->
                    <li class="nav-item">
                        <a href="<?= site_url('Login/formAlterarSenha') ?>" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Altere sua senha</p>
                        </a>
                    </li>

                    <li class="nav-header text-uppercase">Relatórios</li>

                    <li class="nav-item">
                        <a href="#"
                           class="nav-link"
                           data-toggle="modal"
                           data-target="#relatorios">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Relatórios</p>
                        </a>
                    </li>

                    

                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>

<div class="modal fade" id="relatorios" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalLabel">
                    <i class="fas fa-file-text-o mr-2"></i> Relatórios
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <p class="text-muted mb-3 text-center">
                    Selecione o relatório que deseja emitir
                </p>

                <div class="list-group">
					<!--CANDIDATOS POR CURSO-->
					<div class="mb-2">
						<a href="<?= site_url('Relatorios/formCandidatosPorCurso') . '?voltar=' . urlencode($origem) ?>"
						class="list-group-item list-group-item-action d-flex align-items-center"
						target="_blank">
							<i class="fas fa-users fa-lg text-info mr-3"></i>
							<div>
								<strong>Candidatos por Curso</strong><br>
								<small class="text-muted">Lista de candidatos vinculados ao curso</small>
							</div>
						</a>
					</div>
					<!--CANDIDATOS POR ABRANGÊNCIA-->
					<div class="mb-2">
						<a href="<?= site_url('Relatorios/formCandidatosPorAbrangencia') . '?voltar=' . urlencode($origem) ?>"
						class="list-group-item list-group-item-action d-flex align-items-center"
						target="_blank">
							<i class="fas fa-users fa-lg text-info mr-3"></i>
							<div>
								<strong>Candidatos por Abrangência</strong><br>
								<small class="text-muted">Lista de candidatos de acordo com a abrangência de seu curso.</small>
							</div>
						</a>
					</div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fa fa-arrow-left"></i> Retornar
                </button>
            </div>

        </div>
    </div>
</div>