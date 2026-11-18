<?php
require_once __DIR__.'/payment-handler.php';
try{$r=handle_ssl_payment($pdo,$_POST);http_response_code($r['ok']?200:400);echo $r['ok']?'OK':'INVALID';}
catch(Throwable $e){http_response_code(500);echo 'ERROR';}
