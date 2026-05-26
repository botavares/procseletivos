<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Governo Digital</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="<?= base_url("external/img/favicon.png") ?>">

  <!-- Fonte GOV.BR -->
  <link rel="stylesheet" href="https://cdngovbr-ds.estaleiro.serpro.gov.br/design-system/fonts/rawline/css/rawline.css"/>

  <!-- GOV.BR Core -->
  <link rel="stylesheet" href="<?= base_url("external/core/core.css") ?>">

  <!-- Ícones -->
  <link rel="stylesheet" href="<?= base_url("external/css/fontawesome5promaster/css/all.css") ?>">

  <!-- Projeto -->
  <link rel="stylesheet" href="<?= base_url("external/css/capa.css") ?>">
  <link rel="stylesheet" href="<?= base_url("external/css/contrast.css") ?>">
  <link rel="stylesheet" href="<?= base_url("external/css/padroes.css") ?>">

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
        <a href="https://www.divinopolis.mg.gov.br">
          <img src="<?= base_url("external/img/logo/logo.png") ?>" alt="Prefeitura de Divinópolis" height="48">
        </a>
      </div>

      <!-- AÇÕES -->
      <div class="header-actions">

        <!-- CONTRASTE -->
        <div class="header-links">
          <button class="br-button circle small" onclick="window.toggleContrast()" aria-label="Alto contraste">
            <i class="fas fa-adjust"></i>
          </button>
        </div>

        <span class="br-divider vertical mx-1"></span>

        <!-- LINK PORTAL -->
        <div class="header-links">
          <a href="https://www.divinopolis.mg.gov.br" class="br-button small secondary">
            Portal da Prefeitura
          </a>
        </div>

      </div>
    </div>

    <!-- HEADER BOTTOM -->
    <div class="header-bottom">
      <div class="header-menu">

        <!-- BOTÃO MENU -->
        <div class="header-menu-trigger" id="header-navigation">
          <button class="br-button small circle" data-toggle="menu" data-target="#main-navigation">
            <i class="fas fa-bars"></i>
          </button>
        </div>

        <!-- TÍTULO -->
        <div class="header-info">
          <div class="header-title">Governo Digital</div>
          <div class="header-subtitle">Portal de Serviços Integrados</div>
        </div>

      </div>
    </div>

  </div>
</header>
