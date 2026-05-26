<div class="preloader">
    <div class="loader">
        <div class="ytp-spinner">
            <div class="ytp-spinner-container">
                <div class="ytp-spinner-rotator">
                    <div class="ytp-spinner-left">
                        <div class="ytp-spinner-circle"></div>
                    </div>
                    <div class="ytp-spinner-right">
                        <div class="ytp-spinner-circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ALERTAS -->
<?php if (session()->has('errors')): ?>
<div class="alert alert-danger text-center" role="alert">
    <ul class="list-unstyled mb-0">
        <?php foreach (session('errors') as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>


    <nav class="br-breadcrumb" aria-label="Breadcrumbs">
		<ol class="crumb-list" role="list">
			<li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
			<li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Candidato (Página Atual)</span></li>
		</ol>
	</nav>


<?php if(session()->has('mensagemError')): ?>
  <div class="br-message danger mrg-top-10 pdd-5">
      <div class="icon"><i class="fas fa-exclamation-triangle fa-lg"></i></div>
      <div class="content" role="alert">
          <span class="message-title">ATENÇÃO!</span>
          <div class="message-body">
              <div class="content"><?= esc(session('mensagemError')) ?></div>
          </div>
      </div>
  </div>
<?php elseif(session()->has('mensagemSuccess')):  ?>
  <div class="br-message success mrg-top-10 pdd-5">
      <div class="icon"><i class="fas fa-check-circle fa-lg"></i></div>
      <div class="content" role="alert">
          <span class="message-title">ATENÇÃO!</span>
          <div class="message-body">
              <div class="content"><?= esc(session('mensagemSuccess')) ?></div>
          </div>
      </div>
  </div>
<?php else: ?>
  <div class="br-message info mrg-top-10 pdd-5">
    <div class="icon"><i class="fas fa-info-circle fa-lg"></i></div>

    <div class="content" role="alert">
        <span class="message-title">MANTENHA SEUS DADOS ATUALIZADOS!</span>
        <div class="message-body">
            Mantenha seus dados pessoais e acadêmicos sempre atualizados. Confira email, telefone e o período em que está cursando.
        </div>
    </div>
</div>
<?php endif; ?>




<!-- BOTÃO VOLTAR -->
<div class="mrg-top-10">
    <a class="br-button primary voltar" href="<?= base_url("Home") ?>">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>


<!-- BLOCO DE AÇÕES -->
 <!-- CARD: DADOS PESSOAIS -->
  <div class="mt-4 row">
    <div class="col-12  smb-3">
      <a href="<?= base_url("Cadastros/dadosCandidato/") ?>" class="text-decoration-none">
        <div class="br-card hover h-100">
          <div class="card-content">
            <div class="d-flex align-items-center">

              <div class="mr-3">
                <img src="<?= base_url("external/img/icones/pessoais.png") ?>" 
                    class="br-avatar" 
                    style="width: 56px; height: 56px;">
              </div>

              <div>
                <h4 class="mb-1 text-weight-semi-bold text-gray-80">
                  Registrar Dados Pessoais
                </h4>
                <p class="mb-0 text-gray-60">
                  Registrar ou alterar seus dados pessoais
                </p>
              </div>

            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
  <hr>
  <?php if($status == "registrado"){?>
  <!-- CARD: DADOS CLASSIFICATÓRIOS -->
   <legend>Cargos Disponíveis</legend>
    <div class="row mt-4">
      <?php 
        foreach($editais as $edital){
            if($edital->fk_id_cargo != null){
                $ano = substr($edital->ds_numero_edital, -4);            // últimos 4 dígitos → ano
                        $numero = substr($edital->ds_numero_edital, 0, -4);      // o que sobra → número do edital
                        // remove zeros à esquerda
                        $numero = ltrim($numero, "0");
                        $numeroEdital =  $numero . '/' . $ano;
      ?>
    <div class="col-12 col-md-6 col-lg-6 mb-3">
      <a href="<?= base_url("Cadastros/dadosClassificatorios/".$edital->fk_id_edital."/".$edital->fk_id_cargo."/".$candidato) ?>" class="text-decoration-none">
        <div class="br-card hover h-100">
        <div class="card-content">
          <div class="d-flex align-items-center">

            <div class="mr-3">
              <img src="<?= base_url("external/img/icones/academicos.png") ?>" 
                   class="br-avatar" 
                   style="width: 56px; height: 56px;">
            </div>

            <div>
              <h4 class="mb-1 text-weight-semi-bold text-gray-80">
                <?php echo $edital->ds_nome_cargo;?>
              </h4>
              <p class="mb-0 text-gray-60">
                Edital <?php echo $numeroEdital;?>
              </p>
            </div>

          </div>
        </div>
      </div>
    </a>
  </div>
      <?php        
          } else {
                  $nomeCargo = "Sem cargo vinculado";
              }
          }
      ?>
</div>
  <?php } ?>
  <hr>
  <legend>Meus Cadastros</legend>
  <?php
    foreach($protocolos as $protocolo){
        
       
        $ano = substr($protocolo->ds_numero_edital, -4);            // últimos 4 dígitos → ano
                $numero = substr($protocolo->ds_numero_edital, 0, -4);      // o que sobra → número do edital
                // remove zeros à esquerda
                $numero = ltrim($numero, "0");
                $numeroEdital =  $numero . '/' . $ano;
  ?>
    <div class="col-12 col-md-6 col-lg-4 mb-3">
      <a href="<?= base_url("Cadastros/gerarComprovante/".$protocolo->fk_id_edital."/".$protocolo->fk_id_cargo."/".$protocolo->fk_id_cadastrado) ?>" class="text-decoration-none">
        <div class="br-card hover h-100">
          <div class="card-content">
            <div class="d-flex align-items-center">

              <div class="mr-3">
                <img src="<?= base_url("external/img/icones/sugestao.png") ?>" 
                    class="br-avatar" 
                    style="width: 56px; height: 56px;">
              </div>

              <div>
                <h4 class="mb-1 text-weight-semi-bold text-gray-80">
                  Protocolo: <?= $protocolo->ds_protocolo ?>
                </h4>
                <p class="mb-0 text-gray-60">
                  <?= $protocolo->ds_nome_cargo ?> - Edital <?= $numeroEdital ?>
                </p>
              </div>

            </div>
          </div>
        </div>
      </a>
    </div>
  <?php } ?>

