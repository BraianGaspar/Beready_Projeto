<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="register-container">
    <div class="register-card">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="card-title">Criar Conta</h1>
            <p class="card-subtitle">Junte-se à nossa comunidade de aprendizado</p>
        </div>

        <div class="card-body">
            <!-- Flash Messages Centralizadas e Melhoradas -->
            <div class="flash-messages-container">
                <?= $this->Flash->render() ?>
            </div>

            <?= $this->Form->create($user, ['class' => 'register-form']) ?>
              <?= $this->Form->hidden('status', ['value' => 'ativo']) ?>
                <div class="form-grid">
                    <!-- Informações Básicas -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            Informações Pessoais
                        </h3>

                        <div class="form-group">
                            <label class="form-label">Nome Completo *</label>
                            <div class="input-container">
                                <i class="fas fa-user input-icon"></i>
                                <?= $this->Form->control('nome', [
                                    'label' => false,
                                    'placeholder' => 'Seu nome completo',
                                    'required' => true,
                                    'class' => 'form-input'
                                ]) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">E-mail *</label>
                            <div class="input-container">
                                <i class="fas fa-envelope input-icon"></i>
                                <?= $this->Form->control('email', [
                                    'type' => 'email',
                                    'label' => false,
                                    'placeholder' => 'seu.email@exemplo.com',
                                    'required' => true,
                                    'class' => 'form-input'
                                ]) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Telefone</label>
                            <div class="input-container">
                                <i class="fas fa-phone input-icon"></i>
                                <?= $this->Form->control('telefone', [
                                    'label' => false,
                                    'placeholder' => '(99) 99999-9999',
                                    'class' => 'form-input telefone-input',
                                    'id' => 'telefone',
                                    'maxlength' => 15
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Segurança -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-lock"></i>
                            Segurança
                        </h3>

                        <div class="form-group">
                            <label class="form-label">Senha *</label>
                            <div class="input-container password-container">
                                <i class="fas fa-key input-icon"></i>
                                <?= $this->Form->control('senha', [
                                    'type' => 'password',
                                    'label' => false,
                                    'placeholder' => 'Crie uma senha segura',
                                    'required' => true,
                                    'class' => 'form-input password-input',
                                    'id' => 'password'
                                ]) ?>
                                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                            </div>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strength-fill"></div>
                                </div>
                                <div class="strength-text" id="strength-text">Força da senha</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirmar Senha *</label>
                            <div class="input-container password-container">
                                <i class="fas fa-key input-icon"></i>
                                <?= $this->Form->control('confirmar_senha', [
                                    'type' => 'password',
                                    'label' => false,
                                    'placeholder' => 'Digite a senha novamente',
                                    'required' => true,
                                    'class' => 'form-input password-input',
                                    'id' => 'confirmPassword'
                                ]) ?>
                                <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                            </div>
                            <div class="password-match" id="passwordMatch">
                                <i class="fas fa-check-circle match-icon"></i>
                                <span class="match-text">As senhas coincidem</span>
                            </div>
                        </div>
                    </div>

                    <!-- Preferências de Aprendizado -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-graduation-cap"></i>
                            Preferências de Aprendizado
                        </h3>

                        <div class="form-group">
                            <label class="form-label">Nível de Inglês</label>
                            <div class="input-container">
                                <i class="fas fa-language input-icon"></i>
                                <?= $this->Form->control('nivel_ingles', [
                                    'label' => false,
                                    'options' => [
                                        'iniciante' => 'Iniciante',
                                        'intermediario' => 'Intermediário',
                                        'avancado' => 'Avançado'
                                    ],
                                    'empty' => 'Selecione seu nível',
                                    'class' => 'form-input select-input'
                                ]) ?>
                                <i class="fas fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Idioma Preferido</label>
                            <div class="input-container">
                                <i class="fas fa-globe input-icon"></i>
                                <?= $this->Form->control('idioma_preferido', [
                                    'label' => false,
                                    'options' => [
                                        'pt-BR' => 'Português (Brasil)',
                                        'en' => 'Inglês',
                                        'es' => 'Espanhol'
                                    ],
                                    'empty' => 'Selecione o idioma',
                                    'class' => 'form-input select-input'
                                ]) ?>
                                <i class="fas fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Objetivos de Aprendizado</label>
                            <div class="input-container">
                                <i class="fas fa-bullseye input-icon"></i>
                                <?= $this->Form->control('objetivos_aprendizado', [
                                    'type' => 'textarea',
                                    'label' => false,
                                    'placeholder' => 'Descreva seus objetivos...',
                                    'rows' => 3,
                                    'class' => 'form-input textarea-input'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <?= $this->Html->link('Cancelar', ['action' => 'login'], [
                        'class' => 'btn btn-secondary'
                    ]) ?>
                    <?= $this->Form->button('Criar Minha Conta', [
                        'class' => 'btn btn-primary submit-btn',
                        'type' => 'submit'
                    ]) ?>
                </div>
            <?= $this->Form->end() ?>

            <div class="login-redirect">
                <p>Já tem uma conta?
                    <?= $this->Html->link('Fazer Login', ['action' => 'login'], [
                        'class' => 'login-link'
                    ]) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.register-container {
    padding: 20px;
    max-width: 900px;
    margin: 0 auto;
}

.register-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
    padding: 40px 32px;
    text-align: center;
    position: relative;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #8b5cf6, #a78bfa);
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 32px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.card-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: white;
}

.card-subtitle {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
    font-weight: 400;
    line-height: 1.5;
}

.card-body {
    padding: 40px;
}

/* Flash Messages Centralizadas e Melhoradas */
.flash-messages-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    width: 90%;
    max-width: 400px;
}

.flash-messages-container .alert {
    padding: 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: none;
}

.flash-messages-container .alert-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.flash-messages-container .alert-error,
.flash-messages-container .alert-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.flash-messages-container .message {
    padding: 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: none;
}

.flash-messages-container .message.success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.flash-messages-container .message.error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.form-grid {
    display: grid;
    gap: 30px;
}

.form-section {
    background: #f8fafc;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
}

.section-title i {
    color: #7c3aed;
    font-size: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.input-container {
    position: relative;
}

.password-container {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 18px;
    z-index: 2;
}

.form-input {
    width: 100%;
    padding: 14px 16px 14px 50px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: white;
    color: #374151;
    font-family: inherit;
}

.telefone-input {
    padding-right: 16px !important;
}

.password-input {
    padding-right: 50px !important;
}

.select-input {
    appearance: none;
    cursor: pointer;
}

.select-arrow {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 14px;
    pointer-events: none;
}

.textarea-input {
    resize: vertical;
    min-height: 80px;
    padding: 14px 16px;
}

.form-input:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    outline: none;
    transform: translateY(-1px);
}

/* Password Strength */
.password-strength {
    margin-top: 8px;
}

.strength-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 6px;
}

.strength-fill {
    height: 100%;
    background: #ef4444;
    transition: all 0.3s ease;
    width: 0%;
    border-radius: 3px;
}

.strength-fill.weak { background: #ef4444; width: 25%; }
.strength-fill.medium { background: #f59e0b; width: 50%; }
.strength-fill.strong { background: #10b981; width: 75%; }
.strength-fill.very-strong { background: #059669; width: 100%; }

.strength-text {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

/* Password Match */
.password-match {
    display: none;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    font-size: 12px;
    font-weight: 500;
}

.password-match.visible {
    display: flex;
}

.password-match.matching {
    color: #059669;
}

.password-match.not-matching {
    color: #dc2626;
}

.match-icon {
    font-size: 14px;
}

/* Toggle Password */
.toggle-password {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    cursor: pointer;
    font-size: 18px;
    z-index: 2;
    background: none;
    border: none;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-password:hover {
    color: #374151;
}

/* Buttons */
.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 28px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    min-width: 140px;
}

.btn-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
}

.btn-secondary {
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
}

.btn-secondary:hover {
    border-color: #9ca3af;
    transform: translateY(-2px);
}

/* Login Redirect */
.login-redirect {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    color: #6b7280;
}

.login-link {
    color: #7c3aed;
    text-decoration: none;
    font-weight: 600;
}

.login-link:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .register-container {
        padding: 10px;
    }

    .card-body {
        padding: 24px;
    }

    .card-header {
        padding: 30px 24px;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }

    .form-grid {
        gap: 20px;
    }

    .form-section {
        padding: 20px;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

    .flash-messages-container {
        width: 95%;
        max-width: 350px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara de telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                if (value.length <= 2) {
                    value = value.replace(/^(\d{0,2})/, '($1');
                } else if (value.length <= 6) {
                    value = value.replace(/^(\d{2})(\d{0,4})/, '($1) $2');
                } else if (value.length <= 10) {
                    value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                } else {
                    value = value.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                }
                
                e.target.value = value;
            }
        });

        // Permitir apenas backspace, delete, tab, esc, enter e .
        telefoneInput.addEventListener('keydown', function(e) {
            if (!/[0-9]|Backspace|Delete|Tab|Escape|Enter|\./.test(e.key)) {
                e.preventDefault();
            }
        });
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    const passwordMatch = document.getElementById('passwordMatch');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    // Função para verificar força da senha
    function checkPasswordStrength(password) {
        let strength = 0;
        let text = '';
        let className = '';

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        switch(strength) {
            case 0:
                text = 'Muito Fraca';
                className = '';
                break;
            case 1:
                text = 'Fraca';
                className = 'weak';
                break;
            case 2:
                text = 'Moderada';
                className = 'medium';
                break;
            case 3:
                text = 'Forte';
                className = 'strong';
                break;
            case 4:
                text = 'Muito Forte';
                className = 'very-strong';
                break;
        }

        return { text, className };
    }

    // Função para verificar se as senhas coincidem
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (password && confirmPassword) {
            passwordMatch.classList.add('visible');
            if (password === confirmPassword) {
                passwordMatch.classList.add('matching');
                passwordMatch.classList.remove('not-matching');
                passwordMatch.innerHTML = '<i class="fas fa-check-circle match-icon"></i><span class="match-text">As senhas coincidem</span>';
            } else {
                passwordMatch.classList.add('not-matching');
                passwordMatch.classList.remove('matching');
                passwordMatch.innerHTML = '<i class="fas fa-times-circle match-icon"></i><span class="match-text">As senhas não coincidem</span>';
            }
        } else {
            passwordMatch.classList.remove('visible');
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const { text, className } = checkPasswordStrength(password);
            
            strengthFill.className = 'strength-fill ' + className;
            strengthText.textContent = `Força da senha: ${text}`;
            
            checkPasswordMatch();
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }

    // Toggle password visibility
    function setupTogglePassword(toggleElement, passwordElement) {
        if (toggleElement && passwordElement) {
            toggleElement.addEventListener('click', function() {
                const type = passwordElement.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordElement.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    }

    setupTogglePassword(togglePassword, passwordInput);
    setupTogglePassword(toggleConfirmPassword, confirmPasswordInput);

    // Auto-remover mensagens flash após 5 segundos
    setTimeout(function() {
        const flashMessages = document.querySelector('.flash-messages-container');
        if (flashMessages) {
            flashMessages.style.transition = 'opacity 0.5s ease';
            flashMessages.style.opacity = '0';
            setTimeout(function() {
                if (flashMessages.parentNode) {
                    flashMessages.parentNode.removeChild(flashMessages);
                }
            }, 500);
        }
    }, 5000);
});
</script>