<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Traducoes Controller
 *
 */
class TraducoesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Traducoes->find();
        $traducoes = $this->paginate($query);

        $this->set(compact('traducoes'));
    }

    /**
     * View method
     *
     * @param string|null $id Traduco id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $traduco = $this->Traducoes->get($id, contain: []);
        $this->set(compact('traduco'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traduco = $this->Traducoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $traduco = $this->Traducoes->patchEntity($traduco, $this->request->getData());
            if ($this->Traducoes->save($traduco)) {
                $this->Flash->success(__('The traduco has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The traduco could not be saved. Please, try again.'));
        }
        $this->set(compact('traduco'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Traduco id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traduco = $this->Traducoes->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $traduco = $this->Traducoes->patchEntity($traduco, $this->request->getData());
            if ($this->Traducoes->save($traduco)) {
                $this->Flash->success(__('The traduco has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The traduco could not be saved. Please, try again.'));
        }
        $this->set(compact('traduco'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Traduco id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traduco = $this->Traducoes->get($id);
        if ($this->Traducoes->delete($traduco)) {
            $this->Flash->success(__('The traduco has been deleted.'));
        } else {
            $this->Flash->error(__('The traduco could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
