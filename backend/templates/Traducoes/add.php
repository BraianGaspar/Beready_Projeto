<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Traduco $traduco
 * @var \Cake\Collection\CollectionInterface|string[] $prompts
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Traducoes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="traducoes form content">
            <?= $this->Form->create($traduco) ?>
            <fieldset>
                <legend><?= __('Add Traduco') ?></legend>
                <?php
                    echo $this->Form->control('prompt_id', ['options' => $prompts]);
                    echo $this->Form->control('texto_traduzido');
                    echo $this->Form->control('idioma_destino');
                    echo $this->Form->control('pontuacao_confianca');
                    echo $this->Form->control('servico_traducao');
                    echo $this->Form->control('traducoes_alternativas');
                    echo $this->Form->control('criado_em');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
