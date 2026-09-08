<footer class="footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4"><a class="footer-brand" href="<?= e(url()) ?>" aria-label="L2 Chemicals home"><img src="<?= e(asset('images/Logo-with-name-and-tagline.webp')) ?>" alt="L2 Chemicals"></a><p class="mt-3"><?= e($settings['footer_description'] ?? 'Reliable supply partner for thermoplastic resins and polymer additives.') ?></p></div>
            <div class="col-6 col-lg-2"><h2>Company</h2><a href="<?= e(url('about')) ?>">About Us</a><a href="<?= e(url('technical-support')) ?>">Technical Support</a><a href="<?= e(url('insights')) ?>">Insights</a><a href="<?= e(url('contact')) ?>">Contact</a></div>
            <div class="col-6 col-lg-2"><h2>Explore</h2><a href="<?= e(url('products')) ?>">Products</a><a href="<?= e(url('industries')) ?>">Industries</a><a href="<?= e(url('request-quote')) ?>">Request Quote</a><a href="<?= e(url('privacy-policy')) ?>">Privacy Policy</a></div>
            <div class="col-lg-4"><h2>Contact</h2><?php if (!empty($settings['address'])): ?><p><i class="fa-solid fa-location-dot"></i> <?= e($settings['address']) ?></p><?php endif; ?><?php if (!empty($settings['phone'])): ?><a href="tel:<?= e(preg_replace('/\s+/', '', $settings['phone'])) ?>"><i class="fa-solid fa-phone"></i> <?= e($settings['phone']) ?></a><?php endif; ?><?php if (!empty($settings['email'])): ?><a href="mailto:<?= e($settings['email']) ?>"><i class="fa-regular fa-envelope"></i> <?= e($settings['email']) ?></a><?php endif; ?></div>
        </div>
    </div>
    <div class="footer-bottom"><div class="container d-flex flex-wrap justify-content-between gap-2"><span><?= e($settings['copyright'] ?? ('© ' . date('Y') . ' L2 Chemicals. All rights reserved.')) ?></span><a href="<?= e(url('terms-conditions')) ?>">Terms & Conditions</a></div></div>
</footer>
<?php if (!empty($settings['whatsapp'])): $wa = preg_replace('/\D+/', '', $settings['whatsapp']); ?><a class="whatsapp-float" href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hello L2 Chemicals, I would like to discuss a product requirement.') ?>" target="_blank" rel="noopener" aria-label="Enquire on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a><?php endif; ?>
