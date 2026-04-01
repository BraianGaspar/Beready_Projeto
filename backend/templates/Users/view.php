<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<div class="user-profile-container">
    <div class="profile-header">
        <div class="header-background"></div>
        <div class="profile-content">
            <div class="profile-avatar">
                <div class="avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="profile-info">
                <h1 class="profile-name"><?= h($user->nome) ?></h1>
                <p class="profile-email"><?= h($user->email) ?></p>
                <div class="profile-badges">
                    <span class="badge badge-level"><?= h(ucfirst($user->nivel_ingles)) ?></span>
                    <span class="badge badge-status"><?= h(ucfirst($user->status)) ?></span>
                </div>
            </div>
            <div class="profile-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-edit"></i> ' . __('Editar'),
                    ['action' => 'edit', $user->id],
                    ['class' => 'btn btn-edit', 'escape' => false]
                ) ?>
                <?= $this->Form->postLink(
                    '<i class="fas fa-trash"></i> ' . __('Excluir'),
                    ['action' => 'delete', $user->id],
                    [
                        'confirm' => __('Tem certeza que deseja excluir o usuário {0}?', $user->nome),
                        'class' => 'btn btn-delete',
                        'escape' => false
                    ]
                ) ?>
            </div>
        </div>
    </div>

    <div class="profile-body">
        <div class="container-fluid">
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
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-phone"></i>
                                        <?= __('Telefone') ?>
                                    </div>
                                    <div class="info-value"><?= $user->telefone ? h($user->telefone) : '<span class="text-muted">Não informado</span>' ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-language"></i>
                                        <?= __('Idioma Preferido') ?>
                                    </div>
                                    <div class="info-value">
                                        <?= $user->idioma_preferido === 'pt-BR' ? 'Português (Brasil)' : 
                                             ($user->idioma_preferido === 'en' ? 'Inglês' : 
                                             ($user->idioma_preferido === 'es' ? 'Espanhol' : h($user->idioma_preferido))) ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar"></i>
                                        <?= __('Membro desde') ?>
                                    </div>
                                    <div class="info-value"><?= $user->criado_em->format('d/m/Y') ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-clock"></i>
                                        <?= __('Última atualização') ?>
                                    </div>
                                    <div class="info-value"><?= $user->atualizado_em->format('d/m/Y H:i') ?></div>
                                </div>
                                <?php if ($user->ultimo_login): ?>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <?= __('Último login') ?>
                                    </div>
                                    <div class="info-value"><?= $user->ultimo_login->format('d/m/Y H:i') ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Objetivos de Aprendizado -->
                <div class="col-lg-6 mb-4">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bullseye"></i>
                                <?= __('Objetivos de Aprendizado') ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if ($user->objetivos_aprendizado): ?>
                                <div class="objectives-content">
                                    <?= $this->Text->autoParagraph(h($user->objetivos_aprendizado)) ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list"></i>
                                    <p class="text-muted"><?= __('Nenhum objetivo de aprendizado definido.') ?></p>
                                    <?= $this->Html->link(
                                        __('Definir objetivos'),
                                        ['action' => 'edit', $user->id],
                                        ['class' => 'btn btn-outline-primary btn-sm']
                                    ) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas (placeholder para futuras implementações) -->
            <div class="row">
                <div class="col-12">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line"></i>
                                <?= __('Estatísticas de Aprendizado') ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Flashcards</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Quizzes</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">0%</div>
                                    <div class="stat-label">Progresso</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Dias Ativos</div>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <small class="text-muted"><?= __('Estatísticas em desenvolvimento') ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-profile-container {
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
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.avatar-placeholder i {
    font-size: 3rem;
    color: white;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: white;
}

.profile-email {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0 0 1rem 0;
}

.profile-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-level {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.badge-status {
    background: #10b981;
    border: 1px solid #059669;
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
}

.btn-edit {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-edit:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
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
    transform: translateY(-5px);
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

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.info-item {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    gap: 1rem;
}

.info-label {
    font-weight: 600;
    color: #6b7280;
    min-width: 150px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-value {
    color: #374151;
    font-weight: 500;
    flex: 1;
}

.objectives-content {
    line-height: 1.6;
    color: #4b5563;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    text-align: center;
}

.stat-item {
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #7c3aed;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 500;
}

.text-muted {
    color: #6b7280 !important;
}

.btn-outline-primary {
    border: 2px solid #7c3aed;
    color: #7c3aed;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #7c3aed;
    color: white;
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
        flex-wrap: wrap;
    }

    .profile-name {
        font-size: 2rem;
    }

    .avatar-placeholder {
        width: 100px;
        height: 100px;
    }

    .avatar-placeholder i {
        font-size: 2.5rem;
    }

    .info-item {
        flex-direction: column;
        gap: 0.5rem;
        text-align: left;
    }

    .info-label {
        min-width: auto;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .profile-header {
        padding: 1.5rem 0;
    }

    .profile-name {
        font-size: 1.75rem;P
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}
</style>