<?php
require_once __DIR__.'/includes/bootstrap.php';$items=get_cart($pdo);if(!$items){header('Location: '.url('cart.php'));exit;}
$page_title='Checkout';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
$name=trim($_POST['name']??'');$phone=trim($_POST['phone']??'');$address=trim($_POST['address']??'');$zone=$_POST['zone']??'';$note=trim($_POST['note']??'');
if(!$name||!preg_match('/^[0-9+\-\s]{10,16}$/',$phone)||!$address||!in_array($zone,['inside','outside'],true)){$error='সঠিক নাম, মোবাইল নম্বর, ঠিকানা ও ডেলিভারি এলাকা দিন।';}
else{
$fee=$zone==='inside'?70:120;$total=0;$lines=[];
foreach($items as $p){$line=$p['price']===null?null:(float)$p['price']*$p['quantity'];if($line!==null)$total+=$line;$lines[]=$p['name'].' × '.$p['quantity'];}
$grand=$total+$fee;$cust=customer();$pdo->beginTransaction();
try{$s=$pdo->prepare('INSERT INTO orders(customer_id,customer_name,phone,address,delivery_zone,delivery_fee,subtotal,total,note,status,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,NOW())');
$s->execute([$cust['id']??null,$name,$phone,$address,$zone,$fee,$total,$grand,$note,'pending']);$oid=(int)$pdo->lastInsertId();
$it=$pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,quantity,unit_price) VALUES(?,?,?,?,?)');
foreach($items as $p)$it->execute([$oid,$p['id'],$p['name'],$p['quantity'],$p['price']]);
$pdo->commit();unset($_SESSION['cart']);
$msg="নতুন অর্ডার #$oid - Everyone Choice BD\nনাম: $name\nফোন: $phone\nঠিকানা: $address\nডেলিভারি: ".($zone==='inside'?'ঢাকার মধ্যে':'ঢাকার বাইরে')."\nপণ্য: ".implode('; ',$lines)."\nডেলিভারি চার্জ: ৳$fee\nপণ্যের মূল্য অ্যাডমিন নিশ্চিত করবেন।\nনোট: $note";
$wa='https://wa.me/'.WHATSAPP_NUMBER.'?text='.rawurlencode($msg);header('Location: '.url('order-success.php?id='.$oid.'&wa='.urlencode($wa)));exit;
}catch(Throwable $e){$pdo->rollBack();$error='অর্ডার সংরক্ষণ করা যায়নি। আবার চেষ্টা করুন।';}
}}
require __DIR__.'/includes/header.php';?>
<h1>চেকআউট</h1><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<div class="grid2"><form method="post" class="card form"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><label>আপনার নাম<input name="name" required value="<?= e(customer()['name']??'') ?>"></label><label>মোবাইল নম্বর<input name="phone" required></label><label>সম্পূর্ণ ঠিকানা<textarea name="address" required></textarea></label><label>ডেলিভারি এলাকা<select name="zone" required><option value="">নির্বাচন করুন</option><option value="inside">ঢাকার মধ্যে — ৳৭০</option><option value="outside">ঢাকার বাইরে — ৳১২০</option></select></label><label>অতিরিক্ত নির্দেশনা<textarea name="note"></textarea></label><button class="btn">অর্ডার নিশ্চিত করুন</button></form>
<div class="card"><h3>অর্ডারের সারাংশ</h3><?php foreach($items as $p): ?><p><?= e($p['name']) ?> × <?= $p['quantity'] ?> — <?= $p['price']===null?'দাম নিশ্চিত হবে':money($p['line_total']) ?></p><?php endforeach; ?><hr><p>ডেলিভারি চার্জ: চেকআউটে এলাকা অনুযায়ী</p><p class="muted">পণ্যের দাম পরে WhatsApp/ফোনে নিশ্চিত করা হবে।</p></div></div>
<?php require __DIR__.'/includes/footer.php'; ?>
