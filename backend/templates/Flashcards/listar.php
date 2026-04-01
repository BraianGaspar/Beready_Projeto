<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Flashcard> $flashcards
 */
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="welcome-section">
                <h1 class="welcome-title">Flashcards</h1>
                <p class="welcome-subtitle">Gerencie seus cartões de aprendizado</p>
            </div>
            <div class="user-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> Novo Flashcard',
                    ['action' => 'criar'],
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
                <i class="fas fa-list"></i>
                Lista de Flashcards
            </h2>
            <div class="card-actions">
                <span class="total-count"><?= $this->Paginator->counter('{{count}}') ?> flashcards</span>
            </div>
        </div>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                        <th><?= $this->Paginator->sort('question', 'Pergunta') ?></th>
                        <th><?= $this->Paginator->sort('answer', 'Resposta') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Criado em') ?></th>
                        <th class="actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flashcards as $flashcard): ?>
                    <tr>
                        <td><?= $this->Number->format($flashcard->id) ?></td>
                        <td><?= h($flashcard->question) ?></td>
                        <td><?= h($flashcard->answer) ?></td>
                        <td>
                            <?php
                            // Adiciona 3 horas para corrigir o fuso horário
                            $created = $flashcard->created->modify('-1 hours');
                            echo h($created->format('d/m/Y H:i'));
                            ?>
                        </td>
                        <td class="actions">
                            <div class="action-buttons">
                                <?= $this->Html->link(
                                    '<i class="fas fa-eye"></i>',
                                    ['action' => 'ver', $flashcard->id],
                                    ['class' => 'btn-action view', 'title' => 'Visualizar', 'escape' => false]
                                ) ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'editar', $flashcard->id],
                                    ['class' => 'btn-action edit', 'title' => 'Editar', 'escape' => false]
                                ) ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-trash"></i>',
                                    ['action' => 'excluir', $flashcard->id],
                                    [
                                        'class' => 'btn-action delete',
                                        'title' => 'Excluir',
                                        'escape' => false,
                                        'confirm' => __('Tem certeza que deseja excluir o flashcard "{0}"?', $flashcard->question)
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
    
    .action-buttons {
        flex-wrap: wrap;
        justify-content: center;
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