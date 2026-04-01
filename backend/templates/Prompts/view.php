<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Prompt $prompt
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Prompt'), ['action' => 'edit', $prompt->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Prompt'), ['action' => 'delete', $prompt->id], ['confirm' => __('Are you sure you want to delete # {0}?', $prompt->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Prompts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Prompt'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="prompts view content">
            <h3><?= h($prompt->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Usuario') ?></th>
                    <td><?= $prompt->hasValue('usuario') ? $this->Html->link($prompt->usuario->nome, ['controller' => 'Usuarios', 'action' => 'view', $prompt->usuario->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Idioma Original') ?></th>
                    <td><?= h($prompt->idioma_original) ?></td>
                </tr>
                <tr>
                    <th><?= __('Contexto') ?></th>
                    <td><?= h($prompt->contexto) ?></td>
                </tr>
                <tr>
                    <th><?= __('Sessao Id') ?></th>
                    <td><?= h($prompt->sessao_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($prompt->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Midia Origem Id') ?></th>
                    <td><?= $prompt->midia_origem_id === null ? '' : $this->Number->format($prompt->midia_origem_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Criado Em') ?></th>
                    <td><?= h($prompt->criado_em) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Texto Original') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($prompt->texto_original)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Flashcards') ?></h4>
                <?php if (!empty($prompt->flashcards)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Usuario Id') ?></th>
                            <th><?= __('Prompt Id') ?></th>
                            <th><?= __('Texto Frente') ?></th>
                            <th><?= __('Texto Verso') ?></th>
                            <th><?= __('Imagem Frente Id') ?></th>
                            <th><?= __('Imagem Verso Id') ?></th>
                            <th><?= __('Audio Frente Url') ?></th>
                            <th><?= __('Audio Verso Url') ?></th>
                            <th><?= __('Nivel Dificuldade') ?></th>
                            <th><?= __('Tipo Criacao') ?></th>
                            <th><?= __('Vezes Revisado') ?></th>
                            <th><?= __('Vezes Acertado') ?></th>
                            <th><?= __('Ultima Revisao') ?></th>
                            <th><?= __('Proxima Revisao') ?></th>
                            <th><?= __('Arquivado') ?></th>
                            <th><?= __('Criado Em') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($prompt->flashcards as $flashcard) : ?>
                        <tr>
                            <td><?= h($flashcard->id) ?></td>
                            <td><?= h($flashcard->usuario_id) ?></td>
                            <td><?= h($flashcard->prompt_id) ?></td>
                            <td><?= h($flashcard->texto_frente) ?></td>
                            <td><?= h($flashcard->texto_verso) ?></td>
                            <td><?= h($flashcard->imagem_frente_id) ?></td>
                            <td><?= h($flashcard->imagem_verso_id) ?></td>
                            <td><?= h($flashcard->audio_frente_url) ?></td>
                            <td><?= h($flashcard->audio_verso_url) ?></td>
                            <td><?= h($flashcard->nivel_dificuldade) ?></td>
                            <td><?= h($flashcard->tipo_criacao) ?></td>
                            <td><?= h($flashcard->vezes_revisado) ?></td>
                            <td><?= h($flashcard->vezes_acertado) ?></td>
                            <td><?= h($flashcard->ultima_revisao) ?></td>
                            <td><?= h($flashcard->proxima_revisao) ?></td>
                            <td><?= h($flashcard->arquivado) ?></td>
                            <td><?= h($flashcard->criado_em) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Flashcards', 'action' => 'view', $flashcard->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Flashcards', 'action' => 'edit', $flashcard->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Flashcards', 'action' => 'delete', $flashcard->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $flashcard->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Frases Semelhantes') ?></h4>
                <?php if (!empty($prompt->frases_semelhantes)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Prompt Id') ?></th>
                            <th><?= __('Frase Semelhante') ?></th>
                            <th><?= __('Pontuacao Semelhante') ?></th>
                            <th><?= __('Tipo Frase') ?></th>
                            <th><?= __('Nivel Dificuldade') ?></th>
                            <th><?= __('Criado Em') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($prompt->frases_semelhantes as $frasesSemelhante) : ?>
                        <tr>
                            <td><?= h($frasesSemelhante->id) ?></td>
                            <td><?= h($frasesSemelhante->prompt_id) ?></td>
                            <td><?= h($frasesSemelhante->frase_semelhante) ?></td>
                            <td><?= h($frasesSemelhante->pontuacao_semelhante) ?></td>
                            <td><?= h($frasesSemelhante->tipo_frase) ?></td>
                            <td><?= h($frasesSemelhante->nivel_dificuldade) ?></td>
                            <td><?= h($frasesSemelhante->criado_em) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'FrasesSemelhantes', 'action' => 'view', $frasesSemelhante->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'FrasesSemelhantes', 'action' => 'edit', $frasesSemelhante->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'FrasesSemelhantes', 'action' => 'delete', $frasesSemelhante->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $frasesSemelhante->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Imagens Geradas') ?></h4>
                <?php if (!empty($prompt->imagens_geradas)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Prompt Id') ?></th>
                            <th><?= __('Traducao Id') ?></th>
                            <th><?= __('Url Imagem') ?></th>
                            <th><?= __('Prompt Imagem') ?></th>
                            <th><?= __('Servico Geracao') ?></th>
                            <th><?= __('Qualidade Imagem') ?></th>
                            <th><?= __('Tamanho Arquivo') ?></th>
                            <th><?= __('Dimensoes') ?></th>
                            <th><?= __('Criado Em') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($prompt->imagens_geradas as $imagensGerada) : ?>
                        <tr>
                            <td><?= h($imagensGerada->id) ?></td>
                            <td><?= h($imagensGerada->prompt_id) ?></td>
                            <td><?= h($imagensGerada->traducao_id) ?></td>
                            <td><?= h($imagensGerada->url_imagem) ?></td>
                            <td><?= h($imagensGerada->prompt_imagem) ?></td>
                            <td><?= h($imagensGerada->servico_geracao) ?></td>
                            <td><?= h($imagensGerada->qualidade_imagem) ?></td>
                            <td><?= h($imagensGerada->tamanho_arquivo) ?></td>
                            <td><?= h($imagensGerada->dimensoes) ?></td>
                            <td><?= h($imagensGerada->criado_em) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'ImagensGeradas', 'action' => 'view', $imagensGerada->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'ImagensGeradas', 'action' => 'edit', $imagensGerada->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'ImagensGeradas', 'action' => 'delete', $imagensGerada->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $imagensGerada->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Traducoes') ?></h4>
                <?php if (!empty($prompt->traducoes)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Prompt Id') ?></th>
                            <th><?= __('Texto Traduzido') ?></th>
                            <th><?= __('Idioma Destino') ?></th>
                            <th><?= __('Pontuacao Confianca') ?></th>
                            <th><?= __('Servico Traducao') ?></th>
                            <th><?= __('Traducoes Alternativas') ?></th>
                            <th><?= __('Criado Em') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($prompt->traducoes as $traduco) : ?>
                        <tr>
                            <td><?= h($traduco->id) ?></td>
                            <td><?= h($traduco->prompt_id) ?></td>
                            <td><?= h($traduco->texto_traduzido) ?></td>
                            <td><?= h($traduco->idioma_destino) ?></td>
                            <td><?= h($traduco->pontuacao_confianca) ?></td>
                            <td><?= h($traduco->servico_traducao) ?></td>
                            <td><?= h($traduco->traducoes_alternativas) ?></td>
                            <td><?= h($traduco->criado_em) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Traducoes', 'action' => 'view', $traduco->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Traducoes', 'action' => 'edit', $traduco->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Traducoes', 'action' => 'delete', $traduco->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $traduco->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>