<nav class="br-menu col-md-3 col-lg-3"
     id="menu-principal"
     aria-label="Menu principal">

    <div class="menu-container">
        <div class="menu-panel">

            <!-- Cabeçalho do menu -->
            <div class="menu-header">
                <div class="menu-title">
                    <span>Navegação</span>
                </div>

                <div class="menu-close">
                    <button class="br-button circle"
                            type="button"
                            data-dismiss="menu"
                            aria-label="Fechar menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Lista principal -->
            <ul class="menu-list">

                <li class="menu-item">
                    <a href="<?= base_url('/') ?>">Início</a>
                </li>

                <li class="menu-item has-children">
                    <button class="menu-toggle"
                            aria-expanded="false">
                        Serviços
                    </button>

                    <ul class="menu-sublist">
                        <li><a href="#">Consulta Pública</a></li>
                        <li><a href="#">Autorizações</a></li>
                        <li><a href="#">Fiscalização</a></li>
                    </ul>
                </li>

                <li class="menu-item has-children">
                    <button class="menu-toggle"
                            aria-expanded="false">
                        Institucional
                    </button>

                    <ul class="menu-sublist">
                        <li><a href="#">Quem Somos</a></li>
                        <li><a href="#">Estrutura</a></li>
                        <li><a href="#">Legislação</a></li>
                    </ul>
                </li>

                <li class="menu-item">
                    <a href="#">Contato</a>
                </li>

            </ul>

        </div>
    </div>
</nav>
