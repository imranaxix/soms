<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

$mfg = App\Models\User::find(2);
$svc = new App\Services\JazzCashService();
$res = $svc->processWalletPayment(10000, 'T' . date('ymdHis') . 'TST', '03123456789', $mfg, '345678');
print_r($res);
