<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>Processos Seletivos</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="icon" href="<?= base_url("external/img/favicon.png") ?>">

<!-- GOV.BR Font -->
<link rel="stylesheet" href="https://cdngovbr-ds.estaleiro.serpro.gov.br/design-system/fonts/rawline/css/rawline.css"/>

<!-- GOV.BR Core -->
<link rel="stylesheet" href="<?= base_url("external/core/core.css") ?>">

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css"/>

<!-- Project CSS -->
<link rel="stylesheet" href="<?= base_url("external/css/capa.css") ?>">
<link rel="stylesheet" href="<?= base_url("external/css/custom-capa.css") ?>">
<link rel="stylesheet" href="<?= base_url("external/css/contrast.css") ?>">
<link rel="stylesheet" href="<?= base_url("external/css/padroes.css") ?>">
<link rel="stylesheet" href="<?= base_url("external/css/brTables.css") ?>">

<script>
  const BASE_URL = '<?= base_url() ?>';
</script>
</head>

<body>
<div class="template-base">

<!-- Skip Links -->
<nav class="br-skiplink" role="menubar">
  <a class="br-item" href="#main-content" accesskey="1">Ir para o conteúdo</a>
  <a class="br-item" href="#header-navigation" accesskey="2">Ir para o menu</a>
  <a class="br-item" href="#footer" accesskey="4">Ir para o rodapé</a>
</nav>

<header class="br-header mb-4" id="header" data-sticky="true">
  <div class="container-lg">

    <!-- HEADER TOP -->
    <div class="header-top">

      <!-- LOGO -->
      <div class="header-logo">
        <img src="<?= base_url("external/img/logo/assinatura.jpg") ?>" alt="Prefeitura de Divinópolis">
      </div>

      <!-- ACTIONS -->
      <div class="header-actions">

        <!-- ACESSO RÁPIDO -->
         <!--
        <div class="header-links">
          <button class="br-button circle small" type="button" data-toggle="dropdown">
            <i class="fas fa-ellipsis-v"></i>
          </button>

          <div class="br-list">
            <div class="header">
              <div class="title">Acesso Rápido</div>
            </div>

            <a class="br-item" href="https://www.divinopolis.mg.gov.br" target="_blank">
              Portal Divinópolis
            </a>
          </div>
        </div>
-->
        <span class="br-divider vertical mx-1"></span>

        <!-- FUNCIONALIDADES -->
        <div class="header-functions dropdown">
          <button class="br-button circle small" type="button" data-toggle="dropdown" data-target="header-functions" aria-label="Funcionalidades">
            <i class="fas fa-th"></i>
          </button>

          <div id="header-functions" class="br-list" hidden role="menu">
                <div class="header">
                    funcionalidades
                </div>
                <a class="br-item br-button circle small text-center text-info" role="menuitem" onclick="window.toggleContrast()">
                    <i title="Contraste" alt="Contraste" class="fas fa-adjust mr-2"></i>
                    <span class="text">Contraste</span>
                </a>
                <a class="br-item br-button circle small text-center text-info" role="menuitem" href="https://app.prefeituradivinopolis.com.br/contato">
                    <i title="Contato" alt="Contato" class="fas fa-comment mr-2"></i>
                    <span class="text">Contato</span>
                </a>
            </div>
        </div>

        <!-- BUSCA -->
        <!-- ainda não implementado-->
         <!--
        <div class="header-search-trigger">
          <button class="br-button circle" data-toggle="search" aria-label="Buscar">
            <i class="fas fa-search"></i>
          </button>
        </div>
-->
        <!-- PERFIL LOGADO -->
    <div class="header-login">
        <div id="header-top-actions-signin-avatar" class="header-avatar">
            <button 
                class="br-button circled-flex"
                type="button"
                data-toggle="dropdown"
                data-target="user-dropdown"
                aria-label="Abrir menu do usuário"
                alt="Menu do usuário"
            >
                <span class="br-avatar" title="<?= session('nome') ?>">
                    <span class="content bg-orange-vivid-30 text-pure-0">
                        <?= strtoupper(substr(session('nome'), 0, 1)) ?>
                    </span>
                </span>

                <!--NOME E PERFIL-->
                <div id="avatar-sign-in-right" class="d-flex flex-column flex-wrap">
                    <div id="avatar-sign-in-right-name">
                        <span id="avatar-sign-in-right-name-span" class="text-gray-80 text-weight-semi-bold text-wrap text-break ml-2">
                            <?php echo strtoupper(session('nome'))?>
                        </span>                                
                    </div>
                    <!--
                    <div id="avatar-sign-in-right-profile">
                        <span id="avatar-sign-in-right-profile-span" class="text-wrap text-break">
                            Usuário
                        </span>
                    </div>
                    -->
                </div>
            </button>
            <!--DROPDWON-->
            <div id="user-dropdown" class="br-list" hidden role="menu">
                <a class="br-item text-info" href="<?= base_url("logOut") ?>" role="menuitem">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sair
                </a>
            </div>
        </div>
    </div>

<!-- fim PERFIL LOGADO -->
      </div>
    </div>

    <!-- HEADER BOTTOM -->
    <div class="header-bottom">
      <div class="header-menu">

        <div class="header-menu-trigger" id="header-navigation">
          <button class="br-button small circle" data-toggle="menu" data-target="#main-navigation">
            <i class="fas fa-bars"></i>
          </button>
        </div>

        <div class="header-info">
          <div class="header-title">Processos Seletivos</div>
          <div class="header-subtitle">Plataforma Integrada ao GovDigital</div>
        </div>

      </div>
    </div>

  </div>
</header>

<main id="main-content" class="d-flex flex-fill mb-5">
  <div class="container-lg d-flex">
    <div class="row">

      <!-- MENU LATERAL -->
      <nav class="br-menu" id="main-navigation" aria-label="Menu principal">
        <div class="menu-container">
          <div class="menu-panel">

            <div class="menu-header">
              <div class="menu-title">Menu</div>
            </div>

            <div class="menu-body">

              <a class="menu-item divider" href="https://servicos.prefeituradivinopolis.com.br/govdigital/">
                <span class="icon"><i class="fas fa-bell"></i></span>
                <span class="content">Serviços Digitais</span>
              </a>

              <a class="menu-item divider" href="<?= base_url("logOut") ?>">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="content">Sair</span>
              </a>

            </div>

            <div class="menu-footer">
              <div class="menu-info text-down-01 text-center">
                Conteúdo sob licença Creative Commons
              </div>
            </div>

          </div>

          <div class="menu-scrim" data-dismiss="menu"></div>
        </div>
      </nav>

      <!-- CONTEÚDO -->
      <div class="col mb-5">
