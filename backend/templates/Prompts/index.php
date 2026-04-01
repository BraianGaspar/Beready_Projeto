<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Prompt> $prompts
 */
?>
<div class="prompts index content">
    <?= $this->Html->link(__('New Prompt'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Prompts') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('usuario_id') ?></th>
                    <th><?= $this->Paginator->sort('idioma_original') ?></th>
                    <th><?= $this->Paginator->sort('contexto') ?></th>
                    <th><?= $this->Paginator->sort('midia_origem_id') ?></th>
                    <th><?= $this->Paginator->sort('sessao_id') ?></th>
                    <th><?= $this->Paginator->sort('criado_em') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prompts as $prompt): ?>
                <tr>
                    <td><?= $this->Number->format($prompt->id) ?></td>
                    <td><?= $prompt->hasValue('usuario') ? $this->Html->link($prompt->usuario->nome, ['controller' => 'Usuarios', 'action' => 'view', $prompt->usuario->id]) : '' ?></td>
                    <td><?= h($prompt->idioma_original) ?></td>
                    <td><?= h($prompt->contexto) ?></td>
                    <td><?= $prompt->midia_origem_id === null ? '' : $this->Number->format($prompt->midia_origem_id) ?></td>
                    <td><?= h($prompt->sessao_id) ?></td>
                    <td><?= h($prompt->criado_em) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $prompt->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $prompt->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $prompt->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $prompt->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>