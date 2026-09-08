<section class="page-hero"><div class="container"><span class="eyebrow eyebrow-light">B2B product enquiry</span><h1>Request a Quote</h1><p>Tell us what you need and our team will follow up with the right product and supply information.</p></div></section>
<section class="section section-muted"><div class="container">
<?php if($success=flash_pull('success')):?><div class="alert alert-success form-wide"><?=e($success)?></div><?php endif;?>
<?php if($error=flash_pull('error')):?><div class="alert alert-danger form-wide"><?=e($error)?></div><?php endif;?>
<form class="form-card form-wide" method="post" action="<?= e(url('request-quote')) ?>"><?= csrf_field() ?><input class="d-none" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
<div class="row g-4">
<div class="col-md-6"><label for="full_name">Full Name *</label><input class="form-control" id="full_name" name="full_name" required maxlength="120"></div>
<div class="col-md-6"><label for="company_name">Company Name *</label><input class="form-control" id="company_name" name="company_name" required maxlength="160"></div>
<div class="col-md-6"><label for="phone">Phone *</label><input class="form-control" id="phone" name="phone" required maxlength="20"></div>
<div class="col-md-6"><label for="whatsapp">WhatsApp</label><input class="form-control" id="whatsapp" name="whatsapp" maxlength="20"></div>
<div class="col-md-6"><label for="email">Email *</label><input class="form-control" type="email" id="email" name="email" required maxlength="190"></div>
<div class="col-md-3"><label for="city">City *</label><input class="form-control" id="city" name="city" required maxlength="100"></div>
<div class="col-md-3"><label for="state">State *</label><input class="form-control" id="state" name="state" required maxlength="100"></div>
<div class="col-md-6"><label for="category_id">Product Category</label><select class="form-select" id="category_id" name="category_id"><option value="">Select category</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label for="product_name">Product / Material</label><input class="form-control" id="product_name" name="product_name" maxlength="180"></div>
<div class="col-md-6"><label for="quantity">Quantity Requirement</label><input class="form-control" id="quantity" name="quantity_requirement" maxlength="120"></div>
<div class="col-md-6"><label for="application">Application</label><input class="form-control" id="application" name="application" maxlength="255"></div>
<div class="col-md-6"><label for="preferred_contact">Preferred Contact Method</label><select class="form-select" id="preferred_contact" name="preferred_contact_method"><option value="PHONE">Phone</option><option value="WHATSAPP">WhatsApp</option><option value="EMAIL">Email</option></select></div>
<div class="col-12"><label for="message">Message</label><textarea class="form-control" id="message" name="message" rows="5" maxlength="4000"></textarea></div>
<div class="col-12"><button class="btn btn-primary btn-lg" type="submit">Submit Request</button><p class="form-note">Your enquiry will be securely recorded and assigned a reference number.</p></div>
</div></form></div></section>
