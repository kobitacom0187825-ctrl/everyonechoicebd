<?php require_once __DIR__.'/bootstrap.php'; ?>
<!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#f8dce5"><title><?= e($page_title ?? SITE_NAME) ?> | <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= e(url('style.css')) ?>"></head><body>
<div class="topbar">সারা বাংলাদেশে ডেলিভারি • শিশুর যত্নে আপনার বিশ্বস্ত শপ</div>
<header class="site-header"><div class="nav wrap">
<a class="brand" href="<?= e(url('index.php')) ?>">Everyone Choice BD<small>BABY & MOM CARE</small></a>
<form class="search" action="<?= e(url('index.php')) ?>" method="get"><input name="q" value="<?= e($_GET['q']??'') ?>" placeholder="পণ্য খুঁজুন..."><button>খুঁজুন</button></form>
<nav><a href="<?= e(url('index.php')) ?>">হোম</a><a href="<?= e(url('cart.php')) ?>">কার্ট (<?= cart_count() ?>)</a>
<?php if(customer()): ?><a href="<?= e(url('account.php')) ?>">আমার অ্যাকাউন্ট</a><a href="<?= e(url('logout.php')) ?>">লগআউট</a><?php else: ?><a href="<?= e(url('login.php')) ?>">লগইন</a><?php endif; ?></nav>
</div></header><main class="wrap"><?php show_flash(); ?>
