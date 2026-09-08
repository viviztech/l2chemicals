<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\PublicController;
use App\Admin\Controllers\AuthController as AdminAuthController;
use App\Admin\Controllers\CategoryController as AdminCategoryController;
use App\Admin\Controllers\ContentController as AdminContentController;
use App\Admin\Controllers\DashboardController as AdminDashboardController;
use App\Admin\Controllers\EnquiryController as AdminEnquiryController;
use App\Admin\Controllers\ProductController as AdminProductController;
use App\Core\Router;

require __DIR__ . '/app/bootstrap.php';

$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [PublicController::class, 'about']);
$router->get('/products', [PublicController::class, 'products']);
$router->get('/products/{slug}', [PublicController::class, 'productCategory']);
$router->get('/product/{slug}', [PublicController::class, 'product']);
$router->get('/industries', [PublicController::class, 'industries']);
$router->get('/industries/{slug}', [PublicController::class, 'industry']);
$router->get('/technical-support', [PublicController::class, 'technicalSupport']);
$router->get('/insights', [PublicController::class, 'insights']);
$router->get('/insights/{slug}', [PublicController::class, 'article']);
$router->get('/contact', [PublicController::class, 'contact']);
$router->get('/request-quote', [PublicController::class, 'requestQuote']);
$router->post('/request-quote', [PublicController::class, 'submitQuote']);
$router->post('/contact', [PublicController::class, 'submitContact']);
$router->get('/privacy-policy', [PublicController::class, 'page']);
$router->get('/terms-conditions', [PublicController::class, 'page']);

$router->get('/admin', [AdminDashboardController::class, 'index']);
$router->get('/admin/login', [AdminAuthController::class, 'login']);
$router->post('/admin/login', [AdminAuthController::class, 'authenticate']);
$router->post('/admin/logout', [AdminAuthController::class, 'logout']);
$router->get('/admin/dashboard', [AdminDashboardController::class, 'index']);
$router->get('/admin/products', [AdminProductController::class, 'index']);
$router->get('/admin/products/add', [AdminProductController::class, 'create']);
$router->post('/admin/products/add', [AdminProductController::class, 'store']);
$router->get('/admin/products/{id}/edit', [AdminProductController::class, 'edit']);
$router->post('/admin/products/{id}/edit', [AdminProductController::class, 'update']);
$router->post('/admin/products/{id}/delete', [AdminProductController::class, 'delete']);
$router->post('/admin/products/{id}/toggle', [AdminProductController::class, 'toggle']);
$router->get('/admin/categories', [AdminCategoryController::class, 'index']);
$router->get('/admin/categories/add', [AdminCategoryController::class, 'create']);
$router->post('/admin/categories/add', [AdminCategoryController::class, 'store']);
$router->get('/admin/categories/{id}/edit', [AdminCategoryController::class, 'edit']);
$router->post('/admin/categories/{id}/edit', [AdminCategoryController::class, 'update']);
$router->post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'delete']);
$router->post('/admin/categories/{id}/toggle', [AdminCategoryController::class, 'toggle']);
$router->get('/admin/content', [AdminContentController::class, 'index']);
$router->post('/admin/content', [AdminContentController::class, 'update']);
$router->get('/admin/enquiries', [AdminEnquiryController::class, 'index']);
$router->get('/admin/enquiries/{id}', [AdminEnquiryController::class, 'show']);
$router->post('/admin/enquiries/{id}/mark', [AdminEnquiryController::class, 'mark']);
$router->post('/admin/enquiries/{id}/status', [AdminEnquiryController::class, 'status']);
$router->post('/admin/enquiries/{id}/delete', [AdminEnquiryController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
