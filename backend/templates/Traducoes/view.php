<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Traduco $traduco
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Traduco'), ['action' => 'edit', $traduco->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Traduco'), ['action' => 'delete', $traduco->id], ['confirm' => __('Are you sure you want to delete # {0}?', $traduco->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Traducoes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Traduco'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="traducoes view content">
            <h3><?= h($traduco->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Prompt') ?></th>
                    <td><?= $traduco->hasValue('prompt') ? $this->Html->link($traduco->prompt->id, ['controller' => 'Prompts', 'action' => 'view', $traduco->prompt->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Idioma Destino') ?></th>
                    <td><?= h($traduco->idioma_destino) ?></td>
                </tr>
                <tr>
                    <th><?= __('Servico Traducao') ?></th>
                    <td><?= h($traduco->servico_traducao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Traducoes Alternativas') ?></th>
                    <td><?= h($traduco->traducoes_alternativas) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($traduco->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Pontuacao Confianca') ?></th>
                    <td><?= $traduco->pontuacao_confianca === null ? '' : $this->Number->format($traduco->pontuacao_confianca) ?></td>
                </tr>
                <tr>
                    <th><?= __('Criado Em') ?></th>
                    <td><?= h($traduco->criado_em) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Texto Traduzido') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($traduco->texto_traduzido)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>