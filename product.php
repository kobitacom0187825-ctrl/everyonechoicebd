<?php
require_once __DIR__.'/includes/bootstrap.php'; $id=(int)($_GET['id']??0);
$s=$pdo->prepare('SELECT p.*,c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.is_active=1');$s->execute([$id]);$p=$s->fetch();
if(!$p){http_response_code(404);exit('পণ্য পাওয়া যায়নি');}$page_title=$p['name'];require __DIR__.'/includes/header.php';
?>
<div class="card grid2"><div><img style="width:100%;max-height:440px;object-fit:contain;background:#fff8fa;border-radius:14px" src="<?= e(url($p['image'])) ?>" alt="<?= e($p['name']) ?>"></div><div><p class="muted"><?= e($p['category_name']??'Baby & Mom') ?></p><h1><?= e($p['name']) ?></h1><h2 class="price"><?= $p['price']===null?'দাম জানতে যোগাযোগ করুন':money($p['price']) ?></h2><p><?= nl2br(e($p['description'])) ?></p><p class="muted">ডেলিভারি: ঢাকার মধ্যে ৳৭০, ঢাকার বাইরে ৳১২০।</p><a class="btn" href="<?= e(url('cart.php?action=add&id='.$p['id'])) ?>">কার্টে যোগ করুন</a></div></div>
<?php require __DIR__.'/includes/footer.php'; ?>
