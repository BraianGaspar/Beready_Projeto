<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Quize> $quizes
 */
?>
<div class="quizes index content">
    <?= $this->Html->link(__('New Quize'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Quizes') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('usuario_id') ?></th>
                    <th><?= $this->Paginator->sort('titulo') ?></th>
                    <th><?= $this->Paginator->sort('tipo_criacao') ?></th>
                    <th><?= $this->Paginator->sort('nivel_dificuldade') ?></th>
                    <th><?= $this->Paginator->sort('total_questoes') ?></th>
                    <th><?= $this->Paginator->sort('tempo_limite') ?></th>
                    <th><?= $this->Paginator->sort('publico') ?></th>
                    <th><?= $this->Paginator->sort('criado_em') ?></th>
                    <th><?= $this->Paginator->sort('atualizado_em') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizes as $quize): ?>
                <tr>
                    <td><?= $this->Number->format($quize->id) ?></td>
                    <td><?= $quize->hasValue('usuario') ? $this->Html->link($quize->usuario->nome, ['controller' => 'Usuarios', 'action' => 'view', $quize->usuario->id]) : '' ?></td>
                    <td><?= h($quize->titulo) ?></td>
                    <td><?= h($quize->tipo_criacao) ?></td>
                    <td><?= h($quize->nivel_dificuldade) ?></td>
                    <td><?= $quize->total_questoes === null ? '' : $this->Number->format($quize->total_questoes) ?></td>
                    <td><?= $quize->tempo_limite === null ? '' : $this->Number->format($quize->tempo_limite) ?></td>
                    <td><?= h($quize->publico) ?></td>
                    <td><?= h($quize->criado_em) ?></td>
                    <td><?= h($quize->atualizado_em) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $quize->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $quize->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $quize->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $quize->id),
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