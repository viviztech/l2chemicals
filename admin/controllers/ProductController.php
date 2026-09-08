<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Core\Auth;
use App\Core\Upload;

final class ProductController extends AdminController
{
    private const ROLES=['SUPER_ADMIN','ADMIN','CONTENT_MANAGER'];
    public function __construct(){parent::__construct();Auth::requireRoles(self::ROLES);}
    public function index(): void{$search=trim((string)($_GET['q']??''));$this->render('products/index',['title'=>'Products','products'=>$this->admin->products($search),'search'=>$search]);}
    public function create(): void{$this->render('products/form',['title'=>'Add Product','product'=>null,'categories'=>$this->admin->categories(false)]);}
    public function store(): void{$this->csrf();$data=$this->validated();if(!$data)return;try{$this->admin->saveProduct($data);flash('success','Product created successfully.');redirect('admin/products');}catch(\Throwable $e){error_log($e->__toString());flash('error','Unable to create the product. Check that its code and slug are unique.');redirect('admin/products/add');}}
    public function edit(string $id): void{$product=$this->admin->product((int)$id);if(!$product){flash('error','Product not found.');redirect('admin/products');}$this->render('products/form',['title'=>'Edit Product','product'=>$product,'categories'=>$this->admin->categories()]);}
    public function update(string $id): void{$this->csrf();$product=$this->admin->product((int)$id);if(!$product){flash('error','Product not found.');redirect('admin/products');}$data=$this->validated($product,(int)$id);if(!$data)return;try{$this->admin->saveProduct($data,(int)$id);flash('success','Product updated successfully.');redirect('admin/products');}catch(\Throwable $e){error_log($e->__toString());flash('error','Unable to update the product. Check that its code and slug are unique.');redirect('admin/products/'.$id.'/edit');}}
    public function delete(string $id): void{$this->csrf();$this->admin->deleteProduct((int)$id);flash('success','Product deleted.');redirect('admin/products');}
    public function toggle(string $id): void{$this->csrf();$this->admin->toggleProduct((int)$id);flash('success','Product visibility updated.');redirect('admin/products');}

    private function validated(?array $existing=null,?int $id=null): ?array
    {
        $name=trim((string)($_POST['name']??''));$category=(int)($_POST['category_id']??0);$url=trim((string)($_POST['official_url']??''));
        if($name===''||$category<1){flash('error','Product name and category are required.');redirect($id?'admin/products/'.$id.'/edit':'admin/products/add');}
        if($url!==''&&filter_var($url,FILTER_VALIDATE_URL)===false){flash('error','Official website URL must be a valid absolute URL.');redirect($id?'admin/products/'.$id.'/edit':'admin/products/add');}
        try{$image=Upload::image($_FILES['main_image']??[],'products')??($existing['main_image']??null);}catch(\RuntimeException $e){flash('error',$e->getMessage());redirect($id?'admin/products/'.$id.'/edit':'admin/products/add');}
        $requestedSlug=trim((string)($_POST['slug']??''));
        $slug=$this->admin->uniqueSlug('products',slugify($requestedSlug!==''?$requestedSlug:$name),$id);
        return ['category_id'=>$category,'name'=>$name,'slug'=>$slug,'product_code'=>trim((string)($_POST['product_code']??''))?:null,'main_image'=>$image,'short_description'=>trim((string)($_POST['short_description']??'')),'full_description'=>trim((string)($_POST['full_description']??'')),'features'=>trim((string)($_POST['features']??'')),'benefits'=>trim((string)($_POST['benefits']??'')),'applications'=>trim((string)($_POST['applications']??'')),'technical_information'=>trim((string)($_POST['technical_information']??'')),'packaging_information'=>trim((string)($_POST['packaging_information']??'')),'official_url'=>$url?:null,'featured'=>isset($_POST['featured'])?1:0,'status'=>($_POST['status']??'DRAFT')==='PUBLISHED'?'PUBLISHED':'DRAFT'];
    }
}
