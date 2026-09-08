<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? ($settings['default_seo_title'] ?? 'L2 Chemicals')) ?></title>
    <meta name="description" content="<?= e($description ?? ($settings['default_meta_description'] ?? '')) ?>">
    <link rel="canonical" href="<?= e(url(trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/'))) ?>">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
    <style>.honeypot{position:absolute!important;left:-9999px!important;opacity:0!important;pointer-events:none!important}</style>
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/header.php'; ?>
<main><?= $content ?></main>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</body>
</html>
