<div class="main-content pl-sm-3 mt-0" id="main-content">

    <nav class="br-breadcrumb" aria-label="Breadcrumbs">
        <ol class="crumb-list" role="list">
            <li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
            <li class="crumb"><i class="icon fas fa-chevron-right"></i><a href="<?php echo base_url("Cadastros")?>">Pessoais/Academicos</a>
            </li>
            <li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Registro concluído</span>
            </li>
        </ol>
    </nav>


    <section class="bloco-superior bg-silver pdd-5 mrg-bottom-30 chanfrado">
                    <?php if (session()->has('errors')): ?>
    					<div class="alert bg-red-vivid-50 font22 branco text-center area-alertas" role="alert">
							<ul class="list-unstyled mb-0">
								<?php foreach (session('errors') as $error): ?>
									<li style="list-style: none"><?= esc($error) ?></li>
								<?php endforeach; ?>
							</ul>
    					</div>
					<?php endif; 
                     if (session()->has('mensagemError')): ?>
						<div class="alert bg-red-vivid-50 text-center area-alertas font22 branco" role="alert">
							<?= esc(session('mensagemError')) ?>
						</div>
					<?php endif; ?>
	</section>
	<div class="bloco-central col-md-12 mrg-top-30 mrg-bottom-30 centra-horizontal">
		<div class="col-md-6 centra-horizontal centra-vertical">
			<a class="br-button primary col-md-12 ta-center" href="<?php echo base_url("Cadastros/dadosAcademicos/$idCandidato")?>" target="_blank">Registrar Dados Acadêmicos</a>
    	</div>
        <div class="col-md-6 centra-horizontal centra-vertical">
            <a class="br-button secondary col-md-12 ta-center" href="<?php echo base_url("Cadastros")?>">Voltar</a>
		</div>
	</div>
</div>
			