<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Tag> $tags
 */
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="welcome-section">
                <h1 class="welcome-title">Gerenciar Tags</h1>
                <p class="welcome-subtitle">Organize suas tags de aprendizado</p>
            </div>
            <div class="user-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> Nova Tag',
                    ['action' => 'add'],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <div class="flash-messages-container">
        <?= $this->Flash->render() ?>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-tags"></i>
                Lista de Tags
            </h2>
            <div class="card-actions">
                <span class="total-count"><?= $this->Paginator->counter('{{count}}') ?> tags</span>
            </div>
        </div>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                        <th><?= $this->Paginator->sort('nome', 'Nome') ?></th>
                        <th><?= $this->Paginator->sort('cor', 'Cor') ?></th>
                        <th><?= $this->Paginator->sort('tag_sistema', 'Sistema') ?></th>
                        <th><?= $this->Paginator->sort('criado_em', 'Criado Em') ?></th>
                        <th class="actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tags as $tag): ?>
                    <tr>
                        <td><?= $this->Number->format($tag->id) ?></td>
                        <td>
                            <div class="tag-info">
                                <?php if ($tag->cor): ?>
                                    <span class="tag-color" style="background-color: <?= $tag->cor ?>"></span>
                                <?php endif; ?>
                                <strong><?= h($tag->nome) ?></strong>
                                <?php if ($tag->descricao): ?>
                                    <p class="tag-description"><?= h($tag->descricao) ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($tag->cor): ?>
                                <div class="color-preview">
                                    <div class="color-box" style="background-color: <?= $tag->cor ?>"></div>
                                    <span class="color-code"><?= h($tag->cor) ?></span>
                                </div>
                            <?php else: ?>
                                <span class="no-color">Não definida</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tag->tag_sistema): ?>
                                <span class="badge badge-system">Sistema</span>
                            <?php else: ?>
                                <span class="badge badge-custom">Personalizada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="timestamp">
                                <?php // Subtrai 1 hora para corrigir o fuso horário
                                // Subtrai 1 hora para corrigir o fuso horário
                                $criadoEm = $tag->criado_em->modify('-1 hour');
                                echo $criadoEm->format('d/m/Y H:i');
                                ?>
                            </span>
                        </td>
                        <td class="actions">
                            <div class="action-buttons">
                                <?= $this->Html->link(
                                    '<i class="fas fa-eye"></i>',
                                    ['action' => 'view', $tag->id],
                                    ['class' => 'btn-action view', 'title' => 'Visualizar', 'escape' => false]
                                ) ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'edit', $tag->id],
                                    ['class' => 'btn-action edit', 'title' => 'Editar', 'escape' => false]
                                ) ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-trash"></i>',
                                    ['action' => 'delete', $tag->id],
                                    [
                                        'class' => 'btn-action delete',
                                        'title' => 'Excluir',
                                        'escape' => false,
                                        'confirm' => __('Tem certeza que deseja excluir a tag "{0}"?', $tag->nome)
                                    ]
                                ) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <div class="pagination-info">
                <?= $this->Paginator->counter('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} registros') ?>
            </div>
            <ul class="pagination">
                <?= $this->Paginator->first('<<') ?>
                <?= $this->Paginator->prev('<') ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next('>') ?>
                <?= $this->Paginator->last('>>') ?>
            </ul>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

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
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: white;
}

.welcome-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.user-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
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

.content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 30px;
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
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    color: #1f2937;
}

.card-title i {
    color: #7c3aed;
}

.total-count {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

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

.tag-info {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.tag-color {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    flex-shrink: 0;
    margin-top: 2px;
}

.tag-description {
    margin: 4px 0 0 0;
    font-size: 0.875rem;
    color: #6b7280;
    font-style: italic;
}

.color-preview {
    display: flex;
    align-items: center;
    gap: 8px;
}

.color-box {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    border: 1px solid #e5e7eb;
}

.color-code {
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    color: #6b7280;
}

.badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-system {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-custom {
    background: #f3e8ff;
    color: #7c3aed;
}

.timestamp {
    font-size: 0.875rem;
    color: #6b7280;
}

.actions {
    white-space: nowrap;
}

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

.btn-secondary {
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
}

.btn-secondary:hover {
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

@media (max-width: 768px) {
    .dashboard-container {
        padding: 10px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-title {
        font-size: 1.8rem;
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
    
    .modern-table {
        font-size: 0.875rem;
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