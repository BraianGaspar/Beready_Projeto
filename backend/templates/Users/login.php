<?php
/**
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= __('Beready - Login') ?></title>
    <?= $this->Html->meta('icon') ?>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #d81b60;
            --secondary-color: #6c757d;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --border-color: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            height: 100%;
            width: 100%;
        }

        /* Main Content */
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .login-header {
            background: var(--primary-color);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
        }

        .login-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(216, 27, 96, 0.1);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            top: 38px;
            right: 15px;
            cursor: pointer;
            color: var(--secondary-color);
        }

        .alternative-option {
            text-align: right;
            margin-top: 5px;
        }

        .alternative-option a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }

        .alternative-option a:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #c2185b;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: var(--secondary-color);
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider-text {
            padding: 0 15px;
            font-size: 14px;
        }

        .register-link {
            text-align: center;
            margin-bottom: 25px;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .social-login {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: white;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .social-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        .social-icon {
            font-size: 18px;
        }

        .btn-google {
            color: #db4437;
        }

        .btn-apple {
            color: #000;
        }

        .btn-facebook {
            color: #4267B2;
        }

        /* Flash Messages */
        .alert {
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        /* Remove estilos não utilizados */
        .header-content,
        .logo,
        .logo-icon,
        .logo-text,
        .nav-links {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <main class="main-content">
        <div class="login-container">
            <div class="login-header">
                <h1 class="login-title">Entrar</h1>
            </div>

            <div class="login-body">
                <!-- Flash Messages -->
                <?= $this->Flash->render() ?>
                
                <?php 
                // Verifica se há mensagens de erro específicas do CakePHP
                $flashError = $this->Flash->render('error');
                $flashAuthError = $this->Flash->render('auth');
                ?>

                <?php if ($flashError || $flashAuthError): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> 
                        <?= $flashError ?: $flashAuthError ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <?= $this->Form->create(null, [
                    'class' => 'login-form',
                    'url' => ['controller' => 'Users', 'action' => 'login']
                ]) ?>
                    <div class="form-group">
                        <label class="form-label">E-mail</label>
                        <?= $this->Form->control('email', [
                            'type' => 'email',
                            'label' => false,
                            'placeholder' => 'seu.email@exemplo.com',
                            'required' => true,
                            'class' => 'form-control',
                            'templates' => [
                                'inputContainer' => '{{content}}',
                                'error' => '<div class="error-message text-danger mt-1">{{content}}</div>'
                            ]
                        ]) ?>
                        <div class="alternative-option">
                            <?= $this->Html->link('Usar número do celular', '#') ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Senha</label>
                        <?= $this->Form->control('password', [
                            'type' => 'password',
                            'label' => false,
                            'placeholder' => 'Sua senha',
                            'required' => true,
                            'class' => 'form-control',
                            'id' => 'password-input',
                            'templates' => [
                                'inputContainer' => '{{content}}',
                                'error' => '<div class="error-message text-danger mt-1">{{content}}</div>'
                            ]
                        ]) ?>
                        <i class="fas fa-eye toggle-password" id="toggle-password"></i>
                        <div class="alternative-option">
                            <?= $this->Html->link('Esqueceu sua senha?', ['controller' => 'Users', 'action' => 'forgotPassword']) ?>
                        </div>
                    </div>

                    <?= $this->Form->button('Entrar', [
                        'class' => 'btn-login',
                        'type' => 'submit'
                    ]) ?>
                <?= $this->Form->end() ?>

                <div class="divider">
                    <span class="divider-text">OU</span>
                </div>

                <div class="register-link">
                    <?= __('Não tem uma conta?') ?>
                    <?= $this->Html->link(('Registrar-se'), ['controller' => 'Users', 'action' => 'add']) ?>
                </div>

                <div class="social-login">
                    <button type="button" class="social-btn btn-google">
                        <i class="fab fa-google social-icon"></i>
                        Continuar com o Google
                    </button>
                    <button type="button" class="social-btn btn-apple">
                        <i class="fab fa-apple social-icon"></i>
                        Continuar com o Apple
                    </button>
                    <button type="button" class="social-btn btn-facebook">
                        <i class="fab fa-facebook-f social-icon"></i>
                        Continuar com o Facebook
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para mostrar/ocultar senha -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password-input');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Alterna entre os ícones de olho
                    if (type === 'text') {
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            }

            // Foca no campo de email quando a página carrega
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html>