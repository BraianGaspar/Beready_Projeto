<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Flashcards Controller
 * 
 * @property \App\Model\Table\FlashcardsTable $Flashcards
 */
class FlashcardsController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Permitir acesso sem autenticação às ações públicas (ajuste conforme necessário)
        $this->Auth->allow(['listar', 'ver']);
    }

    /**
     * Listar method - Lista todos os flashcards
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function listar()
    {
        // Busca flashcards do usuário logado
        $userId = $this->Auth->user('id');
        
        $query = $this->Flashcards->find()
            ->where(['Flashcards.user_id' => $userId])
            ->order(['Flashcards.created' => 'DESC']);
        
        $flashcards = $this->paginate($query);
        
        $this->set(compact('flashcards'));
    }

    /**
     * Ver method - Visualiza um flashcard específico
     *
     * @param string|null $id Flashcard id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function ver($id = null)
    {
        $userId = $this->Auth->user('id');
        
        $flashcard = $this->Flashcards->get($id, [
            'conditions' => ['Flashcards.user_id' => $userId],
            'contain' => ['Tags']
        ]);
        
        $this->set(compact('flashcard'));
    }

    /**
     * Criar method - Cria um novo flashcard
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function criar()
    {
        $flashcard = $this->Flashcards->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Adiciona o user_id automaticamente
            $data['user_id'] = $this->Auth->user('id');
            
            $flashcard = $this->Flashcards->patchEntity($flashcard, $data);
            
            if ($this->Flashcards->save($flashcard)) {
                $this->Flash->success(__('Flashcard criado com sucesso!'));
                return $this->redirect(['action' => 'listar']);
            }
            
            $this->Flash->error(__('Não foi possível criar o flashcard. Tente novamente.'));
        }
        
        // Busca tags para seleção (se houver relacionamento)
        $tags = $this->Flashcards->Tags->find('list', ['limit' => 200])->all();
        
        $this->set(compact('flashcard', 'tags'));
    }

    /**
     * Editar method - Edita um flashcard existente
     *
     * @param string|null $id Flashcard id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editar($id = null)
    {
        $userId = $this->Auth->user('id');
        
        $flashcard = $this->Flashcards->get($id, [
            'conditions' => ['Flashcards.user_id' => $userId],
            'contain' => []
        ]);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $flashcard = $this->Flashcards->patchEntity($flashcard, $this->request->getData());
            
            if ($this->Flashcards->save($flashcard)) {
                $this->Flash->success(__('Flashcard atualizado com sucesso!'));
                return $this->redirect(['action' => 'listar']);
            }
            
            $this->Flash->error(__('Não foi possível atualizar o flashcard. Tente novamente.'));
        }
        
        // Busca tags para seleção (se houver relacionamento)
        $tags = $this->Flashcards->Tags->find('list', ['limit' => 200])->all();
        
        $this->set(compact('flashcard', 'tags'));
    }

    /**
     * Excluir method - Deleta um flashcard
     *
     * @param string|null $id Flashcard id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function excluir($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $userId = $this->Auth->user('id');
        
        $flashcard = $this->Flashcards->get($id, [
            'conditions' => ['Flashcards.user_id' => $userId]
        ]);
        
        if ($this->Flashcards->delete($flashcard)) {
            $this->Flash->success(__('Flashcard excluído com sucesso!'));
        } else {
            $this->Flash->error(__('Não foi possível excluir o flashcard. Tente novamente.'));
        }
        
        return $this->redirect(['action' => 'listar']);
    }
}