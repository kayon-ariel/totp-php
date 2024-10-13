<?php

use PHPUnit\Framework\TestCase;
use TotpPhp\Base32;

class Base32Test extends TestCase
{
    protected $base32;

    protected function setUp(): void
    {
        $this->base32 = new Base32();
    }

    public function testBase32Decode()
    {
        $encoded = 'JBSWY3DP';
        $decoded = $this->base32->_base32Decode($encoded);

        $this->assertNotFalse($decoded);
        $this->assertEquals('Hello', $decoded);
    }

    public function testBase32DecodeInvalid()
    {
        $invalidEncoded = 'INVALIDBAS2251STRING';
        $decoded = $this->base32->_base32Decode($invalidEncoded);

        $this->assertFalse($decoded, 'Base32 decoding of an invalid string should return false.');
    }


    public function testGetBase32LookupTable()
    {
        $lookupTable = $this->base32->_getBase32LookupTable();
        $this->assertIsArray($lookupTable);
        $this->assertCount(33, $lookupTable);
    }
}
