<div class="main-content pl-sm-3 mt-0" id="main-content">
	<nav class="br-breadcrumb" aria-label="Breadcrumbs">
		<ol class="crumb-list" role="list">
            <li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
            </li>
            <li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Perguntas Frequentes (Página Atual)</span>
            </li>
        </ol>
    </nav>
    <section class="bloco-superior bg-silver pdd-5 mrg-bottom-20 chanfrado">
        <h1 class="bold font-blue">Perguntas Frequentes</h1>
    </section>
    <section class="content">
		<div class="container-fluid">
			<div class="col-md-12">
					<?php if(session()->has('error')):?>
						<div class="porta-mensagem alert alert-danger col-md-12">
							<ul>
								<?php foreach(session()->getFlashdata('error') as $valueError): ?>
									<li class="text text-white text-center font22"><?php echo $valueError ?></li>
								<?php endforeach ?>
							</ul>
						</div>
					<?php endif ?>
					<?php if(session()->has('success')):?>
						<div class="porta-mensagem alert alert-success col-md-12">
							<span class="text text-white text-center bold font22"><?php echo session()->getFlashdata('success')?></span>
						</div>
					<?php endif ?>
			</div>
			<table id="tabela-perguntas" class=" minhaDataTable table">
				<thead>
					<tr>
						
						<?php
							for($i = 0,$j=count($titulosTabela);$i<$j;$i++){
						?>
						<th  align="center" class="font-18 bold"><?php echo $titulosTabela[$i]?></th>
						<?php 
							}
						?>
						<th  align="center" class="font-18 bold">Resposta</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($dados as $valuePerguntas){
					?>
					<?php
							$tokenName = csrf_token();
							$tokenHash = csrf_hash();
					?>
					<tr class="toggle-detalhes" data-id="<?php echo $valuePerguntas->pk_id_pergunta?>">
						<td align="left" class="font-16"><?php echo $valuePerguntas->ds_pergunta?></td>
						<td align="center">
							<button id="<?php echo $valuePerguntas->pk_id_pergunta?>" class="btn btn-primary btn-sm toggle-detalhes" data-id="<?php echo $valuePerguntas->pk_id_pergunta?>">
								<i class="fas fa-chevron-down font-blue"></i>
							</button>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="area-botoes mrg-bottom-10 centra-horizontal" >
                <a  class="br-button block secondary mb-3 col-md-6" href="<?php echo base_url("Home")?>"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
		</div>
	</section>
</div>

