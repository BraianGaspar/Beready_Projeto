<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Traduco> $traducoes
 */
?>
<div class="traducoes index content">
    <?= $this->Html->link(__('New Traduco'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Traducoes') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('prompt_id') ?></th>
                    <th><?= $this->Paginator->sort('idioma_destino') ?></th>
                    <th><?= $this->Paginator->sort('pontuacao_confianca') ?></th>
                    <th><?= $this->Paginator->sort('servico_traducao') ?></th>
                    <th><?= $this->Paginator->sort('traducoes_alternativas') ?></th>
                    <th><?= $this->Paginator->sort('criado_em') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($traducoes as $traduco): ?>
                <tr>
                    <td><?= $this->Number->format($traduco->id) ?></td>
                    <td><?= $traduco->hasValue('prompt') ? $this->Html->link($traduco->prompt->id, ['controller' => 'Prompts', 'action' => 'view', $traduco->prompt->id]) : '' ?></td>
                    <td><?= h($traduco->idioma_destino) ?></td>
                    <td><?= $traduco->pontuacao_confianca === null ? '' : $this->Number->format($traduco->pontuacao_confianca) ?></td>
                    <td><?= h($traduco->servico_traducao) ?></td>
                    <td><?= h($traduco->traducoes_alternativas) ?></td>
                    <td><?= h($traduco->criado_em) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $traduco->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $traduco->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $traduco->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $traduco->id),
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