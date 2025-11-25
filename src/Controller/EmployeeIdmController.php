<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * EmployeeIdm Controller
 *
 * @property \App\Model\Table\EmployeeIdmTable $EmployeeIdm
 * @method \App\Model\Entity\EmployeeIdm[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EmployeeIdmController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Employees'],
        ];
        $employeeIdm = $this->paginate($this->EmployeeIdm);

        $this->set(compact('employeeIdm'));
    }

    /**
     * View method
     *
     * @param string|null $id Employee Idm id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $employeeIdm = $this->EmployeeIdm->get($id, [
            'contain' => ['Employees'],
        ]);

        $this->set(compact('employeeIdm'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $employeeIdm = $this->EmployeeIdm->newEmptyEntity();
        if ($this->request->is('post')) {
            $employeeIdm = $this->EmployeeIdm->patchEntity($employeeIdm, $this->request->getData());
            if ($this->EmployeeIdm->save($employeeIdm)) {
                $this->Flash->success(__('The employee idm has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The employee idm could not be saved. Please, try again.'));
        }
        $employees = $this->EmployeeIdm->Employees->find('list', ['limit' => 200])->all();
        $this->set(compact('employeeIdm', 'employees'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Employee Idm id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $employeeIdm = $this->EmployeeIdm->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $employeeIdm = $this->EmployeeIdm->patchEntity($employeeIdm, $this->request->getData());
            if ($this->EmployeeIdm->save($employeeIdm)) {
                $this->Flash->success(__('The employee idm has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The employee idm could not be saved. Please, try again.'));
        }
        $employees = $this->EmployeeIdm->Employees->find('list', ['limit' => 200])->all();
        $this->set(compact('employeeIdm', 'employees'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Employee Idm id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $employeeIdm = $this->EmployeeIdm->get($id);
        if ($this->EmployeeIdm->delete($employeeIdm)) {
            $this->Flash->success(__('The employee idm has been deleted.'));
        } else {
            $this->Flash->error(__('The employee idm could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
