<div class="login-wrapper">
    <div class="login-overlay">
        <div class="login-container">
            
            <!-- Coluna esquerda: formulário de login -->
            <div class="login-form-section">
                <div class="login-brand">
                    <img src="<?php echo base_url('external/img/logo/prefeitura.jpg') ?>" alt="Prefeitura Municipal de Divinópolis" class="login-secretaria">
                    <h1 class="login-title">Processos Seletivos - SEPLAG</h1>
                    <p class="login-subtitle">Acesso ao Painel Administrativo</p>
                </div>

                <!-- Alertas -->
                <?php if(session()->has('error')): ?>
                    <div class="login-alert login-alert--danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo esc(session()->getFlashdata('error')) ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session()->has('success')): ?>
                    <div class="login-alert login-alert--success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo esc(session()->getFlashdata('success')) ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session()->has('info')): ?>
                    <div class="login-alert login-alert--info">
                        <i class="fas fa-info-circle"></i>
                        <span><?php echo esc(session()->getFlashdata('info')) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo url_to('login.acesso') ?>" class="login-form" autocomplete="off">
                    <?php echo csrf_field() ?>

                    <div class="form-group">
                        <label for="login-user" class="login-label">Usuário</label>
                        <div class="login-input-group">
                            <i class="fas fa-user login-input-icon"></i>
                            <input 
                                type="text" 
                                name="user" 
                                id="login-user" 
                                class="login-input" 
                                placeholder="Digite seu usuário" 
                                required 
                                autocomplete="username"
                                maxlength="50"
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="login-senha" class="login-label">Senha</label>
                        <div class="login-input-group">
                            <i class="fas fa-lock login-input-icon"></i>
                            <input 
                                type="password" 
                                name="senha" 
                                id="login-senha" 
                                class="login-input" 
                                placeholder="Digite sua senha" 
                                required 
                                autocomplete="current-password"
                                maxlength="100"
                            >
                            <button type="button" class="login-toggle-password" onclick="toggleSenha()" title="Mostrar/Ocultar senha">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="login-options">
                        <a href="#" data-toggle="modal" data-target="#modalEsqueciSenha" class="login-forgot">
                            Esqueceu sua senha?
                        </a>
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Acessar
                    </button>

                </form>

                <div class="login-footer">
                    <p><i class="fas fa-shield-alt"></i> Ambiente seguro</p>
                    <p>&copy; <?php echo date('Y') ?> Prefeitura Municipal de Divinópolis</p>
                </div>
            </div>

            <!-- Coluna direita: imagem ilustrativa -->
            <div class="login-image-section">
                <div class="login-image-overlay">
                    <div class="login-image-content">
                        <i class="fas fa-briefcase login-image-icon"></i>
                        <h2>Processos Seletivos - SEPLAG</h2>
                        <p>Gerencie editais, candidatos, classificações e convocações de forma integrada e eficiente.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal: Esqueci a Senha -->
<div class="modal fade" id="modalEsqueciSenha" tabindex="-1" role="dialog" aria-labelledby="modalEsqueciSenhaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content login-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEsqueciSenhaLabel"><i class="fas fa-key"></i> Recuperação de Senha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>

            <form method="POST" action="#" onsubmit="return false;">
                <div class="modal-body">
                    <p class="login-modal-text">
                        Informe seu <strong>nome de usuário</strong>. Uma nova senha será gerada e enviada ao e-mail cadastrado.
                    </p>
                    <div class="form-group">
                        <label for="recupera-usuario" class="login-label">Usuário</label>
                        <div class="login-input-group">
                            <i class="fas fa-user login-input-icon"></i>
                            <input
                                type="text"
                                name="usuario"
                                id="recupera-usuario"
                                class="login-input"
                                placeholder="Digite seu nome de usuário"
                                required
                                maxlength="50"
                            >
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" disabled title="Funcionalidade em desenvolvimento">
                        <i class="fas fa-paper-plane"></i> Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSenha() {
    const input = document.getElementById('login-senha');
    const icon  = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
