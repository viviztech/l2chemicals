<article class="product-card h-100">
    <a class="product-image" href="<?= e(url('product/' . $product['slug'])) ?>"><img src="<?= e(upload_url($product['main_image'])) ?>" alt="<?= e($product['name']) ?>" loading="lazy"></a>
    <div class="p-4"><span class="eyebrow"><?= e($product['category_name']) ?></span><h3><a href="<?= e(url('product/' . $product['slug'])) ?>"><?= e($product['name']) ?></a></h3><p><?= e($product['short_description']) ?></p><div class="d-flex flex-wrap gap-2"><a class="text-link" href="<?= e(url('product/' . $product['slug'])) ?>">View details <i class="fa-solid fa-arrow-right"></i></a><a class="btn btn-sm btn-outline-primary" href="<?= e(url('request-quote?product=' . urlencode($product['slug']))) ?>">Request quote</a></div></div>
</article>

