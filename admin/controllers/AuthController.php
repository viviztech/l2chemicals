<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Core\Auth;
use App\Core\CSRF;

final class AuthController
{
    public function login(): void { if(Auth::check())redirect('admin/dashboard'); view('admin/auth/login',['title'=>'Admin Login'],''); }
    public function authenticate(): void
    {
        if(!CSRF::verify($_POST['_token']??null)){flash('error','Your session expired. Please try again.');redirect('admin/login');}
        $identifier=trim((string)($_POST['identifier']??''));$password=(string)($_POST['password']??'');
        if($identifier===''||$password===''||!Auth::attempt($identifier,$password)){flash('error','Invalid credentials or too many attempts.');redirect('admin/login');}
        flash('success','Welcome back.');redirect('admin/dashboard');
    }
    public function logout(): void { if(!CSRF::verify($_POST['_token']??null)){http_response_code(419);exit('Invalid request.');} Auth::logout();redirect('admin/login'); }
}

