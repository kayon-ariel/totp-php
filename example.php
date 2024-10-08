<?php
require_once 'src/Totp.php';

$ga = new Totp();
$secret = $ga->createSecret();
echo "Secret is: ".$secret."\n\n";

$oneCode = $ga->getCode($secret);
echo "Checking Code '$oneCode' and Secret '$secret':\n";

$checkResult = $ga->verifyCode($secret, $oneCode);
if ($checkResult) {
    echo 'OK';
} else {
    echo 'FAILED';
}