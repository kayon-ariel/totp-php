<?php

require 'vendor/autoload.php';

use TotpPhp\Totp;

// Create a new TOTP instance
$ga = new Totp();

// Generate a secret key
$secret = $ga->createSecret();
echo "Secret is: " . $secret . "\n\n";

// Generate a TOTP code based on the secret
$oneCode = $ga->getCode($secret);
echo "Checking Code '$oneCode' and Secret '$secret':\n";

// Verify the TOTP code against the secret
$checkResult = $ga->verifyCode($secret, $oneCode);

if ($checkResult) {
    echo 'OK';
} else {
    echo 'FAILED';
}
