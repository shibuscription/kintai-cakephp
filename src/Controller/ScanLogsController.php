<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * ScanLogs Controller
 *
 * @property \App\Model\Table\ScanLogsTable $ScanLogs
 * @method \App\Model\Entity\ScanLog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ScanLogsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('ScanLogs');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Devices'],
        ];
        $scanLogs = $this->paginate($this->ScanLogs);

        $this->set(compact('scanLogs'));
    }

    /**
     * View method
     *
     * @param string|null $id Scan Log id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $scanLog = $this->ScanLogs->get($id, [
            'contain' => ['Devices'],
        ]);

        $this->set(compact('scanLog'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $scanLog = $this->ScanLogs->newEmptyEntity();
        if ($this->request->is('post')) {
            $scanLog = $this->ScanLogs->patchEntity($scanLog, $this->request->getData());
            if ($this->ScanLogs->save($scanLog)) {
                $this->Flash->success(__('The scan log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The scan log could not be saved. Please, try again.'));
        }
        $devices = $this->ScanLogs->Devices->find('list', ['limit' => 200])->all();
        $this->set(compact('scanLog', 'devices'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Scan Log id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $scanLog = $this->ScanLogs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $scanLog = $this->ScanLogs->patchEntity($scanLog, $this->request->getData());
            if ($this->ScanLogs->save($scanLog)) {
                $this->Flash->success(__('The scan log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The scan log could not be saved. Please, try again.'));
        }
        $devices = $this->ScanLogs->Devices->find('list', ['limit' => 200])->all();
        $this->set(compact('scanLog', 'devices'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Scan Log id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $scanLog = $this->ScanLogs->get($id);
        if ($this->ScanLogs->delete($scanLog)) {
            $this->Flash->success(__('The scan log has been deleted.'));
        } else {
            $this->Flash->error(__('The scan log could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
