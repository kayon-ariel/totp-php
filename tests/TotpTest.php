<?php

use PHPUnit\Framework\TestCase;
use TotpPhp\Base32;
use TotpPhp\Totp;

class TotpTest extends TestCase
{
    protected $totp;
    protected $base32;

    protected function setUp(): void
    {
        $this->totp = new Totp();
        $this->base32 = new Base32();
    }

    public function testCreateSecretDefaultLength()
    {
        $secret = $this->totp->createSecret();
        $this->assertIsString($secret);
        $this->assertEquals(16, strlen($secret));  // Default length should be 16
    }

    public function testCreateSecretCustomLength()
    {
        $secretLength = 32;
        $secret = $this->totp->createSecret($secretLength);
        $this->assertIsString($secret);
        $this->assertEquals($secretLength, strlen($secret));  // Custom length
    }

    public function testCreateSecretInvalidLengthThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->totp->createSecret(10);  // Invalid length less than 16
    }

    public function testGetQrCodePayload()
    {
        $secret = $this->totp->createSecret();
        $label = 'user@example.com';
        $issuer = 'MyApp';

        $payload = $this->totp->getQrCodePayload($secret, $label, $issuer);

        $this->assertStringStartsWith('otpauth://totp/', $payload);
        $this->assertStringContainsString('totp/' . $label, $payload);
        $this->assertStringContainsString('issuer=' . $issuer, $payload);
        $this->assertStringContainsString('secret=' . $secret, $payload);
    }

    public function testGetCode()
    {
        $secret = $this->totp->createSecret();
        $code = $this->totp->getCode($secret);
        $this->assertIsString($code);
        $this->assertEquals(6, strlen($code));  // Code length should be 6
    }

    public function testVerifyCodeCorrect()
    {
        $secret = $this->totp->createSecret();
        $code = $this->totp->getCode($secret);
        $isValid = $this->totp->verifyCode($secret, $code);
        $this->assertTrue($isValid);  // Code should be valid
    }

    public function testVerifyCodeIncorrect()
    {
        $secret = $this->totp->createSecret();
        $invalidCode = '123456';  // Invalid code
        $isValid = $this->totp->verifyCode($secret, $invalidCode);
        $this->assertFalse($isValid);  // Code should be invalid
    }

    public function testTimingSafeEquals()
    {
        $safeString = 'abcdef';
        $userString = 'abcdef';
        $result = $this->invokeMethod($this->totp, 'timingSafeEquals', [$safeString, $userString]);
        $this->assertTrue($result);  // Strings are identical

        $userString = 'abcxyz';
        $result = $this->invokeMethod($this->totp, 'timingSafeEquals', [$safeString, $userString]);
        $this->assertFalse($result);  // Strings are different
    }

    /**
     * Helper function to invoke private/protected methods for testing.
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
