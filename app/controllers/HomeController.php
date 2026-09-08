<?php

declare(strict_types=1);

namespace App\Controllers;

final class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home', [
            'title' => $this->settings['default_seo_title'] ?? 'L2 Chemicals | Thermoplastic Resins & Polymer Additives',
            'description' => $this->settings['default_meta_description'] ?? '',
            'categories' => $this->site->categories(8),
            'products' => $this->site->products(6, true),
            'industries' => $this->site->industries(12),
            'posts' => $this->site->posts(3),
        ]);
    }
}

