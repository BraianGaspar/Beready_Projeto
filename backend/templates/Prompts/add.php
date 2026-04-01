<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Prompt $prompt
 * @var \Cake\Collection\CollectionInterface|string[] $usuarios
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Prompts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="prompts form content">
            <?= $this->Form->create($prompt) ?>
            <fieldset>
                <legend><?= __('Add Prompt') ?></legend>
                <?php
                    echo $this->Form->control('usuario_id', ['options' => $usuarios]);
                    echo $this->Form->control('texto_original');
                    echo $this->Form->control('idioma_original');
                    echo $this->Form->control('contexto');
                    echo $this->Form->control('midia_origem_id');
                    echo $this->Form->control('sessao_id');
                    echo $this->Form->control('criado_em');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
