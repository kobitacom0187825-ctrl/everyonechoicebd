<?php
require_once __DIR__.'/includes/bootstrap.php';
if(!sslcz_ready()){http_response_code(503);exit('SSLCommerz credentials are not configured.');}
$oid=(int)($_GET['order']??0);if(!$oid || (int)($_SESSION['pending_order_id']??0)!==$oid){http_response_code(403);exit('Order session not found.');}
$s=$pdo->prepare("SELECT * FROM orders WHERE id=? AND payment_method='sslcommerz'");$s->execute([$oid]);$o=$s->fetch();if(!$o){http_response_code(404);exit('Order not found.');}
$tran='ECB'.date('ymdHis').$oid.bin2hex(random_bytes(3));
$u=$pdo->prepare('UPDATE orders SET tran_id=? WHERE id=?');$u->execute([$tran,$oid]);
$base=rtrim(BASE_URL,'/');if($base===''){ $scheme=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$base=$scheme.'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']),'/');}
$data=[
'store_id'=>SSLCZ_STORE_ID,'store_passwd'=>SSLCZ_STORE_PASSWORD,'total_amount'=>number_format((float)$o['total'],2,'.',''),'currency'=>'BDT','tran_id'=>$tran,
'success_url'=>$base.'/payment-success.php','fail_url'=>$base.'/payment-fail.php','cancel_url'=>$base.'/payment-cancel.php','ipn_url'=>$base.'/payment-ipn.php',
'cus_name'=>$o['customer_name'],'cus_email'=>'customer@example.com','cus_add1'=>$o['address'],'cus_city'=>$o['delivery_zone']==='inside'?'Dhaka':'Bangladesh','cus_postcode'=>'1000','cus_country'=>'Bangladesh','cus_phone'=>$o['phone'],
'shipping_method'=>'NO','product_name'=>'Baby & Mom Care Products','product_category'=>'baby care','product_profile'=>'physical-goods',
'value_a'=>(string)$oid
];
try{$res=sslcz_post('/gwprocess/v4/api.php',$data);if(($res['status']??'')==='SUCCESS'&&!empty($res['GatewayPageURL'])){header('Location: '.$res['GatewayPageURL']);exit;}throw new RuntimeException($res['failedreason']??'Gateway session could not be created.');}
catch(Throwable $e){$pdo->prepare("UPDATE orders SET payment_status='failed',payment_response=? WHERE id=?")->execute([substr($e->getMessage(),0,2000),$oid]);http_response_code(502);echo 'Payment gateway error. Please contact the shop and quote order #'.(int)$oid.'.';}
