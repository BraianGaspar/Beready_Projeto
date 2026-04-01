<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User> $users
 */

// Configurar o fuso horário do Brasil
$timezone = new DateTimeZone('America/Sao_Paulo');
?>
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="welcome-section">
                <h1 class="welcome-title">Bem-vindo ao Beready!</h1>
                <p class="welcome-subtitle">Sua plataforma de aprendizado de idiomas</p>
            </div>
            <div class="user-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-user-edit"></i> Editar Perfil',
                    ['action' => 'edit', $this->request->getAttribute('identity') ? $this->request->getAttribute('identity')->id : null],
                    ['class' => 'btn btn-outline', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> Novo Usuário',
                    ['action' => 'add'],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-sign-out-alt"></i> Sair',
                    ['action' => 'logout'],
                    ['class' => 'btn btn-logout', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="flash-messages-container">
        <?= $this->Flash->render() ?>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= count($users) ?></h3>
                <p class="stat-label">Total de Usuários</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">
                    <?= count(array_filter($users->toArray(), function($user) { return $user->status === 'ativo'; })) ?>
                </h3>
                <p class="stat-label">Usuários Ativos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">
                    <?= count(array_filter($users->toArray(), function($user) { return $user->nivel_ingles === 'avancado'; })) ?>
                </h3>
                <p class="stat-label">Avançados</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">
                    <?= count(array_filter($users->toArray(), function($user) { return $user->ultimo_login; })) ?>
                </h3>
                <p class="stat-label">Com Login Recente</p>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-list"></i>
                Lista de Usuários
            </h2>
            <div class="card-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-download"></i> Exportar',
                    '#',
                    ['class' => 'btn btn-outline', 'escape' => false]
                ) ?>
            </div>
        </div>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('nome', 'Nome') ?></th>
                        <th><?= $this->Paginator->sort('email', 'E-mail') ?></th>
                        <th><?= $this->Paginator->sort('nivel_ingles', 'Nível Inglês') ?></th>
                        <th><?= $this->Paginator->sort('status', 'Status') ?></th>
                        <th><?= $this->Paginator->sort('ultimo_login', 'Último Login') ?></th>
                        <th class="actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="user-details">
                                <strong><?= h($user->nome) ?></strong>
                                <?php if ($user->telefone): ?>
                                    <span class="user-phone"><?= h($user->telefone) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= h($user->email) ?></td>
                        <td>
                            <span class="badge badge-<?= $user->nivel_ingles ?>">
                                <?= h(ucfirst($user->nivel_ingles)) ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $user->status ?>">
                                <?= h(ucfirst($user->status)) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user->ultimo_login): ?>
                                <?php
                                // Converter para o fuso horário do Brasil
                                $ultimoLogin = $user->ultimo_login->setTimezone($timezone);
                                ?>
                                <span class="timestamp" title="<?= $ultimoLogin->format('d/m/Y H:i:s T') ?>">
                                    <?= $ultimoLogin->format('d/m/Y H:i') ?>
                                </span>
                            <?php else: ?>
                                <span class="timestamp never">Nunca logou</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <div class="action-buttons">
                                <?= $this->Html->link(
                                    '<i class="fas fa-eye"></i>',
                                    ['action' => 'view', $user->id],
                                    ['class' => 'btn-action view', 'title' => 'Visualizar', 'escape' => false]
                                ) ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'edit', $user->id],
                                    ['class' => 'btn-action edit', 'title' => 'Editar', 'escape' => false]
                                ) ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-trash"></i>',
                                    ['action' => 'delete', $user->id],
                                    [
                                        'class' => 'btn-action delete',
                                        'title' => 'Excluir',
                                        'escape' => false,
                                        'confirm' => __('Tem certeza que deseja excluir o usuário {0}?', $user->nome)
                                    ]
                                ) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                <?= $this->Paginator->counter('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} registros') ?>
            </div>
            <ul class="pagination">
                <?= $this->Paginator->first('<i class="fas fa-angle-double-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->prev('<i class="fas fa-angle-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next('<i class="fas fa-angle-right"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->last('<i class="fas fa-angle-double-right"></i>', ['escape' => false]) ?>
            </ul>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.dashboard-header {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.3);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: white;
}

.welcome-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
}

.user-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #1f2937;
}

.stat-label {
    margin: 0;
    color: #6b7280;
    font-weight: 500;
}

/* Content Card */
.content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-header {
    padding: 25px 30px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
    color: #1f2937;
}

.card-title i {
    color: #7c3aed;
}

/* Table */
.table-container {
    padding: 0;
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table th {
    background: #f8fafc;
    padding: 15px 20px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.modern-table td {
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
}

.modern-table tr:hover {
    background: #f9fafb;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-phone {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 4px;
}

/* Badges */
.badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-iniciante { background: #fef3c7; color: #92400e; }
.badge-intermediario { background: #dbeafe; color: #1e40af; }
.badge-avancado { background: #dcfce7; color: #166534; }

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-ativo { background: #dcfce7; color: #166534; }
.status-inativo { background: #fef3c7; color: #92400e; }

.timestamp {
    font-size: 0.875rem;
    color: #6b7280;
    cursor: help;
}

.timestamp.never {
    color: #9ca3af;
    font-style: italic;
    cursor: default;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-action.view {
    background: #dbeafe;
    color: #1d4ed8;
}

.btn-action.edit {
    background: #fef3c7;
    color: #d97706;
}

.btn-action.delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-action:hover {
    transform: translateY(-2px);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-primary {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
}

.btn-outline {
    background: white;
    color: #374151;
    border-color: #d1d5db;
}

.btn-outline:hover {
    border-color: #9ca3af;
    transform: translateY(-2px);
}

.btn-logout {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}

.btn-logout:hover {
    background: #fecaca;
    transform: translateY(-2px);
}

/* Pagination */
.pagination-container {
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #e5e7eb;
}

.pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

.pagination {
    display: flex;
    gap: 8px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.pagination li a {
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    border: 1px solid #d1d5db;
    transition: all 0.3s ease;
}

.pagination li.active a {
    background: #7c3aed;
    color: white;
    border-color: #7c3aed;
}

.pagination li a:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

/* Flash Messages */
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

.flash-messages-container .alert-error,
.flash-messages-container .alert-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 10px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .card-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .pagination-container {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .user-actions {
        justify-content: center;
    }
    
    .flash-messages-container {
        position: relative;
        top: auto;
        right: auto;
        max-width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-remove flash messages
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