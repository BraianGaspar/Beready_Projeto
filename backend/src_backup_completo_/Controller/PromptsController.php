<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Prompts Controller
 *
 */
class PromptsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Prompts->find();
        $prompts = $this->paginate($query);

        $this->set(compact('prompts'));
    }

    /**
     * View method
     *
     * @param string|null $id Prompt id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $prompt = $this->Prompts->get($id, contain: []);
        $this->set(compact('prompt'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $prompt = $this->Prompts->newEmptyEntity();
        if ($this->request->is('post')) {
            $prompt = $this->Prompts->patchEntity($prompt, $this->request->getData());
            if ($this->Prompts->save($prompt)) {
                $this->Flash->success(__('The prompt has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The prompt could not be saved. Please, try again.'));
        }
        $this->set(compact('prompt'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Prompt id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $prompt = $this->Prompts->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $prompt = $this->Prompts->patchEntity($prompt, $this->request->getData());
            if ($this->Prompts->save($prompt)) {
                $this->Flash->success(__('The prompt has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The prompt could not be saved. Please, try again.'));
        }
        $this->set(compact('prompt'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Prompt id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $prompt = $this->Prompts->get($id);
        if ($this->Prompts->delete($prompt)) {
            $this->Flash->success(__('The prompt has been deleted.'));
        } else {
            $this->Flash->error(__('The prompt could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
