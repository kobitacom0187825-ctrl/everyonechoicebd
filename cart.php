<?php
require_once __DIR__.'/includes/bootstrap.php';
if(isset($_GET['action'],$_GET['id']) && $_GET['action']==='add'){ $id=(int)$_GET['id'];$s=$pdo->prepare('SELECT id FROM products WHERE id=? AND is_active=1');$s->execute([$id]);if($s->fetch()){$_SESSION['cart'][$id]=($_SESSION['cart'][$id]??0)+1;flash('পণ্যটি কার্টে যোগ হয়েছে।');}header('Location: '.url('cart.php'));exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$id=(int)($_POST['id']??0);$qty=(int)($_POST['qty']??1);if($qty<=0)unset($_SESSION['cart'][$id]);else $_SESSION['cart'][$id]=min(50,$qty);header('Location: '.url('cart.php'));exit;}
$page_title='Shopping Cart';$items=get_cart($pdo);require __DIR__.'/includes/header.php';
?>
<h1>আপনার শপিং কার্ট</h1>
<?php if(!$items): ?><div class="card">আপনার কার্ট খালি। <a class="btn" href="<?= e(url('index.php')) ?>">কেনাকাটা করুন</a></div><?php else: ?>
<div class="table-wrap card"><table><tr><th>পণ্য</th><th>দাম</th><th>পরিমাণ</th><th></th></tr>
<?php foreach($items as $p): ?><tr><td><?= e($p['name']) ?></td><td><?= $p['price']===null?'দাম জানতে হবে':money($p['price']) ?></td><td><form method="post" class="inline"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $p['id'] ?>"><input style="width:70px" type="number" min="0" max="50" name="qty" value="<?= $p['quantity'] ?>"><button class="btn alt">আপডেট</button></form></td><td><?= $p['price']===null?'':money($p['line_total']) ?></td></tr><?php endforeach; ?></table></div>
<a class="btn" href="<?= e(url('checkout.php')) ?>">চেকআউট করুন</a><?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>
