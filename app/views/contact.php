<section class="page-hero">
    <div class="container">
        <span class="eyebrow eyebrow-light">Start a conversation</span>
        <h1>Contact Us</h1>
        <p><?= e($settings['contact_intro'] ?? 'Speak with our team about products, availability and application requirements.') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($success = flash_pull('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error = flash_pull('error')): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="eyebrow">L2 Chemicals</span>
                <h2>How can we help?</h2>
                <div class="contact-details">
                    <?php if (!empty($settings['address'])): ?>
                        <div><i class="fa-solid fa-location-dot"></i><span><strong>Office address</strong><?= e($settings['address']) ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($settings['warehouse_address'])): ?>
                        <div><i class="fa-solid fa-warehouse"></i><span><strong>Warehouse</strong><?= nl2br(e($settings['warehouse_address'])) ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($settings['phone'])): ?>
                        <div><i class="fa-solid fa-phone"></i><span><strong>Phone</strong><a href="tel:<?= e(preg_replace('/\s+/', '', $settings['phone'])) ?>"><?= e($settings['phone']) ?></a></span></div>
                    <?php endif; ?>
                    <?php if (!empty($settings['email'])): ?>
                        <div><i class="fa-solid fa-envelope"></i><span><strong>Email</strong><a href="mailto:<?= e($settings['email']) ?>"><?= e($settings['email']) ?></a></span></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-7">
                <form class="form-card" method="post" action="<?= e(url('contact')) ?>">
                    <h2>Send an enquiry</h2><?= csrf_field() ?>
                    <input class="honeypot" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <div class="row g-3">
                        <div class="col-md-6"><label for="name">Name *</label><input class="form-control" id="name" name="name" required maxlength="120"></div>
                        <div class="col-md-6"><label for="company">Company</label><input class="form-control" id="company" name="company" maxlength="160"></div>
                        <div class="col-md-6"><label for="mobile">Mobile *</label><input class="form-control" id="mobile" name="mobile" required maxlength="20"></div>
                        <div class="col-md-6"><label for="email">Email *</label><input class="form-control" type="email" id="email" name="email" required maxlength="190"></div>
                        <div class="col-12"><label for="subject">Subject *</label><input class="form-control" id="subject" name="subject" required maxlength="190"></div>
                        <div class="col-12"><label for="message">Message *</label><textarea class="form-control" id="message" name="message" rows="5" required maxlength="3000"></textarea></div>
                        <div class="col-12"><button class="btn btn-primary btn-lg" type="submit">Send Enquiry</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
