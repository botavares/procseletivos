<div class="main-content pl-sm-3 mt-0" id="main-content">

    <nav class="br-breadcrumb" aria-label="Breadcrumbs">
        <ol class="crumb-list" role="list">
            <li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
            <li class="crumb"><i class="icon fas fa-chevron-right"></i><a href="<?php echo base_url("Cadastros")?>">Pessoais/Academicos</a>
            </li>
            <li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Confirmação de Interesse</span>
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
	<div class="bloco-central col-md-12 mrg-top-30 mrg-bottom-30 centra-horizontal">
        <div class="card pdd-20 sombra">
            <div class="card-body text-center">
                <h2 class="font18 mrg-bottom-20">Orientações Importantes</h2>
                <p class="font16">1. Verifique regularmente seu e-mail cadastrado para atualizações e comunicados importantes.</p>
                <p class="font16">2. Mantenha seus dados pessoais e acadêmicos atualizados para evitar problemas futuros.</p>
               <!-- <p class="font16">3. Em caso de dúvidas, entre em contato com a nossa equipe de suporte através do e-mail <a href="mailto:K4oE7@example.com">K4oE7@example.com</a> ou pelo telefone <a href="tel:0800 123 4567">0800 123 4567</a>.</p>-->
            </div>
        </div>

	</div>
</div>
			