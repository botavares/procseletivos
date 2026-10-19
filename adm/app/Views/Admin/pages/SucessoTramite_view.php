<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-md-6 col-sm-12">
					<h1 class="maiuscula"><?php echo $titulo?></h1>
				</div>
				<div class="col-md-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Início</a></li>
						<li class="breadcrumb-item active">cadastros</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div   class="card">
			<div class="card-header">
				<?php
					if(session()->has('mensagemok')):?>
						<div id="porta-mensagem">
							<div class="mensagem alert alert-success alert-block alert-aling" role="alert">
								<?php echo $this->session->flashdata('mensagemok')?>
							</div>
						</div>
				<?php endif;?>
				<?php
					if(session()->has('mensagemerror')):?>
						<div id="porta-mensagem">
							<div class="mensagem alert alert-danger alert-block alert-aling" role="alert">
								<?php echo $this->session->flashdata('mensagemerror')?>
							</div>
						</div>
				<?php endif;?>
				<a class="btn btn-warning col-md-2 mrig20 chanfrado" href="<?php echo base_url('/Manifestacoes/gerenciarManifestos/')?>" class="btn btn-primary">Voltar</a>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
					<button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
						<i class="fas fa-times"></i>
					</button>
				</div>
			</div>
			<h3 class="card-title maiuscula ta-center font24 green bold"><?php echo $mensagem?></h3>
			<div class="card-body row pdd20 centra-horizontal">
				<a class="btn btn-primary col-md-5 mrig20" href="<?php echo base_url('/Tramites/imprimirFormularioEncaminhamento/'.$idManifesto)?>" target="_blank" class="btn btn-primary">Imprimir Formulario de Encaminhamento</a>
			</div>

			<div class="card-footer">

			</div>

		</div>

	</section>

</div>



<footer class="main-footer">
<div class="float-right d-none d-sm-block">
<b>Versão</b> 1.0.0
</div>
<strong>Copyright &copy; <?php echo date('Y')?> <a href="https://www.divinópolis.mg.gov.br">Prefeitura Municipal de Divinópolis</a></strong>.
</footer>

<aside class="control-sidebar control-sidebar-dark">

</aside>

</div>



























<div class="modal fade" id="modalErros" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				
				<h5 id="nome-erro" class="modal-title font25 red bold ta-center"> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
        		</button>
        		
        		
      		</div>
      		<div class="modal-body">
        		<p id="descricao-erro" class="font20 "></p>
		  		
			</div>
			<div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal"><strong>Retornar</strong></button>
		
			</div>	
    	</div>
  	</div>
</div>



