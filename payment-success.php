<?php
require_once __DIR__.'/payment-handler.php';
try{$r=handle_ssl_payment($pdo,$_POST+$_GET);$id=(int)($r['order_id']??0);if($r['ok']){unset($_SESSION['cart'],$_SESSION['pending_order_id']);header('Location: '.url('payment-result.php?id='.$id.'&result=success'));exit;}header('Location: '.url('payment-result.php?id='.$id.'&result=failed'));exit;}
catch(Throwable $e){http_response_code(500);echo 'Payment verification is pending. Please contact the shop with your order number.';}
