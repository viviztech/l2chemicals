<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Validator;
use App\Models\EnquiryRepository;

final class PublicController extends BaseController
{
    public function about(): void { $this->pageBySlug('about'); }
    public function technicalSupport(): void { $this->pageBySlug('technical-support'); }
    public function contact(): void { $this->render('contact', ['title' => 'Contact L2 Chemicals']); }
    public function requestQuote(): void { $this->render('request-quote', ['title' => 'Request a Quote', 'categories' => $this->site->categories()]); }

    public function submitContact(): void
    {
        if (!CSRF::verify($_POST['_token'] ?? null)) { flash('error','Your session expired. Please try again.'); redirect('contact'); }
        if (trim((string)($_POST['website']??'')) !== '') { flash('success','Thank you. Your enquiry has been received.'); redirect('contact'); }
        $data=['name'=>trim((string)($_POST['name']??'')),'company'=>trim((string)($_POST['company']??''))?:null,'mobile'=>trim((string)($_POST['mobile']??'')),'email'=>trim((string)($_POST['email']??'')),'subject'=>trim((string)($_POST['subject']??'')),'message'=>trim((string)($_POST['message']??'')),'ip_hash'=>client_ip_hash()];
        if(!Validator::required($data['name'])||!Validator::phone($data['mobile'])||!Validator::email($data['email'])||!Validator::required($data['subject'])||!Validator::required($data['message'])){flash('error','Please complete all required fields with valid contact details.');redirect('contact');}
        (new EnquiryRepository())->createContact($data);flash('success','Thank you. Your enquiry has been received.');redirect('contact');
    }

    public function submitQuote(): void
    {
        if (!CSRF::verify($_POST['_token'] ?? null)) { flash('error','Your session expired. Please try again.'); redirect('request-quote'); }
        if (trim((string)($_POST['website']??'')) !== '') { flash('success','Thank you. Your request has been received.'); redirect('request-quote'); }
        $method=in_array($_POST['preferred_contact_method']??'', ['PHONE','WHATSAPP','EMAIL'],true)?$_POST['preferred_contact_method']:'PHONE';
        $data=['full_name'=>trim((string)($_POST['full_name']??'')),'company_name'=>trim((string)($_POST['company_name']??'')),'phone'=>trim((string)($_POST['phone']??'')),'whatsapp'=>trim((string)($_POST['whatsapp']??''))?:null,'email'=>trim((string)($_POST['email']??'')),'city'=>trim((string)($_POST['city']??'')),'state'=>trim((string)($_POST['state']??'')),'category_id'=>(int)($_POST['category_id']??0)?:null,'product_name'=>trim((string)($_POST['product_name']??''))?:null,'quantity_requirement'=>trim((string)($_POST['quantity_requirement']??''))?:null,'application'=>trim((string)($_POST['application']??''))?:null,'message'=>trim((string)($_POST['message']??''))?:null,'preferred_contact_method'=>$method,'source_url'=>substr((string)($_SERVER['HTTP_REFERER']??url('request-quote')),0,500),'ip_hash'=>client_ip_hash()];
        if(!Validator::required($data['full_name'])||!Validator::required($data['company_name'])||!Validator::phone($data['phone'])||!Validator::email($data['email'])||!Validator::required($data['city'])||!Validator::required($data['state'])){flash('error','Please complete all required fields with valid contact details.');redirect('request-quote');}
        $reference=(new EnquiryRepository())->createRfq($data);flash('success','Your request has been saved. Reference: '.$reference);redirect('request-quote');
    }

    public function products(): void
    {
        $this->render('products/index', ['title' => 'Products', 'categories' => $this->site->categories(), 'products' => $this->site->products()]);
    }

    public function productCategory(string $slug): void
    {
        $category = $this->site->category($slug);
        if (!$category) $this->notFound();
        $this->render('products/category', ['title' => $category['seo_title'] ?: $category['name'], 'category' => $category, 'products' => $this->site->products(null, false, (int) $category['id'])]);
    }

    public function product(string $slug): void
    {
        $product = $this->site->product($slug);
        if (!$product) $this->notFound();
        $this->render('products/show', ['title' => $product['seo_title'] ?: $product['name'], 'description' => $product['seo_description'], 'product' => $product]);
    }

    public function industries(): void { $this->render('industries/index', ['title' => 'Industries We Serve', 'industries' => $this->site->industries()]); }

    public function industry(string $slug): void
    {
        $industry = $this->site->industry($slug);
        if (!$industry) $this->notFound();
        $this->render('industries/show', ['title' => $industry['seo_title'] ?: $industry['name'], 'description' => $industry['seo_description'], 'industry' => $industry]);
    }

    public function insights(): void { $this->render('insights/index', ['title' => 'Technical Insights', 'posts' => $this->site->posts()]); }

    public function article(string $slug): void
    {
        $post = $this->site->post($slug);
        if (!$post) $this->notFound();
        $this->render('insights/show', ['title' => $post['seo_title'] ?: $post['title'], 'description' => $post['seo_description'], 'post' => $post]);
    }

    public function page(): void
    {
        $slug = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
        $this->pageBySlug($slug);
    }

    private function pageBySlug(string $slug): void
    {
        $page = $this->site->page($slug);
        if (!$page) $this->notFound();
        $this->render('page', ['title' => $page['seo_title'] ?: $page['title'], 'description' => $page['seo_description'], 'page' => $page]);
    }
}
