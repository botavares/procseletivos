<div class="main-content bloco-central pl-sm-3 mt-0" id="main-content">

    <nav class="br-breadcrumb" aria-label="Breadcrumbs">
        <ol class="crumb-list" role="list">
            <li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
            <li class="crumb"><i class="icon fas fa-chevron-right"></i><a href="<?php echo base_url("Cadastros")?>">Pessoais/Academicos</a>
            </li>
            <li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Registro concluído</span>
            </li>
        </ol>
    </nav>


    <section class="bloco-superior bg-green-vivid-50 pdd-5 mrg-bottom-30 chanfrado">
        <?php
            if (session()->has('mensagemSuccess')): ?>
                <h1 class="text-center font22 branco"">
                    <?= esc(session('mensagemSuccess')) ?>
                </h1>
        <?php endif; ?>
					
	</section>
    <div class="col-md-12 mrg-top-30 mrg-bottom-30 centra-horizontal">
        <div class="col-md-12 centra-horizontal centra-vertical">
            <a class="br-button primary col-md-12 ta-center" href="<?php echo base_url("Cadastros/dadosAcademicos/$idCandidato")?>" target="_self">Registrar Dados Acadêmicos</a>
        </div>
    </div>
    <div class="col-md-12 mrg-bottom-30 centra-horizontal">
        <div class="col-md-12 centra-horizontal centra-vertical">
            <a class="br-button secondary col-md-12 ta-center" href="<?php echo base_url("Cadastros")?>" target="_self">Voltar</a>
        </div>
    </div>
</div>
			