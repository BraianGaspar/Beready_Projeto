<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Quize $quize
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Quize'), ['action' => 'edit', $quize->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Quize'), ['action' => 'delete', $quize->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quize->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Quizes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Quize'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="quizes view content">
            <h3><?= h($quize->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Usuario') ?></th>
                    <td><?= $quize->hasValue('usuario') ? $this->Html->link($quize->usuario->nome, ['controller' => 'Usuarios', 'action' => 'view', $quize->usuario->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Titulo') ?></th>
                    <td><?= h($quize->titulo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tipo Criacao') ?></th>
                    <td><?= h($quize->tipo_criacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nivel Dificuldade') ?></th>
                    <td><?= h($quize->nivel_dificuldade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($quize->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Total Questoes') ?></th>
                    <td><?= $quize->total_questoes === null ? '' : $this->Number->format($quize->total_questoes) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tempo Limite') ?></th>
                    <td><?= $quize->tempo_limite === null ? '' : $this->Number->format($quize->tempo_limite) ?></td>
                </tr>
                <tr>
                    <th><?= __('Criado Em') ?></th>
                    <td><?= h($quize->criado_em) ?></td>
                </tr>
                <tr>
                    <th><?= __('Atualizado Em') ?></th>
                    <td><?= h($quize->atualizado_em) ?></td>
                </tr>
                <tr>
                    <th><?= __('Publico') ?></th>
                    <td><?= $quize->publico ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Descricao') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($quize->descricao)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>