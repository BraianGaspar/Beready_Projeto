<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Quize $quize
 * @var \Cake\Collection\CollectionInterface|string[] $usuarios
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Quizes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="quizes form content">
            <?= $this->Form->create($quize) ?>
            <fieldset>
                <legend><?= __('Add Quize') ?></legend>
                <?php
                    echo $this->Form->control('usuario_id', ['options' => $usuarios]);
                    echo $this->Form->control('titulo');
                    echo $this->Form->control('descricao');
                    echo $this->Form->control('tipo_criacao');
                    echo $this->Form->control('nivel_dificuldade');
                    echo $this->Form->control('total_questoes');
                    echo $this->Form->control('tempo_limite');
                    echo $this->Form->control('publico');
                    echo $this->Form->control('criado_em');
                    echo $this->Form->control('atualizado_em');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
