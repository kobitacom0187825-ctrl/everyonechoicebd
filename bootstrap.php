<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'secure'=>$secure]);
    session_start();
}
require_once __DIR__ . '/../config.php';
$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
);
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($n): string { return CURRENCY . number_format((float)$n, 0); }
function url(string $path=''): string { return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/'); }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); exit('Invalid form token. Please go back and try again.');
    }
}
function customer(): ?array { return $_SESSION['customer'] ?? null; }
function admin(): ?array { return $_SESSION['admin'] ?? null; }
function require_customer(): void { if (!customer()) { header('Location: '.url('login.php')); exit; } }
function require_admin(): void { if (!admin()) { header('Location: '.url('admin/login.php')); exit; } }
function setting(PDO $pdo, string $key, string $default=''): string {
    $q=$pdo->prepare('SELECT setting_value FROM settings WHERE setting_key=?'); $q->execute([$key]); $v=$q->fetchColumn();
    return $v===false ? $default : (string)$v;
}
function cart_count(): int { return array_sum($_SESSION['cart'] ?? []); }
function get_cart(PDO $pdo): array {
    $cart=$_SESSION['cart'] ?? []; if (!$cart) return [];
    $ids=array_values(array_filter(array_map('intval', array_keys($cart))));
    if (!$ids) return [];
    $in=implode(',', array_fill(0,count($ids),'?'));
    $q=$pdo->prepare("SELECT * FROM products WHERE is_active=1 AND id IN ($in)");
    $q->execute($ids); $rows=$q->fetchAll();
    foreach($rows as &$r){$r['quantity']=(int)($cart[$r['id']]??0); $r['line_total']=(float)$r['price']*$r['quantity'];}
    return $rows;
}
function flash(string $msg, string $type='success'): void { $_SESSION['flash']=['msg'=>$msg,'type'=>$type]; }
function show_flash(): void { if(!empty($_SESSION['flash'])) { $f=$_SESSION['flash']; unset($_SESSION['flash']); echo '<div class="alert '.e($f['type']).'">'.e($f['msg']).'</div>'; } }
