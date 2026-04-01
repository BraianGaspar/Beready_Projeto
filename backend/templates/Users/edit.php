<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<div class="edit-profile-container">
    <div class="profile-header">
        <div class="header-background"></div>
        <div class="profile-content">
            <div class="profile-avatar">
                <div class="avatar-placeholder">
                    <i class="fas fa-user-edit"></i>
                </div>
            </div>
            <div class="profile-info">
                <h1 class="profile-name">Editar Perfil</h1>
                <p class="profile-email">Atualize suas informações pessoais</p>
            </div>
            <div class="profile-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-arrow-left"></i> ' . __('Voltar'),
                    ['action' => 'view', $user->id],
                    ['class' => 'btn btn-back', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <div class="profile-body">
        <div class="container-fluid">
            <?= $this->Form->create($user, ['class' => 'edit-profile-form', 'id' => 'editProfileForm']) ?>
            <div class="row">
                <!-- Informações Pessoais -->
                <div class="col-lg-6 mb-4">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-circle"></i>
                                <?= __('Informações Pessoais') ?>
                            </h3>
                        </div>
                        <div class="card-body">
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
                    </div>
                </div>

                <!-- Preferências de Aprendizado -->
                <div class="col-lg-6 mb-4">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap"></i>
                                <?= __('Preferências de Aprendizado') ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Nível de Inglês</label>
                                <div class="input-container select-container">
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
                                <div class="input-container select-container">
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
                                <label class="form-label">Status</label>
                                <div class="input-container select-container">
                                    <i class="fas fa-circle input-icon"></i>
                                    <?= $this->Form->control('status', [
                                        'label' => false,
                                        'options' => [
                                            'ativo' => 'Ativo',
                                            'inativo' => 'Inativo'
                                        ],
                                        'class' => 'form-input select-input'
                                    ]) ?>
                                    <i class="fas fa-chevron-down select-arrow"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Objetivos de Aprendizado</label>
                                <div class="input-container textarea-container">
                                    <i class="fas fa-bullseye input-icon textarea-icon"></i>
                                    <?= $this->Form->control('objetivos_aprendizado', [
                                        'type' => 'textarea',
                                        'label' => false,
                                        'placeholder' => 'Descreva seus objetivos de aprendizado...',
                                        'rows' => 4,
                                        'class' => 'form-input textarea-input'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segurança -->
            <div class="row">
                <div class="col-12">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-lock"></i>
                                <?= __('Alterar Senha') ?>
                            </h3>
                            <small class="text-muted">Deixe em branco para manter a senha atual</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nova Senha</label>
                                        <div class="input-container password-container">
                                            <i class="fas fa-key input-icon"></i>
                                            <?= $this->Form->control('nova_senha', [
                                                'type' => 'password',
                                                'label' => false,
                                                'placeholder' => 'Mínimo 6 caracteres',
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
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Confirmar Nova Senha</label>
                                        <div class="input-container password-container">
                                            <i class="fas fa-key input-icon"></i>
                                            <?= $this->Form->control('confirmar_senha', [
                                                'type' => 'password',
                                                'label' => false,
                                                'placeholder' => 'Digite a senha novamente',
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="form-actions">
                        <?= $this->Html->link(
                            '<i class="fas fa-times"></i> ' . __('Cancelar'),
                            ['action' => 'view', $user->id],
                            ['class' => 'btn btn-cancel', 'escape' => false]
                        ) ?>
                        <?= $this->Form->button(
                             __('Salvar Alterações'),
                            [
                                'class' => 'btn btn-save',
                                'type' => 'submit',
                                'escape' => false,
                                'id' => 'submitButton'
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<style>
.edit-profile-container {
    min-height: 100vh;
    background: #f8f9fa;
}

.profile-header {
    position: relative;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.header-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M1200 120L0 16.48 0 0 1200 0 1200 120z" fill="%23ffffff" fill-opacity="0.1"/></svg>') bottom center no-repeat;
    background-size: cover;
}

.profile-content {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    align-items: center;
    gap: 2rem;
}

.profile-avatar {
    flex-shrink: 0;
}

.avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.avatar-placeholder i {
    font-size: 2rem;
    color: white;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: white;
}

.profile-email {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.profile-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.95rem;
}

.btn-back {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-back:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.profile-body {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem 2rem;
}

.profile-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.profile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
    border-radius: 15px 15px 0 0 !important;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-title i {
    color: #7c3aed;
}

.card-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.input-container {
    position: relative;
}

.password-container,
.select-container,
.textarea-container {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 1rem;
    z-index: 2;
}

.textarea-icon {
    top: 1.25rem;
    transform: none;
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    color: #374151;
    font-family: inherit;
}

.select-input {
    appearance: none;
    cursor: pointer;
    padding-right: 3rem;
}

.textarea-input {
    padding: 1rem 1rem 1rem 3rem;
    min-height: 120px;
    resize: vertical;
    line-height: 1.5;
}

.select-arrow {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 0.875rem;
    pointer-events: none;
}

.form-input:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    outline: none;
}

/* Password Strength */
.password-strength {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.25rem;
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
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 500;
}

/* Password Match */
.password-match {
    display: none;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.8rem;
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
    font-size: 0.9rem;
}

/* Toggle Password */
.toggle-password {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    cursor: pointer;
    font-size: 1rem;
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

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding: 1.5rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.btn-cancel {
    background: #6b7280;
    color: white;
}

.btn-cancel:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.btn-save {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
}

.btn-save:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.text-muted {
    color: #6b7280 !important;
    font-size: 0.875rem;
}

/* Validation Error */
.validation-error {
    display: none;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: #dc2626;
}

.validation-error.visible {
    display: flex;
}

/* Responsividade */
@media (max-width: 768px) {
    .profile-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .profile-actions {
        justify-content: center;
    }

    .profile-name {
        font-size: 1.75rem;
    }

    .avatar-placeholder {
        width: 70px;
        height: 70px;
    }

    .avatar-placeholder i {
        font-size: 1.75rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .profile-header {
        padding: 1.5rem 0;
    }

    .profile-name {
        font-size: 1.5rem;
    }

    .card-body {
        padding: 1rem;
    }

    .form-input {
        padding: 0.75rem 0.875rem 0.75rem 2.5rem;
    }

    .textarea-input {
        padding: 0.75rem 0.875rem 0.75rem 2.5rem;
    }

    .input-icon {
        left: 0.875rem;
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
                } else if (value.length <= 7) {
                    value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
                } else if (value.length <= 11) {
                    value = value.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                }
                
                e.target.value = value;
            }
        });

        // Permitir apenas números e teclas de controle
        telefoneInput.addEventListener('keydown', function(e) {
            const allowedKeys = [
                'Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 
                'Home', 'End', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'
            ];
            
            if (!/[\d]/.test(e.key) && !allowedKeys.includes(e.key)) {
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
    const submitButton = document.getElementById('submitButton');
    const form = document.getElementById('editProfileForm');

    // Criar elemento de erro de validação
    const validationError = document.createElement('div');
    validationError.className = 'validation-error';
    validationError.innerHTML = '<i class="fas fa-exclamation-circle"></i><span class="error-text"></span>';
    if (passwordInput && passwordInput.parentNode) {
        passwordInput.parentNode.parentNode.appendChild(validationError);
    }

    function checkPasswordStrength(password) {
        let strength = 0;
        let text = '';
        let className = '';

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        switch(strength) {
            case 0: text = 'Muito Fraca'; className = ''; break;
            case 1: text = 'Fraca'; className = 'weak'; break;
            case 2: text = 'Moderada'; className = 'medium'; break;
            case 3: text = 'Forte'; className = 'strong'; break;
            case 4: text = 'Muito Forte'; className = 'very-strong'; break;
        }

        return { text, className };
    }

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (password && confirmPassword) {
            passwordMatch.classList.add('visible');
            if (password === confirmPassword) {
                passwordMatch.classList.add('matching');
                passwordMatch.classList.remove('not-matching');
                passwordMatch.innerHTML = '<i class="fas fa-check-circle match-icon"></i><span class="match-text">As senhas coincidem</span>';
                validationError.classList.remove('visible');
                return true;
            } else {
                passwordMatch.classList.add('not-matching');
                passwordMatch.classList.remove('matching');
                passwordMatch.innerHTML = '<i class="fas fa-times-circle match-icon"></i><span class="match-text">As senhas não coincidem</span>';
                return false;
            }
        } else {
            passwordMatch.classList.remove('visible');
            validationError.classList.remove('visible');
            return true;
        }
    }

    function validateForm() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        // Se ambos os campos de senha estão vazios, permitir o envio
        if (!password && !confirmPassword) {
            validationError.classList.remove('visible');
            submitButton.disabled = false;
            return true;
        }
        
        // Verificar se as senhas coincidem
        if (password !== confirmPassword) {
            validationError.querySelector('.error-text').textContent = 'As senhas não coincidem';
            validationError.classList.add('visible');
            submitButton.disabled = true;
            return false;
        }
        
        // Verificar se a senha tem pelo menos 6 caracteres
        if (password.length > 0 && password.length < 6) {
            validationError.querySelector('.error-text').textContent = 'A senha deve ter pelo menos 6 caracteres';
            validationError.classList.add('visible');
            submitButton.disabled = true;
            return false;
        }
        
        // Se chegou aqui, as validações passaram
        validationError.classList.remove('visible');
        submitButton.disabled = false;
        return true;
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const { text, className } = checkPasswordStrength(password);
            
            strengthFill.className = 'strength-fill ' + className;
            strengthText.textContent = `Força da senha: ${text}`;
            
            checkPasswordMatch();
            validateForm();
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            checkPasswordMatch();
            validateForm();
        });
    }

    // Validar formulário antes do envio
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
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

    setupTogglePassword(document.getElementById('togglePassword'), passwordInput);
    setupTogglePassword(document.getElementById('toggleConfirmPassword'), confirmPasswordInput);

    // Validar inicialmente o formulário
    validateForm();
});
</script>