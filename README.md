# TOTP-PHP

![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-brightgreen) ![License](https://img.shields.io/badge/license-MIT-blue) [![Packagist Downloads](https://img.shields.io/packagist/dm/kayon-ariel/totp-php.svg?label=Packagist%20downloads)](https://packagist.org/packages/kayon-ariel/totp-php)

## Introduction

**TOTP-PHP** is a PHP library for generating Time-based One-Time Passwords (TOTP) for two-factor authentication. This library is easy to integrate into your existing PHP applications, allowing you to enhance your security measures effectively.

**TOTP-PHP** is compatible with **Google Authenticator** and other TOTP applications, making it a great choice for implementing two-factor authentication in your projects.

For a secure installation you have to make sure that used codes cannot be reused (replay-attack). You also need to limit the number of verifications, to fight against brute-force attacks. For example you could limit the amount of verifications to 10 tries within 10 minutes for one IP address (or IPv6 block). It depends on your environment.

## Features

- Generate TOTP codes compliant with RFC 6238.
- Simple for use.
- Built-in validation methods.
- Secret key generation for TOTP.
- Compatible with Google Authenticator and similar apps.
- Generate QR code payloads for easy integration with TOTP applications.

## Installation

You can install the `totp-php` library via Composer. Run the following command in your terminal:

```bash
composer require kayon-ariel/totp-php
```

## Usage

Here is a simple example of how to use the library:

### Generating a TOTP Code

```php
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
```

### Generating a Secret Key

You can generate a random secret key using the `createSecret` method. This is useful for initializing a new user or session.

```php
$secret = $ga->createSecret();
echo "Your secret key is: " . $secret;
```

### Generating a QR Code Payload

To generate a QR code payload for a TOTP secret, use the `getQrCodePayload` function:

```php
$label = 'user@example.com'; // The user's email or username
$issuer = 'MyApp'; // Optional issuer name

$qrcodePayload = $totp->getQrCodePayload($secret, $label, $issuer);
echo "QR Code Payload: " . $qrcodePayload . "\n";
```

### Validating a TOTP Code

You can validate a TOTP code using the `verifyCode` method:

```php
$userInputCode = '123456'; // Example user input
$isValid = $ga->verifyCode($secret, $userInputCode);

if ($isValid) {
    echo "The TOTP code is valid!";
} else {
    echo "The TOTP code is invalid!";
}
```

### Code Generation and Verification Logic

The library uses the following methods:

- **`createSecret(int $secretLength = 16): string`**: Generates a new secret key with a specified length (minimum 16 characters).
- **`getCode(string $secret, ?int $timeSlice = null): string`**: Calculates the TOTP code for a given secret key and time slice (defaults to the current time).
- **`verifyCode(string $secret, string $code, int $discrepancy = 1): bool`**: Checks if the provided TOTP code matches the expected code for the secret, allowing for some time discrepancy.

## License

This library incorporates code from the original `PHPGangsta/GoogleAuthenticator` project:

* Copyright (c) 2012-2016, [http://www.phpgangsta.de](http://www.phpgangsta.de)
* Author: Michael Kliewe, [@PHPGangsta](http://twitter.com/PHPGangsta) and [contributors](https://github.com/PHPGangsta/GoogleAuthenticator/graphs/contributors)
* Licensed under the BSD License.

Current: Copyright (c) 2024 Kayon Ariel, provided under the MIT License.

## Contributions

Contributions are welcome! Feel free to submit issues, fork the repository, and submit pull requests.

## Contact

For any inquiries or feedback, you can reach out to me at [kayonariel@gmail.com].