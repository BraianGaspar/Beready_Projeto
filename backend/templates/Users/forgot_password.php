<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="forgot-password-container">
    <div class="forgot-password-card">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="card-title">Recuperar Senha</h1>
            <p class="card-subtitle">Digite seu e-mail para receber o link de recuperação</p>
        </div>

        <div class="card-body">
            <?= $this->Flash->render() ?>

            <?= $this->Form->create(null, ['class' => 'forgot-password-form']) ?>
                <div class="form-group">
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
                    <div class="input-help">
                        <i class="fas fa-info-circle"></i>
                        Enviaremos um link seguro para redefinir sua senha
                    </div>
                </div>

                <?= $this->Form->button(('Enviar Link de Recuperação'), [
                    'class' => 'submit-btn',
                    'type' => 'submit'
                ]) ?>
            <?= $this->Form->end() ?>

            <div class="back-link-container">
                <i class="fas fa-arrow-left"></i>
                <?= $this->Html->link(('Voltar para Login'), ['action' => 'login'], [
                    'class' => 'back-link'
                ]) ?>
            </div>
        </div>
    </div>
</div>

<style>
.forgot-password-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 20px;
}

.forgot-password-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 450px;
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

.form-group {
    margin-bottom: 30px;
}

.input-container {
    position: relative;
    margin-bottom: 12px;
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
    padding: 16px 16px 16px 50px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #f9fafb;
    color: #374151;
    font-family: inherit;
}

.form-input:focus {
    border-color: #7c3aed;
    background: white;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    outline: none;
    transform: translateY(-1px);
}

.form-input::placeholder {
    color: #9ca3af;
}

.input-help {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 12px 16px;
    border-radius: 8px;
    border-left: 4px solid #7c3aed;
}

.input-help i {
    color: #7c3aed;
    font-size: 16px;
}

.submit-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.submit-btn:hover::before {
    left: 100%;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
}

.submit-btn:active {
    transform: translateY(0);
}

.back-link-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.back-link-container i {
    color: #7c3aed;
    font-size: 14px;
}

.back-link {
    color: #7c3aed;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 8px 16px;
    border-radius: 8px;
}

.back-link:hover {
    color: #6d28d9;
    background: #faf5ff;
    text-decoration: none;
}

/* Flash Messages Styling */
.message {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    border-left: 4px solid;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 14px;
}

.message.success {
    background: #f0fdf4;
    border-left-color: #10b981;
    color: #065f46;
}

.message.error {
    background: #fef2f2;
    border-left-color: #ef4444;
    color: #991b1b;
}

.message.info {
    background: #eff6ff;
    border-left-color: #3b82f6;
    color: #1e40af;
}

.message i {
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 1px;
}

/* Responsive Design */
@media (max-width: 480px) {
    .forgot-password-container {
        padding: 10px;
        min-height: 70vh;
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

    .card-title {
        font-size: 24px;
    }

    .card-subtitle {
        font-size: 14px;
    }
}

/* Loading animation for button */
.submit-btn.loading {
    pointer-events: none;
    opacity: 0.8;
}

.submit-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.forgot-password-form');
    const submitBtn = document.querySelector('.submit-btn');

    if (form) {
        form.addEventListener('submit', function() {
            // Add loading state to button
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            // Remove loading state after form submission (or timeout for demo)
            setTimeout(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }, 3000);
        });
    }
});
</script>