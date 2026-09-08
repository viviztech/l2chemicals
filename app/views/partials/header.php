<?php $current = '/' . trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/'); ?>
<div class="topbar d-none d-lg-block">
    <div class="container d-flex justify-content-between">
        <span>Thermoplastic resins & polymer additive solutions</span>
        <div class="d-flex gap-4">
            <?php if (!empty($settings['phone'])): ?><a href="tel:<?= e(preg_replace('/\s+/', '', $settings['phone'])) ?>"><i class="fa-solid fa-phone"></i> <?= e($settings['phone']) ?></a><?php endif; ?>
            <?php if (!empty($settings['email'])): ?><a href="mailto:<?= e($settings['email']) ?>"><i class="fa-regular fa-envelope"></i> <?= e($settings['email']) ?></a><?php endif; ?>
        </div>
    </div>
</div>
<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="<?= e(url()) ?>" aria-label="L2 Chemicals home">
                <img src="<?= e(asset('images/Logo-with-name-and-tagline.webp')) ?>" alt="L2 Chemicals">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <?php foreach (['/' => 'Home', '/about' => 'About Us', '/products' => 'Products', '/industries' => 'Industries', '/technical-support' => 'Technical Support', '/insights' => 'Insights', '/contact' => 'Contact'] as $href => $label): ?>
                        <li class="nav-item"><a class="nav-link <?= $current === $href ? 'active' : '' ?>" href="<?= e(url($href)) ?>"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <a class="btn btn-primary ms-lg-3" href="<?= e(url('request-quote')) ?>">Request Quote <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </nav>
</header>
