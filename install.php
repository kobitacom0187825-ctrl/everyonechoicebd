<?php
require_once __DIR__.'/includes/bootstrap.php';
$done=false;$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
csrf_check();$username=trim($_POST['username']??'');$password=$_POST['password']??'';
if(strlen($username)<4||strlen($password)<12){$err='Admin username কমপক্ষে ৪ অক্ষর এবং password কমপক্ষে ১২ অক্ষরের হতে হবে।';}
else{
try{
$sql=file_get_contents(__DIR__.'/schema.sql');
$statements=array_filter(array_map('trim',preg_split('/;\\s*\\n/', $sql)));
foreach($statements as $stmt){if($stmt!=='')$pdo->exec($stmt);}
$hash=password_hash($password,PASSWORD_DEFAULT);
$s=$pdo->prepare('INSERT INTO admins(username,password_hash) VALUES(?,?)');$s->execute([$username,$hash]);
$seed=[
['Feeding','ফিডিং পণ্য'],['Baby Bottles','বেবি বোতল'],['Mom & Baby','মা ও শিশু'],['Baby Accessories','বেবি অ্যাক্সেসরিজ']
];
$ins=$pdo->prepare('INSERT IGNORE INTO categories(slug,name) VALUES(?,?)');foreach($seed as $v)$ins->execute($v);
$cat=$pdo->query('SELECT id,name FROM categories')->fetchAll();$map=[];foreach($cat as $c)$map[$c['name']]=$c['id'];
$seedProducts=[
['সিলিকন বেবি প্যাসিফায়ার','০–৩ মাসের জন্য প্যাকেজিংয়ে উল্লেখিত সিলিকন প্যাসিফায়ার। কেনার আগে বয়স-উপযোগিতা ও নিরাপত্তা নির্দেশনা যাচাই করুন।',null,$map['ফিডিং পণ্য']??null,'assets/pacifier.jpg'],
['কার্টুন ডিজাইনের বেবি ওয়াটার বোতল','ছবিতে প্রদর্শিত স্ট্রসহ কার্টুন ডিজাইনের বেবি বোতল। উপাদান, ধারণক্ষমতা ও ব্যবহারের বয়স বিক্রেতার কাছ থেকে নিশ্চিত করুন।',null,$map['বেবি বোতল']??null,'assets/baby-bottle.jpg'],
['ম্যানুয়াল ব্রেস্ট পাম্প','ছবিতে প্রদর্শিত ম্যানুয়াল ব্রেস্ট পাম্প। ব্যবহার, পরিষ্কার ও উপাদান-সংক্রান্ত নির্দেশনা পণ্য সরবরাহকারীর কাছ থেকে নিশ্চিত করুন।',null,$map['মা ও শিশু']??null,'assets/manual-breast-pump.jpg']
];
$ins=$pdo->prepare('INSERT INTO products(name,description,price,category_id,image,is_active,created_at) VALUES(?,?,?,?,?,1,NOW())');
foreach($seedProducts as $p)$ins->execute($p);
$settings=[['whatsapp','8801308725792'],['facebook','https://www.facebook.com/EveryoneChoiceBD'],['delivery_inside','70'],['delivery_outside','120']];
$ins=$pdo->prepare('INSERT IGNORE INTO settings(setting_key,setting_value) VALUES(?,?)');foreach($settings as $v)$ins->execute($v);
$done=true;
}catch(Throwable $e){$err='ইনস্টল সম্পন্ন হয়নি: '.$e->getMessage();}
}}
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="style.css"><title>Install Everyone Choice BD</title></head><body><main class="wrap"><div class="card" style="max-width:650px;margin:35px auto"><h1>Everyone Choice BD Setup</h1><?php if($done):?><div class="alert">Setup সম্পন্ন হয়েছে। নিরাপত্তার জন্য এখন install.php ফাইলটি সার্ভার থেকে মুছে ফেলুন। Admin login: <a href="admin/login.php">এখানে</a>।</div><?php else:?><?php if($err):?><div class="alert error"><?=e($err)?></div><?php endif;?><p>প্রথমে config.php-এ database credential দিন এবং schema.sql import করুন। এরপর initial admin তৈরি করুন।</p><form method="post" class="form"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input name="username" required placeholder="Admin username"><input type="password" name="password" minlength="12" required placeholder="Admin password (কমপক্ষে ১২ অক্ষর)"><button class="btn">Setup & Create Admin</button></form><?php endif;?></div></main></body></html>
