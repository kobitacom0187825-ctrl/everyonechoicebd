<?php
require_once __DIR__.'/includes/bootstrap.php';
function handle_ssl_payment(PDO $pdo,array $payload): array {
 $tran=trim((string)($payload['tran_id']??''));$val=trim((string)($payload['val_id']??''));
 if($tran===''||$val==='')return ['ok'=>false,'message'=>'Missing transaction details'];
 $s=$pdo->prepare("SELECT * FROM orders WHERE tran_id=? AND payment_method='sslcommerz'");$s->execute([$tran]);$o=$s->fetch();
 if(!$o)return ['ok'=>false,'message'=>'Order not found'];
 if($o['payment_status']==='paid')return ['ok'=>true,'order_id'=>(int)$o['id'],'message'=>'Already verified'];
 $v=sslcz_validate($val);
 $valid= in_array(($v['status']??''),['VALID','VALIDATED'],true)
   && strtoupper((string)($v['currency']??''))==='BDT'
   && hash_equals($tran,(string)($v['tran_id']??''))
   && abs((float)($v['amount']??0)-(float)$o['total'])<0.01
   && abs((float)($payload['amount']??$v['amount'])-(float)$o['total'])<0.01;
 if(!$valid){$pdo->prepare("UPDATE orders SET payment_status='failed',payment_response=? WHERE id=?")->execute([json_encode($v),$o['id']]);return ['ok'=>false,'order_id'=>(int)$o['id'],'message'=>'Payment validation failed'];}
 $risk=(int)($v['risk_level']??0);$paymentStatus=$risk===1?'review':'paid';$orderStatus=$risk===1?'payment_review':'confirmed';
 $channel=(string)($v['card_type']??'SSLCommerz');$gatewayId=(string)($v['bank_tran_id']??'');
 $up=$pdo->prepare('UPDATE orders SET payment_status=?,status=?,val_id=?,gateway_transaction_id=?,payment_channel=?,payment_response=? WHERE id=?');
 $up->execute([$paymentStatus,$orderStatus,$val,$gatewayId,$channel,json_encode($v,JSON_UNESCAPED_UNICODE),$o['id']]);
 return ['ok'=>true,'order_id'=>(int)$o['id'],'message'=>$risk===1?'Payment received; manual review required':'Payment verified'];
}
