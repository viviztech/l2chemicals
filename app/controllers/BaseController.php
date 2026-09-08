<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\SiteRepository;

abstract class BaseController
{
    protected SiteRepository $site;
    protected array $settings;

    public function __construct()
    {
        $this->site = new SiteRepository();
        $this->settings = $this->site->settings();
    }

    protected function render(string $view, array $data = []): void
    {
        view($view, array_merge(['settings' => $this->settings], $data));
    }

    protected function notFound(): never
    {
        http_response_code(404);
        $this->render('errors/404', ['title' => 'Page not found']);
        exit;
    }
}

