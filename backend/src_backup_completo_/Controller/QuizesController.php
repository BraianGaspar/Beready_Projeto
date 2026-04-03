<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Quizes Controller
 *
 */
class QuizesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Quizes->find();
        $quizes = $this->paginate($query);

        $this->set(compact('quizes'));
    }

    /**
     * View method
     *
     * @param string|null $id Quize id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $quize = $this->Quizes->get($id, contain: []);
        $this->set(compact('quize'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $quize = $this->Quizes->newEmptyEntity();
        if ($this->request->is('post')) {
            $quize = $this->Quizes->patchEntity($quize, $this->request->getData());
            if ($this->Quizes->save($quize)) {
                $this->Flash->success(__('The quize has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quize could not be saved. Please, try again.'));
        }
        $this->set(compact('quize'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Quize id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $quize = $this->Quizes->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $quize = $this->Quizes->patchEntity($quize, $this->request->getData());
            if ($this->Quizes->save($quize)) {
                $this->Flash->success(__('The quize has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quize could not be saved. Please, try again.'));
        }
        $this->set(compact('quize'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Quize id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $quize = $this->Quizes->get($id);
        if ($this->Quizes->delete($quize)) {
            $this->Flash->success(__('The quize has been deleted.'));
        } else {
            $this->Flash->error(__('The quize could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
