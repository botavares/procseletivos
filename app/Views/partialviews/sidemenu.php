<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="#" class="brand-link">
		<img src="<?php echo base_url("external/img/").$mestra->ds_arquivo_brasao?>" alt="Logo do sistema" class="brand-image img-circle elevation-3" style="opacity: .8">
		<span class="brand-text font-weight-light font10"><?php echo $mestra->ds_nome_sistema ?></span>
	</a>

	<div class="sidebar">
		
		<!-- ICONE E NOME USUÁRIO-->
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
				<img src="<?php echo base_url("external/img/programmer.png")?>" class="img-circle elevation-2" alt="icone usuário">
			</div>
			<div class="info">
				<p class="d-block white">Olá,</p>
				<p class="text-left white"><?php echo session('ds_nome_agente')?></p>
			</div>
		</div>
		
		<!-- BARRA DE BUSCA-->
		<div class="form-inline">
			<div class="input-group" data-widget="sidebar-search">
				<input class="form-control form-control-sidebar" type="search" placeholder="Pesquisar" aria-label="Search">
				<div class="input-group-append">
					<button class="btn btn-sidebar">
						<i class="fas fa-search fa-fw"></i>
					</button>
				</div>
			</div>
		</div>

		<!-- INÍCIO MENU-->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				<li class="nav-item">
					<a href="<?php echo base_url("Dashboard")?>" class="nav-link">
						<i class="nav-icon fas fa-home"></i>
						<p>
							Início
							<i class="right fas fa-angle-left"></i>
							<span class="badge badge-info right"></span>
							<span class="badge badge-danger right"></span>
						</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link"  data-toggle="modal" data-target="#Pessoais">
						<i class="nav-icon fas fa-id-card"></i>
						<p>
							Dados Pessoais
							<i class="right fas fa-angle-left"></i>
							<span class="badge badge-info right"></span>
							<span class="badge badge-danger right"></span>
						</p>
					</a>
					<!-- multiNivel--
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="../../index.html" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Dashboard v1</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="../../index2.html" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Dashboard v2</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="../../index3.html" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Dashboard v3</p>
							</a>
						</li>
					</ul>
					-->
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link" data-toggle="modal" data-target="#Divulgacao">
						<i class="nav-icon fas fa-th"></i>
						<p>
							Dados Divulgação
							<i class="right fas fa-angle-left"></i>
							<span class="badge badge-info right"></span>
							<span class="badge badge-danger right"></span>
						</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link" data-toggle="modal" data-target="#Agenda">
						<i class="nav-icon far fa-calendar-alt"></i>
						<p>
							Agenda do Artista
							<i class="right fas fa-angle-left"></i>
							<span class="badge badge-info right"></span>
							<span class="badge badge-danger right"></span>
						</p>
					</a>
				</li>



							<!--
			<li class="nav-header">LABELS</li>
			<li class="nav-item">
			<a href="#" class="nav-link">
			<i class="nav-icon far fa-circle text-danger"></i>
			<p class="text">Important</p>
			</a>
			</li>
			<li class="nav-item">
			<a href="#" class="nav-link">
			<i class="nav-icon far fa-circle text-warning"></i>
			<p>Warning</p>
			</a>
			</li>
			<li class="nav-item">
			<a href="#" class="nav-link">
			<i class="nav-icon far fa-circle text-info"></i>
			<p>Informational</p>
			</a>
			</li>
			-->
			</ul>
		</nav>
	</div>
</aside>
