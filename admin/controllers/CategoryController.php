<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Core\Auth;
use App\Core\Upload;
use PDOException;

final class CategoryController extends AdminController
{
    private const ROLES=['SUPER_ADMIN','ADMIN','CONTENT_MANAGER'];
    public function __construct(){parent::__construct();Auth::requireRoles(self::ROLES);}
    public function index():void{$this->render('categories/index',['title'=>'Categories','categories'=>$this->admin->categories()]);}
    public function create():void{$this->render('categories/form',['title'=>'Add Category','category'=>null]);}
    public function store():void{$this->save(null);}
    public function edit(string $id):void{$category=$this->admin->category((int)$id);if(!$category){flash('error','Category not found.');redirect('admin/categories');}$this->render('categories/form',['title'=>'Edit Category','category'=>$category]);}
    public function update(string $id):void{$this->save((int)$id);}
    public function delete(string $id):void{$this->csrf();try{$this->admin->deleteCategory((int)$id);flash('success','Category deleted.');}catch(PDOException){flash('error','This category contains products and cannot be deleted. Disable it or move its products first.');}redirect('admin/categories');}
    public function toggle(string $id):void{$this->csrf();$this->admin->toggleCategory((int)$id);flash('success','Category visibility updated.');redirect('admin/categories');}
    private function save(?int $id):void
    {
        $this->csrf();$existing=$id?$this->admin->category($id):null;$name=trim((string)($_POST['name']??''));if($name===''){flash('error','Category name is required.');redirect($id?'admin/categories/'.$id.'/edit':'admin/categories/add');}
        try{$image=Upload::image($_FILES['image']??[],'categories')??($existing['image']??null);}catch(\RuntimeException $e){flash('error',$e->getMessage());redirect($id?'admin/categories/'.$id.'/edit':'admin/categories/add');}
        $requestedSlug=trim((string)($_POST['slug']??''));
        $data=['name'=>$name,'slug'=>$this->admin->uniqueSlug('product_categories',slugify($requestedSlug!==''?$requestedSlug:$name),$id),'short_description'=>trim((string)($_POST['short_description']??'')),'description'=>trim((string)($_POST['description']??'')),'image'=>$image,'icon'=>trim((string)($_POST['icon']??'')),'sort_order'=>max(0,(int)($_POST['sort_order']??0)),'status'=>($_POST['status']??'DRAFT')==='PUBLISHED'?'PUBLISHED':'DRAFT'];
        try{$this->admin->saveCategory($data,$id);flash('success',$id?'Category updated.':'Category created.');redirect('admin/categories');}catch(\Throwable $e){error_log($e->__toString());flash('error','Unable to save the category.');redirect($id?'admin/categories/'.$id.'/edit':'admin/categories/add');}
    }
}
