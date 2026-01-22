<?php

declare(strict_types=1);

namespace App\Controller;

class DashboardController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('kiosk');
    }

    public function index()
    {
        // 表示だけ。JSでポーリングする
    }
}
