<div class="main-content pl-sm-3 mt-0" id="main-content">

    <nav class="br-breadcrumb" aria-label="Breadcrumbs">
        <ol class="crumb-list" role="list">
            <li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
            <li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Verificar Termo de Compromisso (Página Atual)</span>
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
	<div class="bloco-central col-md-12 mrg-top-30 mrg-bottom-30">
		<div class="col-md-12">
            <?php if(!$dadosContrato): ?>
                <h2 class="font22">Código de verificação inválido.</h2>
            <?php else: ?>
                <h2 class="font22 text-center">Termo de Compromisso de Estágio verificado com sucesso!</h2>
                <p class="font18 mrg-top-20 text-center"><strong>Estagiário:</strong> <?= esc($dadosContrato['candidato']) ?></p>
                <p class="font18 mrg-top-10 text-center"><strong>Data Início:</strong> <?= esc(date('d/m/Y', strtotime($dadosContrato['dataInicio']))) ?></p>
                <p class="font18 mrg-top-10 text-center"><strong>Data Término:</strong> <?= esc(date('d/m/Y', strtotime($dadosContrato['dataTermino']))) ?></p>
            <?php endif; ?>
    	</div>
        <div class="col-md-12 centra-horizontal centra-vertical">
            <a class="br-button secondary col-md-6 ta-center" href="<?php echo base_url("Home")?>">Voltar</a>
		</div>
	</div>
</div>
			