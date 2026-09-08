<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Core\Auth;

final class ContentController extends AdminController
{
    public function __construct(){parent::__construct();Auth::requireRoles(['SUPER_ADMIN','ADMIN','CONTENT_MANAGER']);}
    public function index():void{$this->render('content/index',['title'=>'Website Content','settings'=>$this->admin->settings(['homepage','content','contact','footer']),'pages'=>$this->admin->editablePages()]);}
    public function update():void
    {
        $this->csrf();$allowed=array_column($this->admin->settings(['homepage','content','contact','footer']),'setting_key');$values=[];
        foreach($allowed as $key)$values[$key]=trim((string)($_POST['settings'][$key]??''));$this->admin->updateSettings($values);
        foreach($_POST['pages']??[] as $id=>$page)$this->admin->updatePage((int)$id,trim((string)($page['excerpt']??'')),trim((string)($page['content']??'')));
        flash('success','Website content updated.');redirect('admin/content');
    }
}

