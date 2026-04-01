<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tag $tag
 */
?>
<div class="register-container">
    <div class="register-card">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <h1 class="card-title">Editar Tag</h1>
            <p class="card-subtitle">Atualize as informações da tag</p>
        </div>

        <div class="card-body">
            <div class="flash-messages-container">
                <?= $this->Flash->render() ?>
            </div>

            <?= $this->Form->create($tag, ['class' => 'register-form']) ?>
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informações da Tag
                    </h3>

                    <div class="form-group">
                        <label class="form-label">Nome da Tag</label>
                        <div class="input-container">
                            <i class="fas fa-tag input-icon"></i>
                            <?= $this->Form->control('nome', [
                                'label' => false,
                                'placeholder' => 'Ex: Gramática, Vocabulário, Verbos...',
                                'class' => 'form-input'
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cor</label>
                        <div class="input-container">
                            <i class="fas fa-palette input-icon"></i>
                            <?= $this->Form->control('cor', [
                                'label' => false,
                                'placeholder' => '#7c3aed',
                                'class' => 'form-input'
                            ]) ?>
                        </div>
                        <small class="form-help">Use código hexadecimal (ex: #7c3aed)</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição</label>
                        <div class="input-container">
                            <i class="fas fa-align-left input-icon"></i>
                            <?= $this->Form->control('descricao', [
                                'type' => 'textarea',
                                'label' => false,
                                'placeholder' => 'Descreva o propósito desta tag...',
                                'rows' => 3,
                                'class' => 'form-input textarea-input'
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Tag</label>
                        <div class="checkbox-container">
                            <?= $this->Form->control('tag_sistema', [
                                'type' => 'checkbox',
                                'label' => ' Tag do Sistema',
                                'class' => 'checkbox-input'
                            ]) ?>
                        </div>
                        <small class="form-help">Marque se esta é uma tag padrão do sistema</small>
                    </div>
                </div>

                <div class="form-actions">
                    <?= $this->Html->link('Cancelar', ['action' => 'index'], [
                        'class' => 'btn btn-secondary'
                    ]) ?>
                    <?= $this->Form->button('Salvar Alterações', [
                        'class' => 'btn btn-primary submit-btn',
                        'type' => 'submit'
                    ]) ?>
                </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<style>
.register-container {
    padding: 20px;
    max-width: 600px;
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

.flash-messages-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    width: 100%;
    max-width: 400px;
}

.flash-messages-container .alert {
    padding: 15px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    text-align: left;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: none;
    margin-bottom: 10px;
}

.flash-messages-container .alert-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.flash-messages-container .alert-error {
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

.color-input {
    height: 50px;
    padding: 10px;
    padding-left: 50px;
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

.checkbox-input {
    margin-left: 10px;
    transform: scale(1.2);
}

.form-help {
    display: block;
    margin-top: 5px;
    font-size: 0.875rem;
    color: #6b7280;
}

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

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

    .flash-messages-container {
        position: relative;
        top: auto;
        right: auto;
        max-width: 100%;
    }
}
</style>