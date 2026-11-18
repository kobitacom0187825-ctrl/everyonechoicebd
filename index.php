<?php
require_once __DIR__.'/includes/bootstrap.php';
$page_title='Baby & Mom Care';
$q=trim($_GET['q']??''); $cat=(int)($_GET['cat']??0);
$sql='SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.is_active=1'; $args=[];
if($q!==''){$sql.=' AND (p.name LIKE ? OR p.description LIKE ?)';$args[]="%$q%";$args[]="%$q%";}
if($cat){$sql.=' AND p.category_id=?';$args[]=$cat;}
$sql.=' ORDER BY p.created_at DESC';$st=$pdo->prepare($sql);$st->execute($args);$products=$st->fetchAll();
$categories=$pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
require __DIR__.'/includes/header.php';
?>
<section class="hero"><div><div class="eyebrow">LITTLE THINGS, BIG LOVE</div><h1>আপনার সোনামণির জন্য<br>ভালোবাসার কেনাকাটা 💗</h1><p>শিশুর প্রয়োজনীয় পণ্য ও মা-বেবি কেয়ার আইটেম—সব এক জায়গায়। পছন্দের পণ্য বেছে নিন সহজেই।</p><a class="btn" href="#products">কেনাকাটা শুরু করুন</a></div><div class="hero-art">🧸 🍼 👶</div></section>
<div class="section-title"><h2>ক্যাটাগরি অনুযায়ী খুঁজুন</h2><p class="muted">আপনার প্রয়োজনীয় পণ্যটি বেছে নিন</p></div>
<div class="categories"><?php foreach($categories as $c): ?><a class="category" href="<?= e(url('index.php?cat='.$c['id'])) ?>"><span>🛍️</span><b><?= e($c['name']) ?></b></a><?php endforeach; ?></div>
<div class="section-title" id="products"><h2><?= $q?'সার্চের ফলাফল':'আমাদের পণ্য' ?></h2></div>
<div class="products"><?php foreach($products as $p): ?><article class="product"><a href="<?= e(url('product.php?id='.$p['id'])) ?>"><img src="<?= e(url($p['image']?:'assets/pacifier.jpg')) ?>" alt="<?= e($p['name']) ?>"></a><div class="product-info"><span class="muted"><?= e($p['category_name']??'Baby & Mom') ?></span><h3><a href="<?= e(url('product.php?id='.$p['id'])) ?>"><?= e($p['name']) ?></a></h3><div class="price"><?= $p['price']===null?'দাম জানতে যোগাযোগ করুন':money($p['price']) ?></div><a class="btn" href="<?= e(url('cart.php?action=add&id='.$p['id'])) ?>">কার্টে যোগ করুন</a></div></article><?php endforeach; ?></div>
<?php if(!$products): ?><p class="card">কোনো পণ্য পাওয়া যায়নি।</p><?php endif; ?>
<div class="grid2"><div class="card">🚚 <b>সারা বাংলাদেশে ডেলিভারি</b><p class="muted">ঢাকার মধ্যে ৳৭০, ঢাকার বাইরে ৳১২০।</p></div><div class="card">💬 <b>সহজ যোগাযোগ</b><p><a class="btn alt" href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>">WhatsApp-এ যোগাযোগ</a></p></div></div>
<?php require __DIR__.'/includes/footer.php'; ?>
