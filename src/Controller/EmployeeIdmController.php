<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;

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

    public function beforeFilter($event)
    {
        parent::beforeFilter($event);

        // Kiosk からの未認証導線を許可（仕様に合わせて公開）
        $this->Authentication->allowUnauthenticated(['register']);
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



    public function register()
    {
        $this->viewBuilder()->setLayout('kiosk');

        if (!Configure::read('debug')) {
            // 本番でどうするかは後で。今は安全寄りで塞ぐ
            throw new \Cake\Http\Exception\NotFoundException();
        }

        $scanLogId = (int)$this->request->getQuery('scan_log_id');

        $scanLogs = $this->fetchTable('ScanLogs');
        $employees = $this->fetchTable('Employees');
        $employeeIdm = $this->fetchTable('EmployeeIdm');

        $scanLog = $scanLogs->get($scanLogId);
        $idm = (string)$scanLog->idm;

        // もし既に登録済みなら戻す（多重登録回避）
        $exists = $employeeIdm->find()
            ->where(['idm' => $idm])
            ->first();

        if ($exists) {
            return $this->redirect(['controller' => 'Attendance', 'action' => 'decide', '?' => ['scan_log_id' => $scanLogId]]);
        }

        // 全社員（いったん会社絞り込み無し）
        $employeeList = $employees->find()
            ->select(['id', 'employee_name'])
            ->where(['active_flag' => 1])
            ->orderAsc('employee_name')
            ->all()
            ->combine('id', 'employee_name')
            ->toArray();

        if ($this->request->is('post')) {
            $employeeId = (int)$this->request->getData('employee_id');

            if (!isset($employeeList[$employeeId])) {
                $this->Flash->error('社員を選択してください。');
            } else {
                $e = $employeeIdm->newEmptyEntity();
                $e->employee_id = $employeeId;
                $e->idm = $idm;
                $e->active_flag = 1;

                $employeeIdm->saveOrFail($e);

                // 登録後、出退勤ボタンへ戻す
                return $this->redirect([
                    'controller' => 'Attendance',
                    'action' => 'decide',
                    '?' => ['scan_log_id' => $scanLogId],
                ]);
            }
        }

        $this->set(compact('scanLogId', 'scanLog', 'idm', 'employeeList'));
    }
}
