<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Flashcard $flashcard
 */
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="welcome-section">
                <h1 class="welcome-title"><?= h($flashcard->question) ?></h1>
                <p class="welcome-subtitle">Detalhes do flashcard</p>
            </div>
            <div class="user-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-edit"></i> Editar',
                    ['action' => 'editar', $flashcard->id],
                    ['class' => 'btn btn-outline', 'escape' => false]
                ) ?>
                <?= $this->Form->postLink(
                    '<i class="fas fa-trash"></i> Excluir',
                    ['action' => 'excluir', $flashcard->id],
                    [
                        'class' => 'btn btn-logout',
                        'escape' => false,
                        'confirm' => __('Tem certeza que deseja excluir o flashcard "{0}"?', $flashcard->question)
                    ]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-arrow-left"></i> Voltar',
                    ['action' => 'listar'],
                    ['class' => 'btn btn-secondary', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-info-circle"></i>
                Informações do Flashcard
            </h2>
        </div>

        <div class="card-body-details">
            <div class="detail-grid">
                <div class="detail-item">
                    <label class="detail-label">ID</label>
                    <div class="detail-value">#<?= h($flashcard->id) ?></div>
                </div>

                <div class="detail-item full-width">
                    <label class="detail-label">Pergunta</label>
                    <div class="detail-value question-text">
                        <?= h($flashcard->question) ?>
                    </div>
                </div>

                <div class="detail-item full-width">
                    <label class="detail-label">Resposta</label>
                    <div class="detail-value answer-text">
                        <?= h($flashcard->answer) ?>
                    </div>
                </div>

                <div class="detail-item">
                    <label class="detail-label">Criado em</label>
                    <div class="detail-value"><?= $flashcard->created->format('d/m/Y \à\s H:i') ?></div>
                </div>

                <div class="detail-item">
                    <label class="detail-label">Modificado em</label>
                    <div class="detail-value"><?= $flashcard->modified->format('d/m/Y \à\s H:i') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.question-text, .answer-text {
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #7c3aed;
    font-size: 1.1rem;
    line-height: 1.6;
}

.answer-text {
    border-left-color: #10b981;
    background: #f0fdf4;
}
</style>
<style>
.dashboard-container {
    padding: 20px;
    max-width: 1000px;
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
    word-break: break-word;
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

.card-body-details {
    padding: 30px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-item.full-width {
    grid-column: 1 / -1;
}

.detail-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.detail-value {
    font-size: 1rem;
    color: #1f2937;
}

.question-text, .answer-text {
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #7c3aed;
    font-size: 1.1rem;
    line-height: 1.6;
    word-break: break-word;
}

.answer-text {
    border-left-color: #10b981;
    background: #f0fdf4;
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
    
    .user-actions {
        justify-content: center;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .question-text, .answer-text {
        font-size: 1rem;
        padding: 15px;
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