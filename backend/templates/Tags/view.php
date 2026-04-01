<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tag $tag
 */
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="welcome-section">
                <h1 class="welcome-title"><?= h($tag->nome) ?></h1>
                <p class="welcome-subtitle">Detalhes da tag</p>
            </div>
            <div class="user-actions">
                <?= $this->Html->link(
                    '<i class="fas fa-edit"></i> Editar',
                    ['action' => 'edit', $tag->id],
                    ['class' => 'btn btn-outline', 'escape' => false]
                ) ?>
                <?= $this->Form->postLink(
                    '<i class="fas fa-trash"></i> Excluir',
                    ['action' => 'delete', $tag->id],
                    [
                        'class' => 'btn btn-logout',
                        'escape' => false,
                        'confirm' => __('Tem certeza que deseja excluir a tag "{0}"?', $tag->nome)
                    ]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-arrow-left"></i> Voltar',
                    ['action' => 'index'],
                    ['class' => 'btn btn-secondary', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-info-circle"></i>
                Informações da Tag
            </h2>
        </div>

        <div class="card-body-details">
            <div class="detail-grid">
                <div class="detail-item">
                    <label class="detail-label">ID</label>
                    <div class="detail-value">#<?= h($tag->id) ?></div>
                </div>

                <div class="detail-item">
                    <label class="detail-label">Nome</label>
                    <div class="detail-value"><?= h($tag->nome) ?></div>
                </div>

                <div class="detail-item">
                    <label class="detail-label">Cor</label>
                    <div class="detail-value">
                        <?php if ($tag->cor): ?>
                            <div class="color-display">
                                <div class="color-box-large" style="background-color: <?= $tag->cor ?>"></div>
                                <span class="color-code"><?= h($tag->cor) ?></span>
                            </div>
                        <?php else: ?>
                            <span class="no-color">Não definida</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-item">
                    <label class="detail-label">Tipo</label>
                    <div class="detail-value">
                        <?php if ($tag->tag_sistema): ?>
                            <span class="badge badge-system">Tag do Sistema</span>
                        <?php else: ?>
                            <span class="badge badge-custom">Tag Personalizada</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-item">
                    <label class="detail-label">Criado em</label>
                    <div class="detail-value"><?= $tag->criado_em->format('d/m/Y \à\s H:i') ?></div>
                </div>

                <?php if ($tag->descricao): ?>
                <div class="detail-item full-width">
                    <label class="detail-label">Descrição</label>
                    <div class="detail-value description-text">
                        <?= $this->Text->autoParagraph(h($tag->descricao)) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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

.color-display {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-box-large {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.description-text {
    line-height: 1.6;
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #7c3aed;
}

.badge {
    padding: 6px 12px;
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

.total-count {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
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
    
    .modern-table {
        font-size: 0.875rem;
    }
}
</style>