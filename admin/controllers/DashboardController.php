<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

final class DashboardController extends AdminController
{
    public function index(): void { $this->render('dashboard',['title'=>'Dashboard','stats'=>$this->admin->dashboard()]); }
}

