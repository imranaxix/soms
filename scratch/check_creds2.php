<?php
/**
 * This script uses the official JazzCash HashCalculator tool to brute force
 * find the correct hash. We know the pre-image from the oracle test:
 * 
 * The oracle test showed that when we hash the RESPONSE fields (returned by JazzCash
 * with exclude-empty), we get the EXACT response hash. This means JazzCash and us
 * are using the SAME algorithm. So the hash IS correct, but JazzCash is saying it's wrong.
 * 
 * THEORY: The credentials in the DB might be different from the sandbox portal!
 * The user might have regenerated the salt in the portal but it wasn't updated in DB.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

$mfg = App\Models\User::find(2);
echo "DB Merchant ID: " . $mfg->jazzcash_merchant_id . "\n";
echo "DB Password: " . $mfg->jazzcash_password . "\n";
echo "DB Integrity Salt: " . $mfg->jazzcash_integrity_salt . "\n";
echo "Email: " . $mfg->email . "\n";
echo "\n";
echo "Expected from portal:\n";
echo "Merchant ID: MC825731\n";
echo "Password: a02z77h1x1\n"; 
echo "Integrity Salt: 519zxxy265\n";
echo "\n";
echo "Match: " . (($mfg->jazzcash_merchant_id === 'MC825731' && $mfg->jazzcash_password === 'a02z77h1x1' && $mfg->jazzcash_integrity_salt === '519zxxy265') ? 'YES' : 'NO');
echo "\n";

// Also verify the DB encryption is consistent
echo "\nRaw DB jazzcash_integrity_salt: ";
$raw = \DB::table('users')->where('id', 2)->value('jazzcash_integrity_salt');
echo $raw . "\n";
