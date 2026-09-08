<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Models\AdminRepository;

abstract class AdminController
{
    protected AdminRepository $admin;
    public function __construct(){ Auth::requireLogin(); $this->admin=new AdminRepository(); }
    protected function render(string $view,array $data=[]): void { view('admin/'.$view,array_merge(['adminUser'=>Auth::user()],$data),'layouts/admin'); }
    protected function csrf(): void { if(!CSRF::verify($_POST['_token']??null)){http_response_code(419);exit('Your session expired. Please go back and try again.');} }
}

