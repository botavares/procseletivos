<div class="login-body">
	<div class="container-fluid bg-login">
		<div class="container">
			<div class="row">
				<div class="col-lg-9 col-md-12 login-card">
					<div class="row">
						<div class="col-md-5 detail-part">
							
						</div>
						<div class="col-md-7 logn-part">
							<div class="row">
								<div class="col-lg-10 col-md-12 mx-auto">
									<div class="logo-cover">
										<img src="<?php echo base_url("external/img/logo.jpg")?>" alt="">
									</div>
									<div class="form-cover">
										<?php if(session()->has('error')):?>
											
										<div class="porta-mensagem alert alert-danger col-md-12">
											<span class="text text-warning text-center bold"><?php echo session()->getFlashdata('error')?></span>
										</div>
										<?php endif ?>
										<?php if(session()->has('success')):?>
											
											<div class="porta-mensagem alert alert-success col-md-12">
												<span class="text text-warning text-center bold"><?php echo session()->getFlashdata('success')?></span>
											</div>
										<?php endif ?>
										<?php if(session()->has('info')):?>
											
											<div class="porta-mensagem alert alert-warning col-md-12">
												<span class="text text-success text-center bold"><?php echo session()->getFlashdata('info')?></span>
											</div>
										<?php endif ?>
										<form method="POST" action="<?php echo url_to('login.acesso') ?>">
											<?php echo csrf_field() ?>
											<h6>Faça seu Login</h6>
											<span class="text text-danger"><?php echo session()->getFlashdata('errors')['usuario'] ?? ''?></span>
											<input name="user" placeholder="Entre com seu usuário" type="text" class="form-control" required/>
									
											<span class="text text-danger"><?php echo session()->getFlashdata('errors')['senha'] ?? ''?></span> 
											<input name="senha" type="password" Placeholder="Entre sua senha"  class="form-control" required>
											<div class="row form-footer">
												<div class="col-md-6 forget-paswd">
													
												</div>
												<div class="col-md-6 button-div">
													<button class="btn btn-primary" type="submit">Acessar</button>
												</div>
											</div>
											<h1 class="text-center font16">GERENCIAMENTO DE PROCESSOS SELETIVOS PARA ESTAGIÁRIOS</h1>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
    	</div>
	</div>
</div>



<!--
ALTERAR SENHA
-->
<div class="modal fade" id="alterarSenha" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog bg-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
		  <h5 class="modal-title" id="eformComprovanteTitle">Alterar Senha</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
		<div class="modal-body">
			<form method="POST" action="<?php echo base_url()."/Login/alterarSenha"?>">
				<?php echo csrf_field() ?>						
				<div class="form-group">
					<label class="fnt14 col-md-12 col-sm-12 col-xs-12">Digite a senha atual:</label>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<input type="password" name="senhaAtual" class="form-control" id="input-senhaatual" required>
					</div>
				</div>
				<div class="form-group">
					<label class="fnt14 col-md-12 col-sm-12 col-xs-12">Digite a nova senha:</label>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<input type="password" name="senhaNova" class="form-control" id="input-senhanova" required>
					</div>
				</div>
		
				<div class="form-group">
					<div class="col-md-12 col-sm-12 col-xs-12 mt10 mb10">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
						<button type="submit" class="btn btn-success botao-refresh">Enviar</button>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
		</div>
        
      </div>
    </div>
  </div>

<!--
NÃO CADASTRADO
-->
<div class="modal fade" id="primeiroacesso" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog  bg-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
		   <h5 class="modal-title" id="eformComprovanteTitle">Primerio Acesso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
		<div class="modal-body">
			<form method="POST" action="<?php echo base_url()."Acesso/primeiroAcesso"?>">
          
				<div class="form-group">
					<div class="input-group">
						<label class="fnt14 col-md-12 col-sm-12 col-xs-12">Digite seu CPF ou CNPJ:</label>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<input type="text" name="documento" class="form-control documento" id="input-docs">
							<p class="red"></p>
							<p class="red"></p>
						</div>
					</div>
				</div>
		
				<div class="form-group mt10 mb10">
					<div class="input-group">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
							<button type="submit" class="btn btn-success botao-refresh">Enviar</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
		</div>
        
      </div>
    </div>
  </div>

<!--
COMPROVANTE DE CADASTRO
-->
<div class="modal fade " id="formComprovante" tabindex="-1" role="dialog" aria-labelledby="formComprovanteTitle" aria-hidden="true">
  <div class="modal-dialog bg-modal" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title mx-auto" id="eformComprovanteTitle">Imprimir Segunda Via do Comprovante</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="<?php echo base_url()."Artistas/imprimirComprovante"?>">
          
				<div class="form-group">
					<div class="input-group">
						<label class="fnt14 col-md-12 col-sm-12 col-xs-12">Digite seu CPF ou CNPJ:</label>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<input type="text" name="documento" class="form-control documento" id="input-documentos">
						</div>
					</div>
				</div>
		
				<div class="form-group">
					<div class="input-group">
						<div class="col-md-12 col-sm-12 col-xs-12 mt10 mb10">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
							<button type="submit" class="btn btn-success botao-refresh">Enviar</button>
						</div>
					</div>
				</div>
			</form>
      </div>
      <div class="modal-footer">
       <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary">Imprimir</button>-->
      </div>
    </div>
  </div>
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